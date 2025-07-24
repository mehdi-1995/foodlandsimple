<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'name', 'type', 'category', 'rating', 'reviews_count', 'delivery_cost', 'delivery_time', 'image'];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
