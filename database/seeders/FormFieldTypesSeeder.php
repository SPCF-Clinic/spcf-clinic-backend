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
            ['name' => 'divider', 'is_answerable' => false, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'short_text', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'long_text', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'checkbox', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => true],
            ['name' => 'dropdown', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => false],
            ['name' => 'date', 'is_answerable' => true, 'has_options' => false, 'can_select_multiple' => false],
            ['name' => 'radio', 'is_answerable' => true, 'has_options' => true, 'can_select_multiple' => false],
        ];

        foreach ($formFieldTypes as $type) {
            FormFieldType::create($type);
        }
    }
}
