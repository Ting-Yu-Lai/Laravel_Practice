<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    //
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index() {
        
        $data = $this->productService->getAllProducts();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $data
        ]);
    }
}
