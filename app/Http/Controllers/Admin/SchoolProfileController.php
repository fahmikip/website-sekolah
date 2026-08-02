<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSchoolProfileRequest;
use App\Models\SchoolProfile;
use App\Services\SchoolProfileService;

class SchoolProfileController extends Controller
{
    public function __construct(private readonly SchoolProfileService $service) {}

    public function edit()
    {
        return view('admin.school-profile.edit', ['school' => SchoolProfile::firstOrFail()]);
    }

    public function update(UpdateSchoolProfileRequest $request)
    {
        $this->service->update(
            SchoolProfile::firstOrFail(),
            $request->safe()->except(['logo', 'favicon']),
            $request->file('logo'),
            $request->file('favicon'),
        );

        return back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}
