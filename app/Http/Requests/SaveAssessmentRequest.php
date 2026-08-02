<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_assessment') ?? false;
    }

    public function rules(): array
    {
        return ['academic_year_id' => 'required|exists:academic_years,id', 'semester_id' => 'required|exists:semesters,id', 'classroom_id' => 'required|exists:classrooms,id', 'subject_id' => 'required|exists:subjects,id', 'teacher_id' => 'required|exists:teachers,id', 'assessment_type_id' => 'required|exists:assessment_types,id', 'title' => 'required|string|max:255', 'assessment_date' => 'required|date', 'max_score' => 'required|numeric|min:1|max:1000', 'status' => 'required|in:draft,published,archived', 'components' => 'required|array|min:1', 'components.*.learning_objective_id' => 'nullable|exists:learning_objectives,id', 'components.*.name' => 'required|string|max:255', 'components.*.weight' => 'required|numeric|min:0|max:100', 'components.*.max_score' => 'required|numeric|min:1|max:1000'];
    }

    public function after(): array
    {
        return [function ($validator) {
            if (abs(collect($this->input('components', []))->sum('weight') - 100) > 0.01) {
                $validator->errors()->add('components', 'Total bobot komponen harus tepat 100%.');
            }
        }];
    }
}
