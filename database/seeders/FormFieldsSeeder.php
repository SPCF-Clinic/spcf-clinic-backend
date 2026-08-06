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
            ],
            [
                'field_name' => 'Last Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'Middle Name',
                'form_field_type_id' => 2,
                'is_required' => false,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'Date of Birth',
                'form_field_type_id' => 6,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
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
            ],
            [
                'field_name' => 'Religion',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'Nationality',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'Address',
                'form_field_type_id' => 3,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'Contact Number',
                'form_field_type_id' => 2,
                'is_required' => true,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
            ],
            [
                'field_name' => 'EDUCATION LEVEL',
                'form_field_type_id' => 1,
                'is_required' => false,
                'options' => [],
                'required_with_field_id' => null,
                'required_with_field_value' => null,
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
            ],
            [
                'field_name' => 'Grade Level',
                'form_field_type_id' => 5,
                'is_required' => false,
                'options' => [0,1,2,3,4,5,6,7,8,9,10,11,12],
                'required_with_field_id' => 11,
                'required_with_field_value' => 'BASIC_ED',
            ],
            [
                'field_name' => 'Year Level',
                'form_field_type_id' => 5,
                'is_required' => false,
                'options' => [13,14,15,16],
                'required_with_field_id' => 11,
                'required_with_field_value' => 'COLLEGE',
            ],
            [
                'field_name' => 'Department',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 11,
                'required_with_field_value' => 'COLLEGE',
                'options' => ['CCIS', 'COE', 'CON', 'COC', 'COB', 'CHTM', 'CASSED'],
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
                ]
            ],
            [
                'field_name' => 'Section',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'PARENT INFORMATION',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Mother\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Father\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' =>  'GUARDIAN INFORMATION',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Guardian\'s Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Contact No. of Guardian',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'EMERGENCY CONTACT',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Contact Name',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Contact No.',
                'form_field_type_id' => 2,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ]
        ];

        $medicalHistoryFields = [
            [
                'field_name' => 'CURRENT HEALTH STATUS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Any present illnesses?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Any Physical injuries/serious illness within the last 5 years?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Have you been hospitalized within the last 5 years?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'LIFESTYLE',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Cigarette smoking?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Alcohol beverage use?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'VACCINATION RECORDS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Vaccinated for COVID-19?',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Vaccine Name',
                'form_field_type_id' => 2,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => []
            ],
            [
                'field_name' => '1st Dose',
                'form_field_type_id' => 6,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => []
            ],
            [
                'field_name' => '2nd Dose',
                'form_field_type_id' => 6,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => []
            ],
            [
                'field_name' => '1st Booster',
                'form_field_type_id' => 6,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => []
            ],
            [
                'field_name' => '2nd Booster',
                'form_field_type_id' => 6,
                'is_required' => false,
                'required_with_field_id' => 9,
                'required_with_field_value' => 'YES',
                'options' => []
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
                ]
            ],
            [
                'field_name' => 'ILLNESSES',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
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
                ]
            ],
            [
                'field_name' => 'SPECIAL CARE NEEDS',
                'form_field_type_id' => 1,
                'is_required' => false,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => []
            ],
            [
                'field_name' => 'Has any special medication:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Requires Special care/precaution concerning health:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Is allergic to any drug/food preparation:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
            ],
            [
                'field_name' => 'Have limitations or restrictions on certain activities:',
                'form_field_type_id' => 7,
                'is_required' => true,
                'required_with_field_id' => null,
                'required_with_field_value' => null,
                'options' => ['YES', 'NO']
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
