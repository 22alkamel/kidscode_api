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
        Schema::create('lesson_media', function (Blueprint $table) {
            
            $table->id();

            // ينتمي لدرس
            $table->unsignedBigInteger('lesson_id');

            // إضافة الـ FK مع اسم محدد لتجنب مشاكل duplicate key
            $table->foreign('lesson_id', 'fk_lesson_media_lesson_id')
                  ->references('id')
                  ->on('lessons')
                  ->onDelete('cascade');

            // نوع الوسائط
            $table->enum('type', [
                'image',
                'video',
                'pdf',
                'audio',
                'external_link'
            ])->index();

            // رابط أو مسار الملف
            $table->string('url');

            // نص مساعد (alt text، caption)
            $table->string('caption')->nullable();

            // ترتيب الوسائط داخل الدرس
            $table->unsignedInteger('order')->default(1)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_media');
    }
};
