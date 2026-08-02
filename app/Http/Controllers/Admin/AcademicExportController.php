<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AcademicPeopleExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportStudentsRequest;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\Teacher;
use Maatwebsite\Excel\Facades\Excel;

class AcademicExportController extends Controller
{
    public function export(string $type)
    {
        abort_unless(in_array($type, ['students', 'teachers']), 404);
        $model = $type === 'students' ? Student::class : Teacher::class;
        $columns = $type === 'students' ? ['nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'address', 'previous_school', 'entry_year', 'status'] : ['nip', 'nuptk', 'name', 'gender', 'position', 'education', 'employment_status', 'expertise', 'status'];

        abort_unless(request()->user()->can('export_academic'), 403);

        return Excel::download(new AcademicPeopleExport($model, $columns), $type.'-'.now()->format('Ymd-His').'.xlsx');
    }

    public function importStudents(ImportStudentsRequest $request)
    {
        $import = new StudentsImport;
        Excel::import($import, $request->file('file'));

        return back()->with('success', "{$import->count} data siswa berhasil diimpor.");
    }
}
