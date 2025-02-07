<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'media', 'status', 'published_at', 'meta_title', 'meta_description', 'meta_keywords', 'author_id',
    ];
    
    // Automatically generate a slug when creating a new page
    public static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            $page->slug = Str::slug($page->title);
        });
    }

    // Relationship: A page belongs to an author (admin)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
