<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'phone_alt',
        'payment_method',
        'user_id',
    ];

    public function products(){
        return $this->belongsToMany(Product::class,'product_order');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
