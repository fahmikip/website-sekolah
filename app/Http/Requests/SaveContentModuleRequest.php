<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveContentModuleRequest extends FormRequest
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
        $common = ['image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']];

        return $common + match ($this->route('module')) {
            'announcements' => ['title' => ['required', 'string', 'max:255'], 'category' => ['required', 'in:Umum,Akademik,PPDB,Ujian,Libur,Kegiatan'], 'excerpt' => ['nullable', 'string', 'max:1000'], 'content' => ['required', 'string'], 'status' => ['required', 'in:draft,published,scheduled,expired'], 'published_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', 'after:published_at'], 'is_featured' => ['nullable', 'boolean']],
            'events' => ['title' => ['required', 'string', 'max:255'], 'description' => ['required', 'string'], 'location' => ['nullable', 'string', 'max:255'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'], 'person_in_charge' => ['nullable', 'string', 'max:255'], 'status' => ['required', 'in:draft,published,cancelled'], 'is_featured' => ['nullable', 'boolean']],
            'galleries' => ['title' => ['required', 'string', 'max:255'], 'category' => ['nullable', 'string', 'max:100'], 'activity_year' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)], 'description' => ['nullable', 'string'], 'is_published' => ['nullable', 'boolean']],
            'facilities' => ['name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'quantity' => ['required', 'integer', 'min:0'], 'condition' => ['required', 'in:Sangat Baik,Baik,Cukup,Rusak'], 'is_published' => ['nullable', 'boolean']],
            'achievements' => ['title' => ['required', 'string', 'max:255'], 'recipient_name' => ['nullable', 'string', 'max:255'], 'category' => ['required', 'in:Akademik,Olahraga,Seni,Keagamaan,Teknologi,Organisasi'], 'level' => ['required', 'in:Sekolah,Kecamatan,Kabupaten,Provinsi,Nasional,Internasional'], 'achievement_date' => ['nullable', 'date'], 'description' => ['nullable', 'string'], 'is_published' => ['nullable', 'boolean']],
            'extracurriculars' => ['name' => ['required', 'string', 'max:255'], 'advisor' => ['nullable', 'string', 'max:255'], 'schedule' => ['nullable', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'member_count' => ['nullable', 'integer', 'min:0'], 'achievements' => ['nullable', 'string'], 'is_published' => ['nullable', 'boolean']],
            'downloads' => ['title' => ['required', 'string', 'max:255'], 'category' => ['required', 'in:Kalender Akademik,Formulir,Brosur,Surat Edaran,SOP,Tata Tertib,Dokumen Sekolah'], 'description' => ['nullable', 'string'], 'file' => [$this->isMethod('post') ? 'required' : 'nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip', 'max:10240'], 'is_published' => ['nullable', 'boolean']],
            'faqs' => ['question' => ['required', 'string', 'max:500'], 'answer' => ['required', 'string'], 'category' => ['nullable', 'string', 'max:100'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean']],
            'pages' => ['title' => ['required', 'string', 'max:255'], 'content' => ['required', 'string'], 'status' => ['required', 'in:draft,published'], 'meta_title' => ['nullable', 'string', 'max:255'], 'meta_description' => ['nullable', 'string', 'max:500'], 'sort_order' => ['nullable', 'integer', 'min:0']],
            'banners' => ['title' => ['required', 'string', 'max:255'], 'subtitle' => ['nullable', 'string', 'max:500'], 'cta_label' => ['nullable', 'string', 'max:100'], 'cta_url' => ['nullable', 'string', 'max:255'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'is_active' => ['nullable', 'boolean'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at']],
            default => abort(404),
        };
    }
}
