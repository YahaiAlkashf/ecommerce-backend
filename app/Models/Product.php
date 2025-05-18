<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'rating',
        'main_image',
        'category_id',
        'price'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function orders(){
        return $this->belongsToMany(Order::class,'product_order');
    }
    public function ImagesProducts(){
        return $this->hasMany(ImageProduct::class);
    }
}
