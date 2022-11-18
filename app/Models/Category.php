<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'cate_name_en',
        'cate_name_ko',
        'cate_name_cn',
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }
}
