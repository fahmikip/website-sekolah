<?php

namespace App\Services;

use App\Models\SchoolProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SchoolProfileService
{
    public function update(SchoolProfile $school, array $data, ?UploadedFile $logo = null, ?UploadedFile $favicon = null): SchoolProfile
    {
        return DB::transaction(function () use ($school, $data, $logo, $favicon) {
            foreach (['logo_path' => $logo, 'favicon_path' => $favicon] as $column => $file) {
                if (! $file) {
                    continue;
                }

                if ($school->{$column}) {
                    Storage::disk('public')->delete($school->{$column});
                }

                $data[$column] = $file->store('school', 'public');
            }

            $school->update($data);

            return $school->refresh();
        });
    }
}
