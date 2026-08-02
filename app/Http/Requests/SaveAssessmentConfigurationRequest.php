<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAssessmentConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage_assessment') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->route('module')) {
            'outcomes' => ['curriculum_id' => ['required', 'exists:curricula,id'], 'phase_id' => ['required', 'exists:phases,id'], 'subject_id' => ['required', 'exists:subjects,id'], 'academic_year_id' => ['required', 'exists:academic_years,id'], 'code' => ['required', 'string', 'max:30'], 'description' => ['required', 'string'], 'is_active' => ['nullable', 'boolean']],
            'objectives' => ['learning_outcome_id' => ['required', 'exists:learning_outcomes,id'], 'code' => ['required', 'string', 'max:30'], 'description' => ['required', 'string'], 'sequence' => ['required', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean']],
            'scopes' => ['learning_objective_id' => ['required', 'exists:learning_objectives,id'], 'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'sort_order' => ['nullable', 'integer', 'min:0']],
            'criteria' => ['curriculum_id' => ['required', 'exists:curricula,id'], 'subject_id' => ['nullable', 'exists:subjects,id'], 'level_id' => ['nullable', 'exists:levels,id'], 'name' => ['required', 'string', 'max:100'], 'passing_score' => ['required', 'numeric', 'between:0,100'], 'is_active' => ['nullable', 'boolean']],
            'ranges' => ['achievement_criterion_id' => ['required', 'exists:achievement_criteria,id'], 'min_score' => ['required', 'numeric', 'between:0,100'], 'max_score' => ['required', 'numeric', 'gte:min_score', 'max:100'], 'label' => ['required', 'string', 'max:100'], 'color' => ['nullable', 'string', 'max:20']],
            'types' => ['name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:30'], 'is_active' => ['nullable', 'boolean'], 'is_summative' => ['nullable', 'boolean']],
            'weights' => ['curriculum_id' => ['required', 'exists:curricula,id'], 'subject_id' => ['required', 'exists:subjects,id'], 'classroom_id' => ['required', 'exists:classrooms,id'], 'semester_id' => ['required', 'exists:semesters,id'], 'assessment_type_id' => ['required', 'exists:assessment_types,id'], 'weight' => ['required', 'numeric', 'between:0,100']],
            default => abort(404),
        };
    }
}
