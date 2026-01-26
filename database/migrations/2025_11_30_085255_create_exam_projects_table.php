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
        Schema::create('exam_projects', function (Blueprint $table) {
            $table->id();

            // ينتمي لبرنامج
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id', 'fk_exam_projects_program_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('cascade');

            // ممكن متعلق بدرس معين (اختياري)
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->foreign('lesson_id', 'fk_exam_projects_lesson_id')
                  ->references('id')
                  ->on('lessons')
                  ->onDelete('set null');

            $table->string('title', 160);
            $table->text('description')->nullable();

            // مثال: رفع ملف المشروع، رابط GitHub، صور، الخ
            $table->enum('submission_type', [
                'file_upload',
                'text_answer',
                'code_answer',
                'external_link'
            ])->index();

            $table->unsignedInteger('max_score')->default(100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_projects');
    }
};
