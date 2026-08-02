<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportStudentsRequest;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AcademicExportController extends Controller
{
    public function export(string $type)
    {
        abort_unless(in_array($type, ['students', 'teachers']), 404);
        $model = $type === 'students' ? Student::class : Teacher::class;
        $columns = $type === 'students' ? ['nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'address', 'previous_school', 'entry_year', 'status'] : ['nip', 'nuptk', 'name', 'gender', 'position', 'education', 'employment_status', 'expertise', 'status'];

        return response()->streamDownload(function () use ($model, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $model::orderBy('id')->chunk(500, function ($rows) use ($out, $columns) {
                foreach ($rows as $row) {
                    fputcsv($out, collect($columns)->map(fn ($column) => $row->{$column})->all());
                }
            });
            fclose($out);
        }, $type.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function importStudents(ImportStudentsRequest $request)
    {
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $required = ['nis', 'name', 'gender', 'entry_year', 'status'];
        abort_unless(empty(array_diff($required, $headers)), 422, 'Header CSV tidak valid.');
        $count = 0;
        DB::transaction(function () use ($handle, $headers, &$count) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) {
                    continue;
                }$data = array_combine($headers, $row);
                Validator::make($data, ['nis' => 'required|max:30', 'name' => 'required|max:255', 'gender' => 'required|in:L,P', 'entry_year' => 'required|integer', 'status' => 'required|in:active,inactive,graduated'])->validate();
                Student::updateOrCreate(['nis' => $data['nis']], collect($data)->only(['nisn', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'address', 'previous_school', 'entry_year', 'status'])->map(fn ($v) => $v === '' ? null : $v)->all());
                $count++;
            }
        });
        fclose($handle);

        return back()->with('success',"{$count} data siswa berhasil diimpor.");
    }
}
