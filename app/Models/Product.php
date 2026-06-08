<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // 建立白名單
    protected $fillable = ['name', 'price'];
}
