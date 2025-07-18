<?php

namespace Tests\Unit;

use App\Services\ProductCategoryService;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductCategoryService $productCategoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productCategoryService = new ProductCategoryService();
    }

    public function test_can_get_product_category_by_id()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create([
            'name' => 'Beverages'
        ]);

        // Act
        $result = $this->productCategoryService->getProductCategoryById($productCategory->product_category_id);

        // Assert
        $this->assertEquals($productCategory->product_category_id, $result->product_category_id);
        $this->assertEquals('Beverages', $result->name);
        $this->assertTrue($result->relationLoaded('products'));
    }

    public function test_throws_exception_when_product_category_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product category not found');

        $this->productCategoryService->getProductCategoryById(999);
    }

    public function test_can_create_product_category()
    {
        // Arrange
        $productCategoryData = [
            'name' => 'Snacks'
        ];

        // Act
        $result = $this->productCategoryService->createProductCategory($productCategoryData);

        // Assert
        $this->assertInstanceOf(ProductCategory::class, $result);
        $this->assertEquals('Snacks', $result->name);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Snacks'
        ]);
    }

    public function test_can_update_product_category()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create([
            'name' => 'Original Name'
        ]);

        $updateData = [
            'name' => 'Updated Name'
        ];

        // Act
        $result = $this->productCategoryService->updateProductCategory($productCategory->product_category_id, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $result->name);

        $this->assertDatabaseHas('product_categories', [
            'product_category_id' => $productCategory->product_category_id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_product_category()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product category not found');

        $this->productCategoryService->updateProductCategory(999, ['name' => 'Updated']);
    }

    public function test_can_delete_product_category()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create();

        // Act
        $result = $this->productCategoryService->deleteProductCategory($productCategory->product_category_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('product_categories', [
            'product_category_id' => $productCategory->product_category_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_product_category()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product category not found');

        $this->productCategoryService->deleteProductCategory(999);
    }

    public function test_get_product_category_by_id_loads_products_relationship()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create(['name' => 'Drinks']);
        $product1 = Product::factory()->create([
            'product_category_id' => $productCategory->product_category_id,
            'name' => 'Coca Cola'
        ]);
        $product2 = Product::factory()->create([
            'product_category_id' => $productCategory->product_category_id,
            'name' => 'Pepsi'
        ]);

        // Act
        $result = $this->productCategoryService->getProductCategoryById($productCategory->product_category_id);

        // Assert
        $this->assertCount(2, $result->products);
        $this->assertEquals('Coca Cola', $result->products->first()->name);
        $this->assertEquals('Pepsi', $result->products->last()->name);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create([
            'name' => 'Original Name'
        ]);

        // Act
        $result = $this->productCategoryService->updateProductCategory(
            $productCategory->product_category_id,
            ['name' => 'Updated Name']
        );

        // Assert
        $this->assertEquals('Updated Name', $result->name);

        // Verify original instance wasn't modified
        $this->assertEquals('Original Name', $productCategory->name);

        // Verify database was updated
        $this->assertDatabaseHas('product_categories', [
            'product_category_id' => $productCategory->product_category_id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_create_product_category_with_minimal_data()
    {
        // Arrange
        $productCategoryData = [
            'name' => 'Minimal Category'
        ];

        // Act
        $result = $this->productCategoryService->createProductCategory($productCategoryData);

        // Assert
        $this->assertNotNull($result->product_category_id);
        $this->assertEquals('Minimal Category', $result->name);
    }

    public function test_can_update_single_field()
    {
        // Arrange
        $productCategory = ProductCategory::factory()->create([
            'name' => 'Original Name'
        ]);

        // Act
        $result = $this->productCategoryService->updateProductCategory(
            $productCategory->product_category_id,
            ['name' => 'Updated Name']
        );

        // Assert
        $this->assertEquals('Updated Name', $result->name);
    }
}