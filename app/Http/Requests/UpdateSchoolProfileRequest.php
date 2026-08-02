<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage_school_profile') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nss' => ['nullable', 'string', 'max:30'],
            'level' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:Negeri,Swasta'],
            'accreditation' => ['nullable', 'string', 'max:10'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:'.now()->year],
            'address' => ['nullable', 'string', 'max:2000'],
            'village' => ['nullable', 'string', 'max:255'], 'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'], 'province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'], 'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'], 'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'], 'tagline' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
        ];
    }
}
