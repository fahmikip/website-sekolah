<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Alumnus;
use App\Models\Classroom;
use App\Models\Curriculum;
use App\Models\Level;
use App\Models\ParentProfile;
use App\Models\Phase;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(['name' => '2026/2027'], ['starts_on' => '2026-07-13', 'ends_on' => '2027-06-25', 'is_active' => true]);
        $semester = Semester::firstOrCreate(['academic_year_id' => $year->id, 'number' => 1], ['name' => 'Ganjil', 'starts_on' => '2026-07-13', 'ends_on' => '2026-12-18', 'is_active' => true]);
        Semester::firstOrCreate(['academic_year_id' => $year->id, 'number' => 2], ['name' => 'Genap', 'starts_on' => '2027-01-04', 'ends_on' => '2027-06-25', 'is_active' => false]);
        $curriculum = Curriculum::firstOrCreate(['code' => 'KM'], ['name' => 'Kurikulum Merdeka', 'description' => 'Kurikulum berbasis kompetensi dan fase.', 'is_active' => true]);
        $phase = Phase::firstOrCreate(['curriculum_id' => $curriculum->id, 'code' => 'E'], ['name' => 'Fase E', 'description' => 'Fase kelas X', 'is_active' => true]);
        $levels = collect([10, 11, 12])->map(fn ($grade) => Level::firstOrCreate(['grade_number' => $grade], ['phase_id' => $grade === 10 ? $phase->id : null, 'name' => 'Kelas '.$grade, 'sort_order' => $grade]));
        $teachers = collect(range(1, 10))->map(fn ($i) => Teacher::firstOrCreate(['nip' => '198000'.str_pad((string) $i, 3, '0', STR_PAD_LEFT)], ['name' => 'Guru '.$i, 'gender' => $i % 2 ? 'L' : 'P', 'position' => 'Guru Mata Pelajaran', 'education' => 'S1', 'employment_status' => 'Tetap', 'expertise' => 'Pendidikan', 'is_public' => true, 'status' => 'active']));
        $subjects = collect(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Informatika', 'IPA', 'IPS', 'Pendidikan Agama', 'PPKn', 'Seni Budaya', 'PJOK'])->map(fn ($name, $i) => Subject::firstOrCreate(['code' => 'MP'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)], ['curriculum_id' => $curriculum->id, 'name' => $name, 'weekly_hours' => 2, 'is_active' => true]));
        $classes = collect();
        foreach ($levels as $level) {
            foreach (['A', 'B'] as $suffix) {
                $classes->push(Classroom::firstOrCreate(['code' => $level->grade_number.$suffix], ['academic_year_id' => $year->id, 'level_id' => $level->id, 'name' => $level->grade_number.' '.$suffix, 'capacity' => 36, 'room' => 'Ruang '.$level->grade_number.$suffix, 'is_active' => true]));
            }
        }
        foreach (range(1, 100) as $i) {
            $student = Student::firstOrCreate(['nis' => '2026'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)], ['nisn' => '00'.str_pad((string) $i, 8, '0', STR_PAD_LEFT), 'name' => 'Siswa '.$i, 'gender' => $i % 2 ? 'L' : 'P', 'birth_place' => 'Jakarta', 'birth_date' => now()->subYears(15)->subDays($i), 'religion' => 'Islam', 'address' => 'Alamat siswa '.$i, 'entry_year' => 2026, 'status' => 'active']);
            $class = $classes[($i - 1) % $classes->count()];
            $student->classrooms()->syncWithoutDetaching([$class->id => ['academic_year_id' => $year->id, 'joined_on' => '2026-07-13', 'status' => 'active']]);
            if ($i <= 20) {
                $parent = ParentProfile::firstOrCreate(['email' => 'parent'.$i.'@example.test'], ['name' => 'Orang Tua '.$i, 'relationship' => 'Orang Tua', 'phone' => '08120000'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)]);
                $parent->students()->syncWithoutDetaching([$student->id => ['is_primary' => true]]);
            }
        }
        foreach (range(1, 5) as $i) {
            Staff::firstOrCreate(['employee_number' => 'TENDIK'.str_pad((string) $i, 3, '0', STR_PAD_LEFT)], ['name' => 'Tenaga Kependidikan '.$i, 'gender' => $i % 2 ? 'L' : 'P', 'position' => $i === 1 ? 'Kepala Tata Usaha' : 'Staf Tata Usaha', 'employment_status' => 'Tetap', 'education' => 'S1', 'status' => 'active']);
        }
        $firstParent = ParentProfile::first();
        if ($firstParent) {
            $firstParent->students()->syncWithoutDetaching([Student::find(2)->id => ['is_primary' => false]]);
        }
        foreach (range(1, 10) as $i) {
            Alumnus::firstOrCreate(['nis' => 'ALUM'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)], ['name' => 'Alumni '.$i, 'graduation_year' => 2025, 'further_education' => 'Perguruan Tinggi', 'publication_consent' => true]);
        }
        foreach ($subjects as $i => $subject) {
            $teacher = $teachers[$i];
            $teacher->subjects()->syncWithoutDetaching([$subject->id => ['academic_year_id' => $year->id]]);
            Schedule::firstOrCreate(['academic_year_id' => $year->id, 'semester_id' => $semester->id, 'classroom_id' => $classes[$i % $classes->count()]->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'day_of_week' => ($i % 5) + 1], ['starts_at' => '07:00:00', 'ends_at' => '08:30:00', 'room' => 'Ruang '.(($i % 6) + 1)]);
        }

        $teacherUser = User::updateOrCreate(['email' => 'guru@example.test'], ['name' => $teachers->first()->name, 'password' => Hash::make('password'), 'email_verified_at' => now()]);
        $teacherUser->syncRoles(['Guru']);
        $teachers->first()->update(['user_id' => $teacherUser->id]);
        $student = Student::first();
        $studentUser = User::updateOrCreate(['email' => 'siswa@example.test'], ['name' => $student->name, 'password' => Hash::make('password'), 'email_verified_at' => now()]);
        $studentUser->syncRoles(['Siswa']);
        $student->update(['user_id' => $studentUser->id]);
        $parent = $student->parents()->first();
        if ($parent) {
            $parentUser = User::updateOrCreate(['email' => 'orangtua@example.test'], ['name' => $parent->name, 'password' => Hash::make('password'), 'email_verified_at' => now()]);
            $parentUser->syncRoles(['Orang Tua']);
            $parent->update(['user_id' => $parentUser->id]);
        }
        $principal = User::updateOrCreate(['email' => 'kepalasekolah@example.test'], ['name' => 'Kepala Sekolah', 'password' => Hash::make('password'), 'email_verified_at' => now()]);
        $principal->syncRoles(['Kepala Sekolah']);
    }
}
