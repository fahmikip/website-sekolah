<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage_cms') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ['images' => ['nullable', 'array', 'max:20'], 'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'video_url' => ['nullable', 'url', 'max:500'], 'caption' => ['nullable', 'string', 'max:255']];
    }
}
