<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public int $count = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $data = $row->toArray();
            Validator::make($data, ['nis' => 'required|max:30', 'name' => 'required|max:255', 'gender' => 'required|in:L,P', 'entry_year' => 'required|integer', 'status' => 'required|in:active,inactive,graduated'])->validate();
            Student::updateOrCreate(['nis' => $data['nis']], collect($data)->only(['nisn', 'name', 'gender', 'birth_place', 'birth_date', 'religion', 'address', 'previous_school', 'entry_year', 'status'])->map(fn ($value) => $value === '' ? null : $value)->all());
            $this->count++;
        }
    }
}
