<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Api\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Api\Admin\TrackController as AdminTrackController;

use App\Http\Controllers\Api\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Api\Admin\LessonMediaController as AdminLessonMediaController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\Admin\ExamProjectController as AdminExamProjectController;

use App\Http\Controllers\Api\ProgramGroupController;
use App\Http\Controllers\Api\ClassSessionController;

// use App\Http\Controllers\Api\SessionController;
// use App\Http\Controllers\Api\TrackController;
// use App\Http\Controllers\Api\LessonController;

use App\Http\Controllers\Api\ProfileController;
use App\Models\Role;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);

// Public programs
Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/programs/{slug}', [ProgramController::class, 'show']);


// Protected routes (Sanctum + OTP verified)
Route::middleware(['auth:sanctum','otp.verified'])->group(function () {
    // auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

   

    Route::post('/programs', [ProgramController::class, 'store'])->middleware('permission:manage_programs');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->middleware('permission:manage_programs');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->middleware('permission:manage_programs');
    Route::post('/programs/{program}/publish', [ProgramController::class, 'publish'])->middleware('permission:manage_programs');

    // Enrollment
    Route::post('/programs/{program}/enroll', [EnrollmentController::class, 'enroll'])->middleware('permission:enroll_program');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'cancel']);
    Route::get('/my-enrollments', [EnrollmentController::class, 'myEnrollments']);
    Route::get('/programs/{program}/enrollments', [EnrollmentController::class, 'programEnrollments'])->middleware('permission:view_enrollments');
    Route::post('/enrollments/{enrollment}/confirm', [EnrollmentController::class, 'confirmPayment'])->middleware('permission:confirm_payments');

    // Profile (students/trainers)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    // مسار تغيير كلمة المرور
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);


    Route::apiResource('registrations', RegistrationController::class);
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations']);
     

    // Admin area
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/stats', [AdminStatsController::class, 'index']);
        Route::apiResource('/admin/users', AdminUserController::class);
        // ======= Admin Programs ==========
        Route::get('/admin/programs', [AdminProgramController::class, 'index']);
        Route::post('/admin/programs', [AdminProgramController::class, 'store']);
        Route::get('/admin/programs/{program}', [AdminProgramController::class, 'show']);
        Route::post('/admin/programs/{program}', [AdminProgramController::class, 'update']);
        Route::put('/admin/programs/{program}', [AdminProgramController::class, 'update']);
        Route::delete('/admin/programs/{program}', [AdminProgramController::class, 'destroy']);
        Route::post('/admin/programs/{program}/publish', [AdminProgramController::class, 'publish']);

 // إضافة جروب داخل برنامج

    Route::get('programs/{program}/groups', [ProgramGroupController::class, 'index']);
    Route::post('programs/{program}/groups', [ProgramGroupController::class, 'store']);
    Route::get('groups/{group}', [ProgramGroupController::class, 'show']);

    Route::put('groups/{group}', [ProgramGroupController::class, 'update']);
    Route::delete('groups/{group}', [ProgramGroupController::class, 'destroy']);
    Route::get('/groups/{group}/students', [ProgramGroupController::class, 'students']);
    Route::post('groups/{group}/add-student', [ProgramGroupController::class, 'addStudent']);
    Route::get('programs/{program}/groups/{group}/available-students',[ProgramGroupController::class, 'availableStudents']);
           
             // Tracks CRUD
        Route::get('/programs/{program}/tracks', [AdminTrackController::class, 'index']);   // عرض كل المسارات لبرنامج
        Route::post('/programs/{program}/tracks', [AdminTrackController::class, 'store']);  // إضافة مسار داخل برنامج
        
        Route::get('/tracks/{track}', [AdminTrackController::class, 'show']); 
        Route::post('/tracks/{track}', [AdminTrackController::class, 'update']);    
        Route::put('/tracks/{track}', [AdminTrackController::class, 'update']);             // تعديل مسار
        Route::delete('/tracks/{track}', [AdminTrackController::class, 'destroy']);         // حذف مسار

             
       
        // Lessons CRUD
        Route::get('/tracks/{track}/lessons', [AdminLessonController::class, 'index']);
        Route::post('/tracks/{track}/lessons', [AdminLessonController::class, 'store']);
        Route::get('lessons/{lesson}', [AdminLessonController::class, 'show']);
        Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update']);
        Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy']);

        Route::post('/lessons/{lesson}/media', [AdminLessonMediaController::class, 'store']);

        Route::post('/lessons/{lesson}/questions', [AdminQuestionController::class, 'store']);
        Route::put('/lessons/{lesson}/questions/{question}', [AdminQuestionController::class, 'update']);


         Route::get('/admin/registrations', [RegistrationController::class, 'index']);
         Route::put('/admin/registrations/{id}', [RegistrationController::class, 'update']);
         Route::delete('/admin/registrations/{id}', [RegistrationController::class, 'destroy']);

        

Route::prefix('class-sessions')->group(function () {

    // إنشاء حصة جديدة (للإدارة / المدرب)
    Route::post('/', [ClassSessionController::class, 'store']);

    // حصص الطالب المسجل فيها
    Route::get('/student', [ClassSessionController::class, 'studentSessions']);

    // تعليم الحصة كمشاهدة
    Route::post('{id}/watched', [ClassSessionController::class, 'markWatched']);

    // تسليم إجابات الحصة
    Route::post('{id}/submit', [ClassSessionController::class, 'submitAnswers']);

    // تقرير الحصة (للإدارة)
    Route::get('{id}/report', [ClassSessionController::class, 'sessionReport']);
});

// جلب حصص مجموعة معينة
Route::get('/groups/{groupId}/class-sessions', [ClassSessionController::class, 'groupSessions']);

    //      Route::post('/sessions', [SessionController::class, 'store']);
    // Route::get('/student/sessions', [SessionController::class, 'studentSessions']);
    // Route::post('/sessions/{id}/watched', [SessionController::class, 'markWatched']);
    // Route::post('/sessions/{id}/submit', [SessionController::class, 'submitAnswers']);
    // Route::get('/sessions/{id}/report', [SessionController::class, 'sessionReport']);
    // Route::get('/groups/{groupId}/sessions', [SessionController::class, 'groupSessions']);

  

     });
     

      Route::middleware('role:trainer')->group(function () {
        // المدرب يشوف فقط مجموعاته
        Route::get('/trainer/groups', 
            [ProgramGroupController::class, 'myGroups']);

        // يشوف طلاب مجموعته فقط
        // Route::get('/groups/{group}/students', 
        //     [ProgramGroupController::class, 'students'])
        //     ->middleware('can:access,group');
    });


    

});
