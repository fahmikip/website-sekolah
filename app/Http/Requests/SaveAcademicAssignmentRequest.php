<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAcademicAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage_academic') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->input('type')) {
            'student_classroom' => ['type' => ['required'], 'student_id' => ['required', 'exists:students,id'], 'classroom_id' => ['required', 'exists:classrooms,id'], 'academic_year_id' => ['required', 'exists:academic_years,id'], 'status' => ['required', 'in:active,promoted,moved']],
            'parent_student' => ['type' => ['required'], 'parent_profile_id' => ['required', 'exists:parent_profiles,id'], 'student_id' => ['required', 'exists:students,id'], 'is_primary' => ['nullable', 'boolean']],
            'teacher_subject' => ['type' => ['required'], 'teacher_id' => ['required', 'exists:teachers,id'], 'subject_id' => ['required', 'exists:subjects,id'], 'academic_year_id' => ['required', 'exists:academic_years,id']],
            'homeroom' => ['type' => ['required'], 'teacher_id' => ['required', 'exists:teachers,id'], 'classroom_id' => ['required', 'exists:classrooms,id']],
            default => ['type' => ['required', 'in:student_classroom,parent_student,teacher_subject,homeroom']],
        };
    }
}
