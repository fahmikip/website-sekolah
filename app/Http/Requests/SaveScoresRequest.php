<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveScoresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('input_scores') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ['scores' => ['required', 'array'], 'scores.*.student_score_id' => ['required', 'exists:student_scores,id'], 'scores.*.reason' => ['required', 'string', 'max:255'], 'scores.*.details' => ['required', 'array'], 'scores.*.details.*.assessment_component_id' => ['required', 'exists:assessment_components,id'], 'scores.*.details.*.score' => ['required', 'numeric', 'min:0'], 'scores.*.details.*.weight' => ['required', 'numeric', 'min:0'], 'scores.*.details.*.max_score' => ['required', 'numeric', 'gt:0']];
    }
}
