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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
    $table->string('title',160);
    $table->string('slug',160)->unique();
    $table->text('description')->nullable();
    $table->string('image', 255)->nullable();
    $table->enum('level', ['beginner','intermediate','advanced'])->default('beginner')->index();
    $table->integer('agemin')->nullable()->index();
    $table->integer('agemax')->nullable()->index();
    $table->integer('duration_weeks')->nullable();
     $table->integer('price')->nullable();
    $table->foreignId('created_by')->constrained('users')->index();
    $table->boolean('is_published')->default(false)->index();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
