<?php

namespace App\Http\Controllers\V1\Merchant\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $data = ProductCategory::paginate();

        return ProductCategoryResource::collection($data);
    }
}
