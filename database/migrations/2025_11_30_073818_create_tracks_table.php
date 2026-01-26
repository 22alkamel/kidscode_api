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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            
          $table->unsignedBigInteger('program_id');

    // إضافة الـ FK
    $table->foreign('program_id', 'fk_tracks_program_id')
        ->references('id')
        ->on('programs')
        ->onDelete('cascade');  

            $table->string('title', 160);
           
            $table->string('track_img', 255)->nullable();
            $table->string('slug', 160)->unique();
            $table->text('description')->nullable();

            // ترتيب ظهور التراك داخل البرنامج
            $table->unsignedInteger('order')->default(1)->index();

            // الوقت التقديري لإنهاء الوحدة
            $table->unsignedInteger('estimated_time')->nullable();

            $table->boolean('is_published')->default(false)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
