<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Api\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Api\Admin\TrackController as AdminTrackController;
use App\Http\Controllers\Api\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Api\Admin\LessonMediaController as AdminLessonMediaController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;

use App\Http\Controllers\Api\ProgramGroupController;
use App\Http\Controllers\Api\ClassSessionController;

// ---------------------------
// Public Routes (No Auth)
// ---------------------------

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/programs', [ProgramController::class, 'index']); // All programs
Route::get('/programs/{program}', [ProgramController::class, 'show']); // Program by ID
Route::get('/programs/{program_slug}/tracks', [ProgramController::class, 'tracks']); // Tracks by slug for public
// Removed duplicate /programs/{program}/tracks to avoid conflict

// ---------------------------
// Protected Routes (Auth + OTP Verified)
// ---------------------------
Route::middleware(['auth:sanctum','otp.verified'])->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);

    // Registrations
    Route::apiResource('registrations', RegistrationController::class);
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations']);

    // Enrollment
    Route::post('/programs/{program}/enroll', [EnrollmentController::class, 'enroll'])->middleware('permission:enroll_program');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'cancel']);
    Route::get('/my-enrollments', [EnrollmentController::class, 'myEnrollments']);
    Route::get('/programs/{program}/enrollments', [EnrollmentController::class, 'programEnrollments'])->middleware('permission:view_enrollments');
    Route::post('/enrollments/{enrollment}/confirm', [EnrollmentController::class, 'confirmPayment'])->middleware('permission:confirm_payments');

    // ---------------------------
    // Admin Routes
    // ---------------------------
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Stats
        Route::get('/stats', [AdminStatsController::class, 'index']);

        // Users
        Route::apiResource('/users', AdminUserController::class);

        // Programs
        // Route::apiResource('/programs', AdminProgramController::class);
         // ===== Programs CRUD =====
    // جلب كل البرامج
    Route::get('/programs', [AdminProgramController::class, 'index']);

    // إنشاء برنامج جديد
    Route::post('/programs', [AdminProgramController::class, 'store']);

    // عرض برنامج محدد
    Route::get('/programs/{program}', [AdminProgramController::class, 'show']);

    // تعديل برنامج محدد بالكامل
    Route::put('/programs/{program}', [AdminProgramController::class, 'update']);

    // تعديل جزئي (PATCH) برنامج محدد
    Route::patch('/programs/{program}', [AdminProgramController::class, 'update']);

    // حذف برنامج محدد
    Route::delete('/programs/{program}', [AdminProgramController::class, 'destroy']);
    
        Route::post('/programs/{program}/publish', [AdminProgramController::class, 'publish']);

        // Tracks
        Route::get('/programs/{program}/tracks', [AdminTrackController::class, 'index']);
        Route::post('/programs/{program}/tracks', [AdminTrackController::class, 'store']);
        Route::apiResource('/tracks', AdminTrackController::class)->only(['show','update','destroy']);

        // Lessons
        Route::get('/tracks/{track}/lessons', [AdminLessonController::class, 'index']);
        Route::post('/tracks/{track}/lessons', [AdminLessonController::class, 'store']);
        Route::apiResource('/lessons', AdminLessonController::class)->only(['show','update','destroy']);
        Route::post('/lessons/{lesson}/media', [AdminLessonMediaController::class, 'store']);
        Route::post('/lessons/{lesson}/questions', [AdminQuestionController::class, 'store']);
        Route::put('/lessons/{lesson}/questions/{question}', [AdminQuestionController::class, 'update']);

        // Registrations management
        Route::get('/registrations', [RegistrationController::class, 'index']);
        Route::put('/registrations/{id}', [RegistrationController::class, 'update']);
        Route::delete('/registrations/{id}', [RegistrationController::class, 'destroy']);

        // Program Groups
        Route::get('/programs/{program}/groups', [ProgramGroupController::class, 'index']);
        Route::post('/programs/{program}/groups', [ProgramGroupController::class, 'store']);
        Route::get('/groups/{group}', [ProgramGroupController::class, 'show']);
        Route::put('/groups/{group}', [ProgramGroupController::class, 'update']);
        Route::delete('/groups/{group}', [ProgramGroupController::class, 'destroy']);
        Route::get('/groups/{group}/students', [ProgramGroupController::class, 'students']);
        Route::post('/groups/{group}/add-student', [ProgramGroupController::class, 'addStudent']);
        Route::get('/programs/{program}/groups/{group}/available-students', [ProgramGroupController::class, 'availableStudents']);

        // Class Sessions
        Route::prefix('class-sessions')->group(function () {
            Route::post('/', [ClassSessionController::class, 'store']);
            Route::get('/student', [ClassSessionController::class, 'studentSessions']);
            Route::post('{id}/watched', [ClassSessionController::class, 'markWatched']);
            Route::post('{id}/submit', [ClassSessionController::class, 'submitAnswers']);
            Route::get('{id}/report', [ClassSessionController::class, 'sessionReport']);
        });

        Route::get('/groups/{groupId}/class-sessions', [ClassSessionController::class, 'groupSessions']);
    });

    // ---------------------------
    // Trainer Routes
    // ---------------------------
    Route::middleware('role:trainer')->group(function () {
        Route::get('/trainer/groups', [ProgramGroupController::class, 'myGroups']);
    });

});