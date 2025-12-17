<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Purchase;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'brand',
        'description',
        'price',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        // すでにURL（S3など）の場合
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // storage（publicディスク）の場合
        return Storage::url($this->image);
    }

    // リレーション

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
