<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportAssessmentScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('input_scores') ?? false;
    }

    public function rules(): array
    {
        return ['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']];
    }
}
