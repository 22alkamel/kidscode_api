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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            // ينتمي لتراك
            $table->unsignedBigInteger('track_id');

            // إضافة الـ FK مع اسم محدد لتجنب مشاكل duplicate key
            $table->foreign('track_id', 'fk_lessons_track_id')
                  ->references('id')
                  ->on('tracks')
                  ->onDelete('cascade');

            $table->string('title', 160);
            $table->string('slug', 160)->unique();

            // محتوى الدرس
            $table->longText('content')->nullable();

            // ترتيب الدرس داخل التراك
            $table->unsignedInteger('order')->default(1)->index();

            // المدة التقديرية للدرس
            $table->unsignedInteger('duration_minutes')->nullable();

            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
