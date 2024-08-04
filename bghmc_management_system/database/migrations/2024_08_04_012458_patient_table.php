<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{ 
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('suffix_name')->nullable();
            $table->date('date_of_birth');
            $table->text('address');
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
