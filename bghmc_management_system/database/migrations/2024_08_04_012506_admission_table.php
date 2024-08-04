<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{ 
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('ward');
            $table->dateTime('datetime_of_admission');
            $table->dateTime('datetime_of_discharge')->nullable();
            $table->timestamps();
    
            $table->foreign('patient_id')
                  ->references('id')
                  ->on('patients')
                  ->onDelete('cascade');
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
