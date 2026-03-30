<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Define certificates logic
        $certificates = [
            [
                'id' => '100_points',
                'title' => __('Certificat des 100 Points'),
                'description' => __('Décerné pour avoir atteint 100 points de gamification.'),
                'is_earned' => $student->points >= 100,
                'earned_at' => null // You could track this in a separate table if needed
            ],
            [
                'id' => '500_points',
                'title' => __('Certificat des 500 Points'),
                'description' => __('Décerné pour avoir atteint 500 points de gamification.'),
                'is_earned' => $student->points >= 500,
                'earned_at' => null
            ],
            [
                'id' => '10_books',
                'title' => __('Lecteur Assidu (10 livres)'),
                'description' => __('Décerné pour avoir lu 10 livres complètement.'),
                'is_earned' => $student->getCompletedBooksCount() >= 10,
                'earned_at' => null
            ],
        ];

        return view('student.certificates.index', compact('certificates'));
    }

    public function generate(Request $request, $type)
    {
        $student = auth()->user();

        $certificateTitle = '';
        $isEarned = false;

        if ($type === '100_points') {
            $certificateTitle = __('Certificat des 100 Points');
            $isEarned = $student->points >= 100;
        } elseif ($type === '500_points') {
            $certificateTitle = __('Certificat des 500 Points');
            $isEarned = $student->points >= 500;
        } elseif ($type === '10_books') {
            $certificateTitle = __('Lecteur Assidu');
            $isEarned = $student->getCompletedBooksCount() >= 10;
        }

        if (!$isEarned) {
            return redirect()->back()->with('error', __('Vous n\'avez pas encore débloqué ce certificat.'));
        }

        $data = [
            'studentName' => $student->first_name . ' ' . $student->last_name,
            'certificateTitle' => $certificateTitle,
            'date' => now()->format('d/m/Y'),
            'schoolName' => $student->school ? $student->school->name : config('app.name'),
        ];

        $pdf = Pdf::loadView('student.certificates.template', $data);
        // Set paper to landscape
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("certificat_{$type}.pdf");
    }
}
