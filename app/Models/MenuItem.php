<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'name', 'price', 'description', 'category', 'image'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
