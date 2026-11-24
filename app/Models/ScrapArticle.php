<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScrapArticle extends Model
{
    protected $fillable = ['title', 'slug', 'summary', 'content', 'cover_image', 'is_active', 'sort'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        // The slug is created once and then stays the same, so shared / indexed links never break
        static::creating(function (self $a) {
            if (empty($a->slug)) {
                $base = Str::slug($a->title) ?: 'article';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $a->slug = $slug;
            }
        });
    }

    public function images()
    {
        return $this->hasMany(ScrapArticleImage::class)->orderBy('sort')->orderBy('id');
    }

    /** Short plain-text description for search results and social previews. */
    public function metaDescription(int $limit = 158): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($this->summary ?: $this->content))));

        return Str::limit($text, $limit, '...');
    }
}
