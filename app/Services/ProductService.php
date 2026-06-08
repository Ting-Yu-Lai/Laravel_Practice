<?php

namespace App\Services;
use App\Repositories\ProductRepository;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected ProductRepository $productRepository
    ){}

    public function getAllProducts() {
        return $this->productRepository->getAll();
    }
}
