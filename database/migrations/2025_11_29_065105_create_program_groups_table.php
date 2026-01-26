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
        Schema::create('program_groups', function (Blueprint $table) {
            $table->id();

            // البرنامج الذي تنتمي له المجموعة
            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            // اسم الجروب (Group A, Group B)
            $table->string('name', 120);

            // المدرب المسؤول
            $table->foreignId('trainer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_groups');
    }
};
