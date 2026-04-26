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
use Illuminate\Support\Facades\RateLimiter;
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
            return back()->withErrors(['email' => __('L\'adresse email n\'a pas été vérifiée ou a été modifiée après vérification.')])->withInput();
        }

        if (session('verified_phone') !== $request->phone) {
            return back()->withErrors(['phone' => __('Le numéro de téléphone n\'a pas été vérifié ou a été modifié après vérification.')])->withInput();
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
        $ipKey = 'send-code-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return response()->json(['success' => false, 'message' => __('Trop de tentatives. Veuillez patienter :seconds secondes.', ['seconds' => $seconds])], 429);
        }

        if ($type === 'email') {
             $targetKey = 'send-code-target:' . $request->email;
        } else {
             $targetKey = 'send-code-target:' . $request->phone;
        }

        if (RateLimiter::tooManyAttempts($targetKey, 3)) {
            $seconds = RateLimiter::availableIn($targetKey);
            return response()->json(['success' => false, 'message' => __('Trop de tentatives pour ce contact. Veuillez patienter :seconds secondes.', ['seconds' => $seconds])], 429);
        }


        $code = Str::random(6);
        $expiresAt = now()->addMinutes(10);

        if ($type === 'email') {
            $request->validate(['email' => ['required', 'email']]);

            if (User::where('email', $request->email)->exists()) {
                return response()->json(['success' => false, 'message' => __('Cet email est déjà utilisé.')], 422);
            }

            session([
                "verification_code_email" => $code,
                "verification_code_expires_at_email" => $expiresAt,
                "verification_target_email" => $request->email
            ]);

            try {
                RateLimiter::hit($ipKey, 300); // 5 minutes
                RateLimiter::hit($targetKey, 900); // 15 minutes
                Mail::to($request->email)->send(new VerificationCodeMail($code));
                return response()->json(['success' => true, 'message' => __('Code envoyé par email avec succès.')]);
            } catch (\Exception $e) {
                Log::error("RegisterController: Erreur d'envoi email de vérification", [
                    'email' => $request->email,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['success' => false, 'message' => __('Erreur lors de l\'envoi de l\'email.')], 500);
            }
        } else if ($type === 'phone') {
            $request->validate(['phone' => ['required', 'string']]);
            session([
                "verification_code_phone" => $code,
                "verification_code_expires_at_phone" => $expiresAt,
                "verification_target_phone" => $request->phone
            ]);

            RateLimiter::hit($ipKey, 300); // 5 minutes
            RateLimiter::hit($targetKey, 900); // 15 minutes

            $apiToken = config('services.whatsapp.token');
            $phoneNumberId = config('services.whatsapp.phone_number_id');

            if ($apiToken && $phoneNumberId) {
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
            'messaging_product' => 'whatsapp',
            'to' => $number,
            'type' => 'template',
            'template' => [
                'name' => 'otp',
                'language' => [
                    'code' => 'en_us'
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => (string) $code
                            ]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => (string) $code
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $client->request('POST', 'https://graph.facebook.com/v21.0/' . $phoneNumberId . '/messages', [
            'body' => json_encode($data),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        Log::info("WhatsApp SUCCESS", [
            'to' => $number,
            'response' => $responseBody
        ]);

        if (isset($responseBody['messages']) && count($responseBody['messages']) > 0) {
            $sent = true;
        }

    } catch (\GuzzleHttp\Exception\ClientException $e) {

        // 🔥 ERREUR API (400, 401, etc.)
        $response = $e->getResponse();
        $body = $response ? $response->getBody()->getContents() : null;

        Log::error("WhatsApp CLIENT ERROR", [
            'chat_id' => $number,
            'status' => $response ? $response->getStatusCode() : null,
            'body' => $body,
            'message' => $e->getMessage()
        ]);

    } catch (\GuzzleHttp\Exception\RequestException $e) {

        // 🔥 ERREUR RESEAU
        $response = $e->getResponse();
        $body = $response ? $response->getBody()->getContents() : null;

        Log::error("WhatsApp REQUEST ERROR", [
            'chat_id' => $number,
            'body' => $body,
            'message' => $e->getMessage()
        ]);

    } catch (\Exception $e) {

        // 🔥 AUTRES ERREURS
        Log::error("WhatsApp GENERAL ERROR", [
            'chat_id' => $number,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

                if ($sent) {
                    return response()->json(['success' => true, 'message' => __('Code envoyé sur WhatsApp avec succès.')]);
                } else {
                    return response()->json(['success' => false, 'message' => __('Impossible d\'envoyer le message WhatsApp.')], 500);
                }
            } else {
                Log::error("RegisterController: Tokens WhatsApp manquants");
                return response()->json(['success' => false, 'message' => __('Service WhatsApp non configuré.')], 500);
            }
        }

        return response()->json(['success' => false, 'message' => __('Type invalide.')], 400);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone'],
            'code' => ['required', 'string'],
        ]);

        $type = $request->type;
        $verifyKey = 'verify-code-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($verifyKey, 5)) {
            $seconds = RateLimiter::availableIn($verifyKey);
            return response()->json(['success' => false, 'message' => __('Trop de tentatives de vérification. Veuillez patienter :seconds secondes.', ['seconds' => $seconds])], 429);
        }

        $sessionCode = session("verification_code_{$type}");
        $expiresAt = session("verification_code_expires_at_{$type}");
        $target = session("verification_target_{$type}");

        if (!$sessionCode || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => __('Le code de vérification a expiré ou est invalide.')], 400);
        }

        if ($request->code !== $sessionCode) {
            RateLimiter::hit($verifyKey, 60); // block 1 minute if trying to brute force
            return response()->json(['success' => false, 'message' => __('Code de vérification incorrect.')], 400);
        }

        session(["verified_{$type}" => $target]);
        session()->forget(["verification_code_{$type}", "verification_code_expires_at_{$type}", "verification_target_{$type}"]);
        RateLimiter::clear($verifyKey);

        return response()->json(['success' => true, 'message' => __('Vérification réussie.')]);
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
