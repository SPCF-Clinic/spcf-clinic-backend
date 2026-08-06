<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormFieldType;

class FormFieldTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formFieldTypes = [
            ['name' => 'Divider', 'is_answerable' => false, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'Short Text', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'Long Text', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'Checkbox', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => true],
            ['name' => 'Dropdown', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => false],
            ['name' => 'Date', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'Radio', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => false],
        ];

        foreach ($formFieldTypes as $type) {
            FormFieldType::create($type);
        }
    }
}
