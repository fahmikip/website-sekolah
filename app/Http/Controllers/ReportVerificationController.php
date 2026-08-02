<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;

class ReportVerificationController extends Controller
{
    public function __invoke(string $token)
    {
        $report = ReportCard::with(['student:id,name,nis', 'academicYear:id,name', 'semester:id,name'])->where('verification_token', $token)->whereIn('status', ['published', 'locked'])->firstOrFail();

        return view('reports.verify', compact('report'));
    }
}
