<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate(['academic_year_id' => 'nullable|exists:academic_years,id', 'semester_id' => 'nullable|exists:semesters,id', 'classroom_id' => 'nullable|exists:classrooms,id']);
        $yearId = $filters['academic_year_id'] ?? AcademicYear::where('is_active', true)->value('id');
        $semesterId = $filters['semester_id'] ?? Semester::where('is_active', true)->value('id');
        $schedules = Schedule::with(['classroom', 'subject', 'teacher'])->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))->when($filters['classroom_id'] ?? null, fn ($q, $id) => $q->where('classroom_id', $id))->orderBy('starts_at')->get()->groupBy('day_of_week');

        return view('admin.academic.timetable', ['schedules' => $schedules, 'academicYears' => AcademicYear::orderByDesc('starts_on')->get(), 'semesters' => Semester::orderByDesc('starts_on')->get(), 'classrooms' => Classroom::orderBy('name')->get(), 'yearId' => $yearId, 'semesterId' => $semesterId]);
    }
}
