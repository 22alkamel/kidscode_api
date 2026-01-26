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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users') ->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('program_enrollments')->onDelete('set null');
            
            // خيارات محددة مسبقًا
            $table->string('preferred_days')->nullable(); // sat_tue, sun_wed, mon_thu
            $table->string('preferred_time')->nullable(); // 08-10, 10-12, 13-15, 15-17, 19-21

            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
