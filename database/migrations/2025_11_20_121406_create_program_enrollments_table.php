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
        Schema::create('program_enrollments', function (Blueprint $table) {
            $table->id();
     $table->foreignId('program_id')
                  ->constrained('programs')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

    $table->string('age_group',20)->nullable()->index();
    $table->timestamp('enrolled_at')->nullable();
    $table->enum('payment_status', ['pending','confirmed','rejected'])->default('pending')->index();
    $table->enum('payment_method', ['whatsapp','sms','cash','other'])->nullable()->index();
    $table->string('payment_reference',120)->nullable();
    $table->text('confirmation_note')->nullable();
    $table->foreignId('confirmed_by')
      ->nullable()
      ->constrained('users', 'id') // العمود 'id' محدد
      ->nullOnDelete();

    $table->timestamp('confirmed_at')->nullable();
    $table->boolean('activated')->default(false)->index();
    $table->timestamp('activation_at')->nullable();
    $table->timestamps();

    $table->unique(['program_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_enrollments');
    }
};
