<?php

namespace Tests\Unit;

use App\Services\ClassificationService;
use App\Models\Classification;
use App\Models\Employee;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ClassificationService $classificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classificationService = new ClassificationService();
    }

    public function test_can_get_classification_by_id()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'name' => 'Manager',
            'daily_point_limit' => 100
        ]);

        // Act
        $result = $this->classificationService->getClassificationById($classification->classification_id);

        // Assert
        $this->assertEquals($classification->classification_id, $result->classification_id);
        $this->assertEquals('Manager', $result->name);
        $this->assertEquals(100, $result->daily_point_limit);
        $this->assertTrue($result->relationLoaded('employees'));
    }

    public function test_throws_exception_when_classification_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Classification not found');

        $this->classificationService->getClassificationById(999);
    }

    public function test_can_create_classification()
    {
        // Arrange
        $classificationData = [
            'name' => 'Senior Developer',
            'daily_juice_limit' => 3,
            'daily_meal_limit' => 2,
            'daily_snack_limit' => 5,
            'daily_point_limit' => 80,
            'daily_point_recharge_amount' => 20
        ];

        // Act
        $result = $this->classificationService->createClassification($classificationData);

        // Assert
        $this->assertInstanceOf(Classification::class, $result);
        $this->assertEquals('Senior Developer', $result->name);
        $this->assertEquals(3, $result->daily_juice_limit);
        $this->assertEquals(2, $result->daily_meal_limit);
        $this->assertEquals(5, $result->daily_snack_limit);
        $this->assertEquals(80, $result->daily_point_limit);
        $this->assertEquals(20, $result->daily_point_recharge_amount);

        $this->assertDatabaseHas('classifications', [
            'name' => 'Senior Developer',
            'daily_juice_limit' => 3,
            'daily_meal_limit' => 2,
            'daily_snack_limit' => 5,
            'daily_point_limit' => 80,
            'daily_point_recharge_amount' => 20
        ]);
    }

    public function test_can_update_classification()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'name' => 'Junior Developer',
            'daily_point_limit' => 30
        ]);

        $updateData = [
            'name' => 'Mid-Level Developer',
            'daily_point_limit' => 50
        ];

        // Act
        $result = $this->classificationService->updateClassification($classification->classification_id, $updateData);

        // Assert
        $this->assertEquals('Mid-Level Developer', $result->name);
        $this->assertEquals(50, $result->daily_point_limit);

        $this->assertDatabaseHas('classifications', [
            'classification_id' => $classification->classification_id,
            'name' => 'Mid-Level Developer',
            'daily_point_limit' => 50
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_classification()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Classification not found');

        $this->classificationService->updateClassification(999, ['name' => 'Updated']);
    }

    public function test_can_delete_classification()
    {
        // Arrange
        $classification = Classification::factory()->create();

        // Act
        $result = $this->classificationService->deleteClassification($classification->classification_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('classifications', [
            'classification_id' => $classification->classification_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_classification()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Classification not found');

        $this->classificationService->deleteClassification(999);
    }

    public function test_get_classification_by_id_loads_employees_relationship()
    {
        // Arrange
        $classification = Classification::factory()->create(['name' => 'Test Manager']);
        $employee1 = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'name' => 'John Doe'
        ]);
        $employee2 = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'name' => 'Jane Smith'
        ]);

        // Act
        $result = $this->classificationService->getClassificationById($classification->classification_id);

        // Assert
        $this->assertCount(2, $result->employees);
        $this->assertEquals('John Doe', $result->employees->first()->name);
        $this->assertEquals('Jane Smith', $result->employees->last()->name);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'name' => 'Original Name',
            'daily_point_limit' => 30
        ]);

        // Act
        $result = $this->classificationService->updateClassification(
            $classification->classification_id,
            ['name' => 'Updated Name', 'daily_point_limit' => 60]
        );

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(60, $result->daily_point_limit);

        // Verify original instance wasn't modified
        $this->assertEquals('Original Name', $classification->name);
        $this->assertEquals(30, $classification->daily_point_limit);

        // Verify database was updated
        $this->assertDatabaseHas('classifications', [
            'classification_id' => $classification->classification_id,
            'name' => 'Updated Name',
            'daily_point_limit' => 60
        ]);
    }

    public function test_create_classification_with_all_fields()
    {
        // Arrange
        $data = [
            'name' => 'Executive',
            'daily_juice_limit' => 5,
            'daily_meal_limit' => 3,
            'daily_snack_limit' => 8,
            'daily_point_limit' => 150,
            'daily_point_recharge_amount' => 50
        ];

        // Act
        $result = $this->classificationService->createClassification($data);

        // Assert
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $result->$key);
        }
    }
}
