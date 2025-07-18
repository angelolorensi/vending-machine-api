<?php

namespace Tests\Unit;

use App\Services\ProductService;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Slot;
use App\Models\Machine;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    public function test_can_get_product_by_id()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Coca Cola',
            'product_category_id' => $category->product_category_id,
            'price_points' => 150
        ]);

        // Act
        $result = $this->productService->getProductById($product->product_id);

        // Assert
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertEquals('Coca Cola', $result->name);
        $this->assertEquals(150, $result->price_points);
        $this->assertTrue($result->relationLoaded('productCategory'));
        $this->assertTrue($result->relationLoaded('slots'));
    }

    public function test_throws_exception_when_product_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product not found');

        $this->productService->getProductById(999);
    }

    public function test_can_create_product()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $productData = [
            'name' => 'Pepsi',
            'product_category_id' => $category->product_category_id,
            'price_points' => 120,
            'description' => 'Refreshing cola drink'
        ];

        // Act
        $result = $this->productService->createProduct($productData);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Pepsi', $result->name);
        $this->assertEquals($category->product_category_id, $result->product_category_id);
        $this->assertEquals(120, $result->price_points);
        $this->assertEquals('Refreshing cola drink', $result->description);

        $this->assertDatabaseHas('products', [
            'name' => 'Pepsi',
            'product_category_id' => $category->product_category_id,
            'price_points' => 120,
            'description' => 'Refreshing cola drink'
        ]);
    }

    public function test_can_update_product()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'product_category_id' => $category->product_category_id,
            'price_points' => 100
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'price_points' => 150
        ];

        // Act
        $result = $this->productService->updateProduct($product->product_id, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(150, $result->price_points);

        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'name' => 'Updated Name',
            'price_points' => 150
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_product()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product not found');

        $this->productService->updateProduct(999, ['name' => 'Updated']);
    }

    public function test_can_delete_product()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->product_category_id
        ]);

        // Act
        $result = $this->productService->deleteProduct($product->product_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', [
            'product_id' => $product->product_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_product()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Product not found');

        $this->productService->deleteProduct(999);
    }

    public function test_get_product_by_id_loads_category_relationship()
    {
        // Arrange
        $category = ProductCategory::factory()->create(['name' => 'Beverages']);
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'product_category_id' => $category->product_category_id
        ]);

        // Act
        $result = $this->productService->getProductById($product->product_id);

        // Assert
        $this->assertNotNull($result->productCategory);
        $this->assertEquals('Beverages', $result->productCategory->name);
        $this->assertEquals($category->product_category_id, $result->productCategory->product_category_id);
    }

    public function test_get_product_by_id_loads_slots_relationship()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $category->product_category_id
        ]);
        $machine = Machine::factory()->create();
        $slot1 = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 1
        ]);
        $slot2 = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 2
        ]);

        // Act
        $result = $this->productService->getProductById($product->product_id);

        // Assert
        $this->assertCount(2, $result->slots);
        $this->assertEquals($slot1->slot_id, $result->slots->first()->slot_id);
        $this->assertEquals($slot2->slot_id, $result->slots->last()->slot_id);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'product_category_id' => $category->product_category_id,
            'price_points' => 100
        ]);

        // Act
        $result = $this->productService->updateProduct(
            $product->product_id,
            ['name' => 'Updated Name', 'price_points' => 200]
        );

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(200, $result->price_points);

        // Verify original instance wasn't modified
        $this->assertEquals('Original Name', $product->name);
        $this->assertEquals(100, $product->price_points);

        // Verify database was updated
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'name' => 'Updated Name',
            'price_points' => 200
        ]);
    }

    public function test_can_update_product_category()
    {
        // Arrange
        $oldCategory = ProductCategory::factory()->create();
        $newCategory = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'product_category_id' => $oldCategory->product_category_id
        ]);

        // Act
        $result = $this->productService->updateProduct($product->product_id, [
            'product_category_id' => $newCategory->product_category_id
        ]);

        // Assert
        $this->assertEquals($newCategory->product_category_id, $result->product_category_id);
        $this->assertDatabaseHas('products', [
            'product_id' => $product->product_id,
            'product_category_id' => $newCategory->product_category_id
        ]);
    }

    public function test_create_product_with_minimal_data()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $productData = [
            'name' => 'Minimal Product',
            'product_category_id' => $category->product_category_id,
            'description' => 'A product with minimal data',
            'price_points' => 50
        ];

        // Act
        $result = $this->productService->createProduct($productData);

        // Assert
        $this->assertNotNull($result->product_id);
        $this->assertEquals('Minimal Product', $result->name);
        $this->assertEquals($category->product_category_id, $result->product_category_id);
    }

    public function test_can_update_single_field()
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'product_category_id' => $category->product_category_id,
            'price_points' => 100
        ]);

        // Act
        $result = $this->productService->updateProduct(
            $product->product_id,
            ['price_points' => 250]
        );

        // Assert
        $this->assertEquals('Original Name', $result->name);
        $this->assertEquals(250, $result->price_points);
    }
}
