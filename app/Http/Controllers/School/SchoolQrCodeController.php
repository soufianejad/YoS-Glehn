<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class SchoolQrCodeController extends Controller
{
    /**
     * Display the school's registration QR code.
     */
    public function showQrCode()
    {
        $user = auth()->user();
        $school = $user->managedSchool;

        if (! $school) {
            return redirect()->route('school.dashboard')->with('error', 'Vous n\'êtes associé à aucune école.');
        }

        // Ensure the school has an access code
        if (empty($school->access_code)) {
            $school->access_code = strtoupper(Str::random(8));
            $school->save();
        }

        $registrationUrl = route('student.register.code', ['code' => $school->access_code]);

        // Generate and save QR Code image if not exists
        if (empty($school->qr_code_path) || !Storage::disk('public')->exists($school->qr_code_path)) {
            $qrCodePath = 'schools/qrcodes/' . $school->id . '_' . Str::random(10) . '.svg';

            // We use simple-qrcode to generate the SVG
            $qrCodeContent = QrCode::size(300)->generate($registrationUrl);

            Storage::disk('public')->put($qrCodePath, $qrCodeContent);

            $school->qr_code_path = $qrCodePath;
            $school->save();
        }

        return view('school.dashboard.qrcode', compact('school', 'registrationUrl'));
    }
}
