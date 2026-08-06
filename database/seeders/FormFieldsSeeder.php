<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    PersonalInfoField,
    PersonalInfoFieldOption,
    PersonalInfoFieldVersion,
    MedicalHistoryField,
    MedicalHistoryFieldOption,
    MedicalHistoryFieldVersion,
};

class FormFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personalInfoFields = [
            [
                'field_name' => 'First Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 1,
                'description_text' => null,
            ],
            [
                'field_name' => 'Last Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 2,
                'description_text' => null,
            ],
            [
                'field_name' => 'Middle Name',
                'form_field_type_id' => 2,
                'is_required' => false,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 3,
                'description_text' => null,
            ],
            [
                'field_name' => 'Date of Birth',
                'form_field_type_id' => 6,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 4,
                'description_text' => null,
            ],
            [
                'field_name' => 'Gender',
                'form_field_type_id' => 7,
                'is_required' => true,
                'options' => [
                    'MALE',
                    'FEMALE',
                ],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 5,
                'description_text' => null,
            ],
            [
                'field_name' => 'Religion',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 6,
                'description_text' => null,
            ],
            [
                'field_name' => 'Nationality',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 7,
                'description_text' => null,
            ],
            [
                'field_name' => 'Address',
                'form_field_type_id' => 3,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 8,
                'description_text' => null,
            ],
            [
                'field_name' => 'Contact Number',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 9,
                'description_text' => null,
            ],
            [
                'field_name' => 'EDUCATION LEVEL',
                'form_field_type_id' => 1,
                'is_required' => false,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 10,
                'description_text' => null,
            ],
            [
                'field_name' => 'Education Level',
                'form_field_type_id' => 5,
                'is_required' => true,
                'options' => [
                    'BASIC_ED',
                    'COLLEGE',
                ],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'form_order' => 11,
                'description_text' => null,
            ],
            [
                'field_name' => 'Grade Level',
                'form_field_type_id' => 5,
                'is_required' => false,
                'options' => [0,1,2,3,4,5,6,7,8,9,10,11,12],
                'required_with_field_id' => 11,
                'required_with_field_value' => 'BASIC_ED',
                'form_order' => 12,
                'description_text' => null,
            ],
            [
                'field_name' => 'Year Level',
                'form_field_type_id' => 5,
                'is_required' => false,
                'options' => [13,14,15,16],
                'required_with_field_id' => 11,
                'required_with_field_value' => 'COLLEGE',
                'form_order' => 13,
                'description_text' => null,
            ],
            [
                'field_name' => 'Department',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 11,
                'required_with_field_value' => 'COLLEGE',
                'options' => ['CCIS', 'COE', 'CON', 'COC', 'COB', 'CHTM', 'CASSED'],
                'form_order' => 14,
                'description_text' => null,
            ],
            [
                'field_name' => 'Course',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 11,
                'required_with_field_value' => 'COLLEGE',
                'options' => [
                    'BSIT',
                    'BSCS',
                    'BSIS',
                    'BSCE',
                    'BSECE',
                    'BSN',
                    'BSC',
                    'BSBA',
                    'BSA',
                    'BSCA',
                    'BSREM',
                    'BSHM',
                    'BST',
                    'BEE',
                    'BSE',
                    'BAC',
                    'BSSW',
                ],
                'form_order' => 15,
                'description_text' => null,
            ],
            [
                'field_name' => 'Section',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 16,
                'description_text' => null,
            ],
            [
                'field_name' => 'PARENT INFORMATION',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 17,
                'description_text' => null,
            ],
            [
                'field_name' => 'Mother\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 18,
                'description_text' => null,
            ],
            [
                'field_name' => 'Father\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 19,
                'description_text' => null,
            ],
            [
                'field_name' =>  'GUARDIAN INFORMATION',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 20,
                'description_text' => null,
            ],
            [
                'field_name' => 'Guardian\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 21,
                'description_text' => '(If parents are not around)',
            ],
            [
                'field_name' => 'Contact No. of Guardian',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 22,
                'description_text' => null
            ],
            [
                'field_name' => 'EMERGENCY CONTACT',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 23,
                'description_text' => null
            ],
            [
                'field_name' => 'Contact Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 24,
                'description_text' => null
            ],
            [
                'field_name' => 'Contact No.',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 25,
                'description_text' => null
            ]
        ];

        $medicalHistoryFields = [
            [
                'field_name' => 'CURRENT HEALTH STATUS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 1,
                'description_text' => null
            ],
            [
                'field_name' => 'Any present illnesses?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 2,
                'description_text' => null
            ],
            [
                'field_name' => 'Any Physical injuries/serious illness within the last 5 years?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 3,
                'description_text' => null
            ],
            [
                'field_name' => 'Have you been hospitalized within the last 5 years?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 4,
                'description_text' => null
            ],
            [
                'field_name' => 'LIFESTYLE',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 5,
                'description_text' => '(For Adult Students Only)'
            ],
            [
                'field_name' => 'Cigarette smoking?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 6,
                'description_text' => null
            ],
            [
                'field_name' => 'Alcohol beverage use?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 7,
                'description_text' => null
            ],
            [
                'field_name' => 'VACCINATION RECORDS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 8,
                'description_text' => null
            ],
            [
                'field_name' => 'Vaccinated for COVID-19?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 9,
                'description_text' => null
            ],
            [
                'field_name' => 'Vaccine Name',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => [],
                'form_order' => 10,
                'description_text' => null
            ],
            [
                'field_name' => '1st Dose',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => [],
                'form_order' => 11,
                'description_text' => 'If you are uncertain about the exact date, please just indicate the year it was administered.'
            ],
            [
                'field_name' => '2nd Dose',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => [],
                'form_order' => 12,
                'description_text' => null
            ],
            [
                'field_name' => '1st Booster',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => [],
                'form_order' => 13,
                'description_text' => null
            ],
            [
                'field_name' => '2nd Booster',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => [],
                'form_order' => 14,
                'description_text' => null
            ],
            [
                'field_name' => 'Other Vaccines',
                'form_field_type_id' => 4,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [
                    'Measles Containing Vaccine',
                    'Tetanus Diptheria',
                    'Human Papiloma Virus',
                    'Measles Rubella',
                ],
                'form_order' => 15,
                'description_text' => null
            ],
            [
                'field_name' => 'ILLNESSES',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 16,
                'description_text' => null
            ],
            [
                'field_name' => 'Illnesses',
                'form_field_type_id' => 4,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [
                    'Allergy',
                    'Asthma',
                    'Hypertension',
                    'Diabetes',
                    'Heart Disorder',
                    'Tuberculosis or Seizure',
                    'Other'
                ],
                'form_order' => 17,
                'description_text' => 'Please check if you have any of the following illnesses.'
            ],
            [
                'field_name' => 'SPECIAL CARE NEEDS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => [],
                'form_order' => 18,
                'description_text' => null
            ],
            [
                'field_name' => 'Has any special medication:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 19,
                'description_text' => null
            ],
            [
                'field_name' => 'Requires Special care/precaution concerning health:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 20,
                'description_text' => null
            ],
            [
                'field_name' => 'Is allergic to any drug/food preparation:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 21,
                'description_text' => null
            ],
            [
                'field_name' => 'Have limitations or restrictions on certain activities:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO'],
                'form_order' => 22,
                'description_text' => null
            ]
        ];

        foreach ($personalInfoFields as $fieldData) {
            $field = PersonalInfoField::create([
                'is_default' => true,
            ]);

            $version = PersonalInfoFieldVersion::create([
                'personal_info_field_id' => $field->id,
                'version_number' => 1,
                'field_name' => $fieldData['field_name'],
                'form_field_type_id' => $fieldData['form_field_type_id'],
                'is_required' => $fieldData['is_required'],
                'required_with_field_id' => $fieldData['required_with_field_id'],
                'required_with_field_value' => $fieldData['required_with_field_value'],
                'form_order' => $fieldData['form_order'],
                'description_text' => $fieldData['description_text'],
            ]);

            foreach ($fieldData['options'] as $optionValue) {
                PersonalInfoFieldOption::create([
                    'field_version_id' => $version->id,
                    'option_value' => $optionValue,
                ]);
            }
        }

        foreach ($medicalHistoryFields as $fieldData) {
            $field = MedicalHistoryField::create([
                'is_default' => true,
            ]);

            $version = MedicalHistoryFieldVersion::create([
                'medical_history_field_id' => $field->id,
                'version_number' => 1,
                'field_name' => $fieldData['field_name'],
                'form_field_type_id' => $fieldData['form_field_type_id'],
                'is_required' => $fieldData['is_required'],
                'required_with_field_id' => $fieldData['required_with_field_id'],
                'required_with_field_value' => $fieldData['required_with_field_value'],
                'form_order' => $fieldData['form_order'],
                'description_text' => $fieldData['description_text'],
            ]);

            foreach ($fieldData['options'] as $optionValue) {
                MedicalHistoryFieldOption::create([
                    'field_version_id' => $version->id,
                    'option_value' => $optionValue,
                ]);
            }
        }
    }
}
