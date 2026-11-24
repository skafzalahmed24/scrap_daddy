<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapArticleImage extends Model
{
    protected $fillable = ['scrap_article_id', 'image', 'sort'];
}
