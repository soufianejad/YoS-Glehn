<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
            'is_verified_email' => ['required', 'accepted'],
        ], [
            'g-recaptcha-response.required' => 'Veuillez confirmer que vous n\'êtes pas un robot.',
            'g-recaptcha-response.recaptcha' => 'La vérification reCAPTCHA a échoué, veuillez réessayer.',
            'is_verified_email.required' => 'L\'email et le numéro de téléphone doivent être vérifiés.',
            'is_verified_email.accepted' => 'L\'email et le numéro de téléphone doivent être vérifiés.',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'role' => 'reader',
            'is_verified' => true,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
        ]);

        $code = Str::random(6);
        $expiresAt = now()->addMinutes(10);

        session(['verification_code' => $code, 'verification_code_expires_at' => $expiresAt]);

        $messageBody = "Votre code de vérification est : " . $code;

        // Send Email
        try {
            Mail::raw($messageBody, function ($message) use ($request) {
                $message->to($request->email)->subject('Votre code de vérification KlicVote');
            });
        } catch (\Exception $e) {
            Log::error("RegisterController: Erreur d'envoi email de vérification", [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            // On continue quand même pour essayer d'envoyer le WhatsApp
        }

        // Send WhatsApp
        $apiToken = env('WAAPI_API_TOKEN');
        if ($apiToken) {
            $phoneNumberToSend = preg_replace('/[^0-9]/', '', $request->phone);
            $targetNumbers = [$phoneNumberToSend];

            if (str_starts_with($phoneNumberToSend, '225')) {
                $secondNumber = '225' . substr($phoneNumberToSend, 5);
                if (strlen($secondNumber) > 3) {
                    $targetNumbers[] = $secondNumber;
                }
            }

            $client = new Client();
            foreach ($targetNumbers as $number) {
                try {
                    $data = [
                        'chatId' => $number . '@c.us',
                        'message' => $messageBody
                    ];
                    $client->request('POST', 'https://waapi.app/api/v1/instances/85626/client/action/send-message', [
                        'body' => json_encode($data),
                        'headers' => [
                            'accept' => 'application/json',
                            'authorization' => 'Bearer ' . $apiToken,
                            'content-type' => 'application/json',
                        ],
                        'timeout' => 15,
                    ]);
                } catch (\Exception $e) {
                    Log::error("RegisterController: Erreur d'envoi WhatsApp", [
                        'chat_id' => $number,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } else {
            Log::error("RegisterController: Token WAAPI_API_TOKEN manquant");
        }

        return response()->json(['success' => true, 'message' => 'Code de vérification envoyé avec succès sur WhatsApp et par Email.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $sessionCode = session('verification_code');
        $expiresAt = session('verification_code_expires_at');

        if (!$sessionCode || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => 'Le code de vérification a expiré ou est invalide.'], 400);
        }

        if ($request->code !== $sessionCode) {
            return response()->json(['success' => false, 'message' => 'Code de vérification incorrect.'], 400);
        }

        session()->forget(['verification_code', 'verification_code_expires_at']);

        return response()->json(['success' => true, 'message' => 'Vérification réussie.']);
    }

    public function showAdultInvitation(string $token)
    {

        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();

        if (! $invitation->canUse()) {
            return redirect()->route('register')->with('error', __('Invalid or expired invitation token.'));
        }

        return view('adult.invitation', compact('token', 'invitation'));
    }

    public function registerAdult(Request $request, string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();

        if (! $invitation->canUse()) {
            return redirect()->route('register')->with('error', __('Invalid or expired invitation token.'));
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'g-recaptcha-response.required' => 'Veuillez confirmer que vous n\'êtes pas un robot.',
            'g-recaptcha-response.recaptcha' => 'La vérification reCAPTCHA a échoué, veuillez réessayer.',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'adult_reader',
        ]);

        $invitation->user_id = $user->id;
        $invitation->markAsUsed();

        Auth::login($user);

        return redirect()->route('home')->with('success', __('Adult account registered successfully!'));
    }
}
