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
        Schema::create('sessionstudents', function (Blueprint $table) {
             $table->id();
             $table->foreignId('session_id')->constrained()->cascadeOnDelete();
             $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
             $table->timestamp('watched_at')->nullable();
             $table->timestamp('submitted_at')->nullable();
             $table->integer('score')->nullable();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessionstudents');
    }
};
