<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // リレーション
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
