<?php

namespace Tests\Feature\V1\Merchant\Admin\Product\ProductCategoryController;

use App\Models\Merchant;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function merchant_admin_can_create_product_category(): void
    {
        $merchant = Merchant::factory()->create();

        Sanctum::actingAs(
            User::factory()
                ->create(['merchant_id' => $merchant->id])
        );

        $data = ProductCategory::factory()
            ->make(['merchant_id' => $merchant->id])
            ->toArray();

        $this->postJson("/api/v1/merchants/$merchant->id/admin/product/categories", $data)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name', 'created_at', 'updated_at']]);

        $this->assertDatabaseHas('product_categories', $data);
    }

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function merchant_admin_can_create_product_sub_category(): void
    {
        $merchant = Merchant::factory()->create();

        Sanctum::actingAs(
            User::factory()
                ->create(['merchant_id' => $merchant->id])
        );

        $parentCategory = ProductCategory::factory()
            ->create(['merchant_id' => $merchant->id]);

        $data = ProductCategory::factory()
            ->make(['merchant_id' => $merchant->id, 'parent_id' => $parentCategory->id])
            ->toArray();

        $this->postJson("/api/v1/merchants/$merchant->id/admin/product/categories", $data)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name', 'created_at', 'updated_at']]);

        $this->assertDatabaseHas('product_categories', $data);
    }

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function guess_cannot_create_product_categories()
    {
        $merchant = Merchant::factory()->create();

        $this->postJson("/api/v1/merchants/$merchant->id/admin/product/categories")
            ->assertUnauthorized();
    }

    /**
     * @test
     * @group merchant
     * @group merchant.admin
     * @group merchant.admin.product.category
     */
    public function non_merchant_admin_cannot_create_product_categories()
    {
        $merchant = Merchant::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/v1/merchants/$merchant->id/admin/product/categories")
            ->assertForbidden();
    }
}
