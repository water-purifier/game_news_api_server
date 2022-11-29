<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::select('id','title_cn','title_en','description_cn','text_cn','author_ori','date_ori','view_count','thumbs_up','thumbs_down','created_at')
            ->where('title_en','<>','','and')
            ->where('title_cn','<>','','and')
            ->orderBy('created_at', 'desc')
            ->with(['user:name,user_id,id,email', 'comments', 'images:image_url', 'tags:tag_name_en'])
            ->paginate(10);
        return response()->json(
            $posts
        );
    }

    public function index_limit(Request $request)
    {
        $start = $request->query('start');
        $limit = $request->query('limit');
        if(!$start) $start=0;
        if(!$limit) $limit=10;
        $posts = Post::select('id','title_cn','title_en','description_cn','text_cn','author_ori','date_ori','view_count','thumbs_up','thumbs_down','created_at')
            ->where('title_en','<>','','and')
            ->where('title_cn','<>','','and')
            ->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($limit)
            ->with(['user:name,user_id,id,email', 'comments', 'images', 'tags'])
            ->get();
        return response()->json(
            $posts
        );
    }

    public function index_sitemap(Request $request)
    {
        $limit = $request->query('limit');
        if(!$limit) $limit=1000;
//        $posts = Post::select('id')
//            ->where('title_en','<>','','and')
//            ->where('title_cn','<>','','and')
//            ->orderBy('created_at', 'desc')
//            ->take($limit)
//            ->with(['user:name,user_id,id,email', 'comments', 'images', 'tags'])
//            ->get();
        $posts = DB::select('select id,updated_at from posts where title_cn <> "" and title_en <> "" order by created_at desc limit ? ',[$limit]);
        return response()->json(
            $posts
        );
    }

    public function index_paths(){
        $posts = Post::select('id','title_en')
            ->where('title_en','<>','','and')
            ->where('title_cn','<>','','and')
            ->orderBy('created_at','desc')
            ->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $params = $request->only([
            'page_url',
            'page_title_en',
            'page_title_ko',
            'page_title_cn',
            'page_description_en',
            'page_description_ko',
            'page_description_cn',
            'page_author',
            'page_date',
            'page_pid',
            'page_text_html',
            'page_text_en',
            'page_text_ko',
            'page_text_cn',
            'page_tags',
            'page_images',
        ]);

        $post = Post::where('pid',$params['page_pid'])->first();
        if($post){
            return response()->json(['message'=>'record exists'],202);
        }

        $post = Post::Create(
            [
                'title_en' => $params['page_title_en'],
                'title_ko' => $params['page_title_ko'],
                'title_cn' => $params['page_title_cn'],
                'description_en' => $params['page_description_en'],
                'description_ko' => $params['page_description_ko'],
                'description_cn' => $params['page_description_cn'],
                'author_ori' => $params['page_author'],
                'date_ori' => $params['page_date'],
                'url_ori' => $params['page_url'],
                'pid' => $params['page_pid'],
                'text_html' => $params['page_text_html'],
                'text_en' => $params['page_text_en'],
                'text_ko' => $params['page_text_ko'],
                'text_cn' => $params['page_text_cn'],
            ]
        );


        foreach ($params['page_images'] as $page_image) {
            $image = new Image();
            $image->image_url = $page_image;
            $image->post_id = $post->id;
            $image->save();
        }

        // 1. Tag insert ==> return $ids
        $ids = [];
        foreach ($params['page_tags'] as $page_tag) {
            $tag = Tag::updateOrCreate(
                ['tag_name_en' => $page_tag],['tag_name_en']
            );
//            $tag = new Tag();
//            $tag->tag_name_en = $page_tag;
//            $tag->save();
            array_push($ids, $tag->id);
        }

        // 2. post_tag sync : $post->tags()->sync(ids)
        $post->tags()->sync($ids);

        return response()->json(
            $post
        );
    }

    public function show($id)
    {
        //
        $post = Post::select('id','title_cn','title_en','description_cn','text_cn','author_ori','date_ori','view_count','thumbs_up','thumbs_down','url_ori','created_at')
            ->where('id', $id)
            ->with(['user:name,user_id,id,email', 'comments', 'images', 'tags'])
            ->first();
        if (!$post) {
            return response()->json(
                ['message' => 'no data'],
                404
            );
        } else {
            // 방문시 해당 글 조횟수 +1.
            DB::update('update posts set view_count=view_count+1 where id = ?', [$post->id]);
            return response()->json(
                $post
            );
        }

    }

    public function tag(Request $request)
    {
        $params = $request->only(['tag_name']);
        $tag_name = $params['tag_name'];
        if (!$tag_name) {
            return response()->json(['message' => 'no data'], 404);
        }
        $posts = DB::select('select * from posts as post,post_tag as posttag,tags as tag where post.id=posttag.post_id and posttag.tag_id=tag.id and tag.tag_name_en = ?',
            [$tag_name]);
        return response()->json(
            $posts
        );
    }

    public function search(Request $request)
    {
        //title_cn,text_cn , author_ori
        // # matched ==> #tag
        $params = $request->only(['keywords']);
        if (!$params || !$params['keywords']) {
            return response()->json(
                ['message' => 'no data'],
                404
            );
        } else {
            if (str_contains($params['keywords'], '#')) {
                #tags
                $keywords = '%' . str_replace('#', '', $params['keywords']) . '%';
                $posts = DB::select('select * from posts as post,post_tag as posttag,tags as tag where post.id=posttag.post_id and posttag.tag_id=tag.id and tag.tag_name_en like ?', [$keywords]);
                return response()->json(
                    $posts
                );
            } else {
                $keywords = '%' . $params['keywords'] . '%';
                $posts_title = DB::select('select * from posts where title_cn like ?', [$keywords]);
                $posts_text = DB::select('select * from posts where text_cn like ?', [$keywords]);
                $posts_description = DB::select('select * from posts where description_cn like ?', [$keywords]);
                $posts_author = DB::select('select * from posts where author_ori like ?', [$keywords]);
                return response()->json(
                    array_merge($posts_title, $posts_text, $posts_description, $posts_author)
                );
            }
        }

    }

    // 이부분 아이디 한개당, 한개글에 한번씩 적용가능하게 하고 풀어야 함. @issu
    public function thumbs_up(Request $request, $id)
    {
        $post = Post::where('id', $id)->first();
        if ($post) {
            DB::update('update posts set thumbs_up=thumbs_up+1 where id=?', [$post->id]);
            return response()->json(['message', 'thumbs_up ok!'], 200);
        } else {
            return response()->json(['message', 'thumbs_up faild!'], 403);
        }
    }

    // 이부분 아이디 한개당, 한개글에 한번씩 적용가능하게 하고 풀어야 함. @issu
    public function thumbs_down(Request $request, $id)
    {
        $post = Post::where('id', $id)->first();
        if ($post) {
            DB::update('update posts set thumbs_down=thumbs_down+1 where id=?', [$post->id]);
            return response()->json(['message', 'thumbs_down ok!'], 200);
        } else {
            return response()->json(['message', 'thumbs_down faild!'], 403);
        }
    }

}
