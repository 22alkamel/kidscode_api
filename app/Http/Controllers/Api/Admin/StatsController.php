<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramEnrollment;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalPrograms = Program::count();
        $enrollmentsPending = ProgramEnrollment::where('payment_status','pending')->count();
        $enrollmentsConfirmed = ProgramEnrollment::where('payment_status','confirmed')->count();

        return response()->json([
            'users' => $totalUsers,
            'programs' => $totalPrograms,
            'enrollments' => [
                'pending' => $enrollmentsPending,
                'confirmed' => $enrollmentsConfirmed
            ]
        ]);
    }
}
