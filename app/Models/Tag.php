<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_name_en',
        'tag_name_ko',
        'tag_name_cn',
    ];

    public function posts(){
        return $this->belongsToMany(Post::class);
    }

}
