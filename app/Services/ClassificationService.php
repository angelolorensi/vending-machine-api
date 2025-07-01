<?php

namespace App\Services;

use App\Models\Classification;
use App\Exceptions\NotFoundException;
use App\Filters\ClassificationFilter;

class ClassificationService
{
    public function getClassificationById(int $id): Classification
    {
        $classification = Classification::with('employees')->find($id);

        if (!$classification) {
            throw new NotFoundException('Classification not found');
        }

        return $classification;
    }

    public function createClassification(array $data): Classification
    {
        return Classification::create($data);
    }

    public function updateClassification(int $id, array $data): Classification
    {
        $classification = Classification::find($id);

        if (!$classification) {
            throw new NotFoundException('Classification not found');
        }

        $classification->update($data);
        return $classification->fresh();
    }

    public function deleteClassification(int $id): bool
    {
        $classification = Classification::find($id);

        if (!$classification) {
            throw new NotFoundException('Classification not found');
        }

        return $classification->delete();
    }
}
