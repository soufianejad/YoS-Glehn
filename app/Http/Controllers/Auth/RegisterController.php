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
use App\Mail\VerificationCodeMail;

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
        ], [
            'g-recaptcha-response.required' => 'Veuillez confirmer que vous n\'êtes pas un robot.',
            'g-recaptcha-response.recaptcha' => 'La vérification reCAPTCHA a échoué, veuillez réessayer.',
        ]);

        if (session('verified_email') !== $request->email) {
            return back()->withErrors(['email' => 'L\'adresse email n\'a pas été vérifiée ou a été modifiée après vérification.'])->withInput();
        }

        if (session('verified_phone') !== $request->phone) {
            return back()->withErrors(['phone' => 'Le numéro de téléphone n\'a pas été vérifié ou a été modifié après vérification.'])->withInput();
        }

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
            'type' => ['required', 'in:email,phone'],
        ]);

        $type = $request->type;
        $code = Str::random(6);
        $expiresAt = now()->addMinutes(10);

        if ($type === 'email') {
            $request->validate(['email' => ['required', 'email']]);
            session([
                "verification_code_email" => $code,
                "verification_code_expires_at_email" => $expiresAt,
                "verification_target_email" => $request->email
            ]);

            try {
                Mail::to($request->email)->send(new VerificationCodeMail($code));
                return response()->json(['success' => true, 'message' => 'Code envoyé par email avec succès.']);
            } catch (\Exception $e) {
                Log::error("RegisterController: Erreur d'envoi email de vérification", [
                    'email' => $request->email,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email.'], 500);
            }
        } else if ($type === 'phone') {
            $request->validate(['phone' => ['required', 'string']]);
            session([
                "verification_code_phone" => $code,
                "verification_code_expires_at_phone" => $expiresAt,
                "verification_target_phone" => $request->phone
            ]);

            $messageBody = "Votre code de vérification pour KlicVote est : " . $code;

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
                $sent = false;
                foreach ($targetNumbers as $number) {
                    try {
                        $data = [
                            'chatId' => $number . '@c.us',
                            'message' => $messageBody
                        ];
                        $response = $client->request('POST', 'https://waapi.app/api/v1/instances/85626/client/action/send-message', [
                            'body' => json_encode($data),
                            'headers' => [
                                'accept' => 'application/json',
                                'authorization' => 'Bearer ' . $apiToken,
                                'content-type' => 'application/json',
                            ],
                            'timeout' => 15,
                        ]);

                        $responseBody = json_decode($response->getBody()->getContents(), true);
                        if (isset($responseBody['data']['status']) && $responseBody['data']['status'] === 'success') {
                            $sent = true;
                        }
                    } catch (\Exception $e) {
                        Log::error("RegisterController: Erreur d'envoi WhatsApp", [
                            'chat_id' => $number,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($sent) {
                    return response()->json(['success' => true, 'message' => 'Code envoyé sur WhatsApp avec succès.']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Impossible d\'envoyer le message WhatsApp.'], 500);
                }
            } else {
                Log::error("RegisterController: Token WAAPI_API_TOKEN manquant");
                return response()->json(['success' => false, 'message' => 'Service WhatsApp non configuré.'], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Type invalide.'], 400);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone'],
            'code' => ['required', 'string'],
        ]);

        $type = $request->type;
        $sessionCode = session("verification_code_{$type}");
        $expiresAt = session("verification_code_expires_at_{$type}");
        $target = session("verification_target_{$type}");

        if (!$sessionCode || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => 'Le code de vérification a expiré ou est invalide.'], 400);
        }

        if ($request->code !== $sessionCode) {
            return response()->json(['success' => false, 'message' => 'Code de vérification incorrect.'], 400);
        }

        session(["verified_{$type}" => $target]);
        session()->forget(["verification_code_{$type}", "verification_code_expires_at_{$type}", "verification_target_{$type}"]);

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
