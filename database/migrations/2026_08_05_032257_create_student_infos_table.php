<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('student_id')->unique()->default('0');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('birthdate');
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('religion');
            $table->string('nationality');
            $table->string('address');
            $table->string('contact_number');
            $table->enum('education_level', ['BASIC_ED', 'COLLEGE']);
            $table->unsignedInteger('year_level');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('section');
            $table->string('mother_name');
            $table->string('father_name');
            $table->string('guardian_name');
            $table->string('guardian_contact_number');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->boolean('covid_19_vaccination')->default(false);
            $table->date('covid_19_vaccination_date')->nullable();
            $table->string('covid_19_vaccine_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_infos');
    }
};
