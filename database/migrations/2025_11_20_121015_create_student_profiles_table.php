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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
           $table->foreignId('user_id')->constrained('users')->unique()->cascadeOnDelete();
    $table->integer('age')->nullable();
    $table->string('school',160)->nullable();
    $table->string('grade',40)->nullable();
    $table->string('guardian_name',120)->nullable();
    $table->string('guardian_phone',30)->nullable();
    $table->json('interests')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
