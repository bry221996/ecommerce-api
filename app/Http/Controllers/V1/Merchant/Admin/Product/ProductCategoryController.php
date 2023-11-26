<?php

namespace App\Http\Controllers\V1\Merchant\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\ProductCategory;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;
use App\Http\Resources\ProductCategoryResource;

class ProductCategoryController extends Controller
{
    /**
     * List merchant product categories.
     *
     * @param Request $request
     * @param Merchant $merchant
     * @return JsonResource
     */
    public function index(Request $request, Merchant $merchant): JsonResource
    {
        $data = ProductCategory::where('merchant_id', $merchant->id)
            ->paginate();

        return ProductCategoryResource::collection($data);
    }

    /**
     * Store merchant product category.
     *
     * @param Request $request
     * @param Merchant $merchant
     * @return JsonResource
     */
    public function store(Request $request, Merchant $merchant): JsonResource
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'parent_id' => [
                'sometimes',
                Rule::exists('product_categories', 'id')
                    ->where(function (Builder $query) use ($merchant) {
                        return $query->where('merchant_id', $merchant->id);
                    })
            ]
        ]);

        $category = ProductCategory::create([
            ...$request->only(['name', 'parent_id']),
            'merchant_id' => $merchant->id
        ]);

        return ProductCategoryResource::make($category);
    }
}
