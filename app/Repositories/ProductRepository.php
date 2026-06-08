<?php

namespace App\Repositories;
use App\Repositories\Repository;
use App\Models\Product;

class ProductRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(        
        protected Product $product
    ){}
    public function getAll() {
        return $this->product->all();
    }
}
