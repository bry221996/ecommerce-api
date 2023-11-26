<?php

namespace Tests\Feature\V1\Merchant\Admin\Product\ProductCategoryController;

use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function merchant_admin_get_product_categories(): void
    {
        $merchant = Merchant::factory()->create();

        Sanctum::actingAs(
            User::factory()
                ->create(['merchant_id' => $merchant->id])
        );

        ProductCategory::factory()
            ->count($count = $this->faker()->numberBetween(1, 10))
            ->create(['merchant_id' => $merchant->id]);

        $this->getJson("/api/v1/merchant/$merchant->id/admin/product/categories")
            ->assertSuccessful()
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['total' => $count])
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'parent_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta',
                'links'
            ]);
    }

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function guess_cannot_get_product_categories()
    {
        $merchant = Merchant::factory()->create();

        $this->getJson("/api/v1/merchant/$merchant->id/admin/product/categories")
            ->assertUnauthorized();
    }

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function non_merchant_admin_cannot_get_product_categories()
    {
        $merchant = Merchant::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/v1/merchant/$merchant->id/admin/product/categories")
            ->assertForbidden();
    }
}
