<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatus;
use App\Models\Classification;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = Classification::whereIn('name', ['Manager', 'Senior Employee', 'Regular Employee', 'Intern'])
            ->get()
            ->keyBy('name');

        $manager = $classifications['Manager'];
        $senior = $classifications['Senior Employee'];
        $regular = $classifications['Regular Employee'];
        $intern = $classifications['Intern'];

        $employees = [
            ['name' => 'John Smith', 'card_number' => 'EMP001', 'classification_id' => $manager->getKey(), 'status' => EmployeeStatus::ACTIVE],
            ['name' => 'Sarah Johnson', 'card_number' => 'EMP002', 'classification_id' => $senior->getKey(), 'status' => EmployeeStatus::ACTIVE],
            ['name' => 'Mike Wilson', 'card_number' => 'EMP003', 'classification_id' => $regular->getKey(), 'status' => EmployeeStatus::ACTIVE],
            ['name' => 'Lisa Brown', 'card_number' => 'EMP004', 'classification_id' => $regular->getKey(), 'status' => EmployeeStatus::ACTIVE],
            ['name' => 'David Lee', 'card_number' => 'EMP005', 'classification_id' => $intern->getKey(), 'status' => EmployeeStatus::ACTIVE],
            ['name' => 'Emma Davis', 'card_number' => 'EMP006', 'classification_id' => $regular->getKey(), 'status' => EmployeeStatus::INACTIVE],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
