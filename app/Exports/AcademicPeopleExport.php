<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AcademicPeopleExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly string $model, private readonly array $columns) {}

    public function collection(): Collection
    {
        return $this->model::orderBy('id')->get()->map(fn ($row) => collect($this->columns)->map(fn ($column) => $row->{$column})->all());
    }

    public function headings(): array
    {
        return $this->columns;
    }
}
