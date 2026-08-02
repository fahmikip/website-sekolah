<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAcademicMasterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $permission = $this->isMethod('post') ? 'create_academic' : 'edit_academic';

        return $this->user()?->can($permission) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->route('module')) {
            'academic-years' => ['name' => ['required', 'string', 'max:20'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after:starts_on'], 'is_active' => ['nullable', 'boolean']],
            'semesters' => ['academic_year_id' => ['required', 'exists:academic_years,id'], 'name' => ['required', 'string', 'max:30'], 'number' => ['required', 'integer', 'between:1,2'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date', 'after:starts_on'], 'is_active' => ['nullable', 'boolean']],
            'curricula' => ['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:30'], 'description' => ['nullable', 'string'], 'is_active' => ['nullable', 'boolean']],
            'phases' => ['curriculum_id' => ['required', 'exists:curricula,id'], 'name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:10'], 'description' => ['nullable', 'string'], 'is_active' => ['nullable', 'boolean']],
            'levels' => ['phase_id' => ['nullable', 'exists:phases,id'], 'name' => ['required', 'string', 'max:100'], 'grade_number' => ['required', 'integer', 'between:1,12'], 'sort_order' => ['nullable', 'integer', 'min:0']],
            'classrooms' => ['academic_year_id' => ['required', 'exists:academic_years,id'], 'level_id' => ['required', 'exists:levels,id'], 'name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:30'], 'capacity' => ['required', 'integer', 'min:1'], 'room' => ['nullable', 'string', 'max:100'], 'is_active' => ['nullable', 'boolean']],
            'subjects' => ['curriculum_id' => ['required', 'exists:curricula,id'], 'name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:30'], 'group' => ['nullable', 'string', 'max:100'], 'weekly_hours' => ['required', 'integer', 'min:1'], 'is_active' => ['nullable', 'boolean']],
            'teachers' => ['name' => ['required', 'string', 'max:255'], 'nip' => ['nullable', 'string', 'max:30'], 'nuptk' => ['nullable', 'string', 'max:30'], 'gender' => ['required', 'in:L,P'], 'position' => ['nullable', 'string', 'max:100'], 'education' => ['nullable', 'string', 'max:100'], 'employment_status' => ['nullable', 'string', 'max:100'], 'expertise' => ['nullable', 'string', 'max:255'], 'bio' => ['nullable', 'string'], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], 'is_public' => ['nullable', 'boolean'], 'status' => ['required', 'in:active,inactive']],
            'staff' => ['employee_number' => ['nullable', 'string', 'max:30'], 'name' => ['required', 'string', 'max:255'], 'gender' => ['required', 'in:L,P'], 'position' => ['required', 'string', 'max:100'], 'employment_status' => ['nullable', 'string', 'max:100'], 'education' => ['nullable', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:30'], 'email' => ['nullable', 'email'], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], 'is_public' => ['nullable', 'boolean'], 'status' => ['required', 'in:active,inactive']],
            'students' => ['nis' => ['required', 'string', 'max:30'], 'nisn' => ['nullable', 'string', 'max:30'], 'name' => ['required', 'string', 'max:255'], 'gender' => ['required', 'in:L,P'], 'birth_place' => ['nullable', 'string', 'max:100'], 'birth_date' => ['nullable', 'date'], 'religion' => ['nullable', 'string', 'max:50'], 'address' => ['nullable', 'string'], 'previous_school' => ['nullable', 'string', 'max:255'], 'entry_year' => ['required', 'integer', 'between:2000,'.(now()->year + 1)], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], 'status' => ['required', 'in:active,inactive,graduated']],
            'parents' => ['name' => ['required', 'string', 'max:255'], 'relationship' => ['required', 'string', 'max:30'], 'phone' => ['nullable', 'string', 'max:30'], 'email' => ['nullable', 'email'], 'address' => ['nullable', 'string'], 'occupation' => ['nullable', 'string', 'max:100']],
            'alumni' => ['student_id' => ['nullable', 'exists:students,id'], 'nis' => ['nullable', 'string', 'max:30'], 'name' => ['required', 'string', 'max:255'], 'graduation_year' => ['required', 'integer', 'between:1900,'.now()->year], 'further_education' => ['nullable', 'string', 'max:255'], 'occupation' => ['nullable', 'string', 'max:255'], 'achievement' => ['nullable', 'string'], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'publication_consent' => ['nullable', 'boolean']],
            'schedules' => ['academic_year_id' => ['required', 'exists:academic_years,id'], 'semester_id' => ['required', 'exists:semesters,id'], 'classroom_id' => ['required', 'exists:classrooms,id'], 'subject_id' => ['required', 'exists:subjects,id'], 'teacher_id' => ['required', 'exists:teachers,id'], 'day_of_week' => ['required', 'integer', 'between:1,6'], 'starts_at' => ['required', 'date_format:H:i'], 'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'], 'room' => ['nullable', 'string', 'max:100']],
            default => abort(404),
        };
    }
}
