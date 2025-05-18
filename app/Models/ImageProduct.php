<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{
    protected $fillable = [
        'image',
        'product_id',
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
