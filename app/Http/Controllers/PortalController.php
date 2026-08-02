<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\ReportCard;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function teacher(Request $request)
    {
        $teacher = Teacher::where('user_id', $request->user()->id)->firstOrFail();

        return view('portals.teacher', ['profile' => $teacher, 'schedules' => Schedule::with(['classroom', 'subject'])->where('teacher_id', $teacher->id)->orderBy('day_of_week')->orderBy('starts_at')->get(), 'announcements' => Announcement::where('status', 'published')->latest()->limit(5)->get()]);
    }

    public function student(Request $request)
    {
        return $this->studentView(Student::where('user_id', $request->user()->id)->firstOrFail(), 'portals.student');
    }

    public function parent(Request $request, ?Student $student = null)
    {
        $parent = $request->user()->parentProfile;
        $children = $parent?->students()->get() ?? collect();
        abort_if($children->isEmpty(), 404);
        if ($student?->exists) {
            abort_unless($children->contains('id', $student->id), 403);
        }
        $selected = $student?->exists ? $student : $children->first();

        return $this->studentView($selected, 'portals.parent', ['children' => $children]);
    }

    public function principal()
    {
        return view('portals.principal', ['kpi' => ['Siswa' => Student::where('status', 'active')->count(), 'Guru' => Teacher::where('status', 'active')->count(), 'Rombel' => Classroom::count(), 'Rapor terbit' => ReportCard::whereIn('status', ['published', 'locked'])->count()], 'performance' => StudentScore::whereNotNull('final_score')->avg('final_score'), 'achievements' => Achievement::latest()->limit(5)->get()]);
    }

    private function studentView(Student $student, string $view, array $extra = [])
    {
        $classroomIds = $student->classrooms()->pluck('classrooms.id');

        return view($view, $extra + ['student' => $student, 'schedules' => Schedule::with(['classroom', 'subject'])->whereIn('classroom_id', $classroomIds)->orderBy('day_of_week')->get(), 'scores' => StudentScore::with('assessment.subject')->where('student_id', $student->id)->whereHas('assessment', fn ($q) => $q->where('status', 'published'))->latest()->get(), 'reports' => ReportCard::with('semester')->where('student_id', $student->id)->whereIn('status', ['published', 'locked'])->get(), 'announcements' => Announcement::where('status', 'published')->latest()->limit(5)->get()]);
    }
}
