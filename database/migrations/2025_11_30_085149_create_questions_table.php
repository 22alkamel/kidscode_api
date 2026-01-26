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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // ينتمي لدرس
            $table->unsignedBigInteger('lesson_id');

            // إضافة الـ FK مع اسم محدد لتجنب مشاكل duplicate key
            $table->foreign('lesson_id', 'fk_questions_lesson_id')
                  ->references('id')
                  ->on('lessons')
                  ->onDelete('cascade');

            $table->enum('type', [
                'multiple_choice',
                'true_false',
                'fill_blank',
                'code_output'
            ])->index();

            // نص السؤال
            $table->text('question');

            // خيارات JSON للسؤال (A,B,C,D)
            $table->json('options')->nullable();

            // الإجابة الصحيحة
            $table->string('correct_answer')->nullable();

            // ترتيب السؤال داخل الدرس
            $table->unsignedInteger('order')->default(1)->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
