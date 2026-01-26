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
        Schema::create('group_students', function (Blueprint $table) {
            $table->id();

            // الجروب
            $table->foreignId('group_id')
                ->constrained('program_groups')
                ->cascadeOnDelete();

            // الطالب
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // عدم تكرار الطالب داخل نفس الجروب
            $table->unique(['group_id', 'student_id']);
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_students');
    }
};
