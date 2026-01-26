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
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name', 120);
        $table->string('email', 190)->unique()->index();
        $table->string('password', 255)->nullable(); // for Google accounts
        $table->enum('role', ['admin','trainer','student'])->default('student')->index();
        $table->string('avatar', 255)->nullable();
        $table->enum('status', ['active','inactive','banned'])->default('active')->index();
        $table->boolean('email_verified')->default(false)->index();
        $table->timestamp('emailverifiedat')->nullable();
        $table->string('google_id',64)->nullable()->unique();
        $table->string('verification_token',64)->nullable()->unique();
        $table->string('otp_code', 10)->nullable();
        $table->timestamp('otpexpiresat')->nullable();
        $table->boolean('otp_verified')->default(false)->index();
        $table->timestamps();
    });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
