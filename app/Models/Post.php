<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_ko',
        'title_cn',
        'description_en',
        'description_ko',
        'description_cn',
        'text_html',
        'text_en',
        'text_ko',
        'text_cn',
        'thumbs_up',
        'thumbs_down',
        'view_count',
        'is_view',
        'url_ori',
        'pid',
        'author_ori',
        'date_ori',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function images(){
        return $this->hasMany(Image::class);
    }
}
