<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatus;
use App\Models\Card;
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
        $cards = Card::all();

        $classifications = Classification::whereIn('name', ['Manager', 'Senior Employee', 'Regular Employee', 'Intern'])
            ->get()
            ->keyBy('name');

        $manager = $classifications['Manager'];
        $senior = $classifications['Senior Employee'];
        $regular = $classifications['Regular Employee'];
        $intern = $classifications['Intern'];

        $employees = [
            ['name' => 'John Smith', 'classification_id' => $manager->classification_id, 'status' => EmployeeStatus::ACTIVE, 'card_id' => $cards[0]->card_id],
            ['name' => 'Sarah Johnson', 'classification_id' => $senior->classification_id, 'status' => EmployeeStatus::ACTIVE, 'card_id' => $cards[1]->card_id],
            ['name' => 'Mike Wilson', 'classification_id' => $regular->classification_id, 'status' => EmployeeStatus::ACTIVE, 'card_id' => $cards[2]->card_id],
            ['name' => 'Lisa Brown', 'classification_id' => $regular->classification_id, 'status' => EmployeeStatus::ACTIVE, 'card_id' => $cards[3]->card_id],
            ['name' => 'David Lee', 'classification_id' => $intern->classification_id, 'status' => EmployeeStatus::ACTIVE, 'card_id' => $cards[4]->card_id],
            ['name' => 'Emma Davis', 'classification_id' => $regular->classification_id, 'status' => EmployeeStatus::INACTIVE, 'card_id' => $cards[5]->card_id]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
