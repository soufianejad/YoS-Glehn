<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Mail\VerificationCodeMail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
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

            if (!User::where('email', $request->email)->exists()) {
                return response()->json(['success' => false, 'message' => __('Cet email n\'existe pas dans notre base de données.')], 404);
            }

            session([
                "reset_code_email" => $code,
                "reset_code_expires_at_email" => $expiresAt,
                "reset_target_email" => $request->email
            ]);

            try {
                Mail::to($request->email)->send(new VerificationCodeMail($code));
                return response()->json(['success' => true, 'message' => __('Code envoyé par email avec succès.')]);
            } catch (\Exception $e) {
                Log::error("ForgotPasswordController: Erreur d'envoi email de réinitialisation", [
                    'email' => $request->email,
                    'error' => $e->getMessage()
                ]);
                return response()->json(['success' => false, 'message' => __('Erreur lors de l\'envoi de l\'email.')], 500);
            }
        } else if ($type === 'phone') {
            $request->validate(['phone' => ['required', 'string']]);

            if (!User::where('phone', $request->phone)->exists()) {
                return response()->json(['success' => false, 'message' => __('Ce numéro de téléphone n\'existe pas dans notre base de données.')], 404);
            }

            session([
                "reset_code_phone" => $code,
                "reset_code_expires_at_phone" => $expiresAt,
                "reset_target_phone" => $request->phone
            ]);

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

                        Log::info("WhatsApp Reset SUCCESS", [
                            'to' => $number,
                            'response' => $responseBody
                        ]);

                        if (isset($responseBody['messages']) && count($responseBody['messages']) > 0) {
                            $sent = true;
                        }
                    } catch (\Exception $e) {
                        Log::error("WhatsApp Reset ERROR", [
                            'chat_id' => $number,
                            'message' => $e->getMessage(),
                        ]);
                    }
                }

                if ($sent) {
                    return response()->json(['success' => true, 'message' => __('Code envoyé sur WhatsApp avec succès.')]);
                } else {
                    return response()->json(['success' => false, 'message' => __('Impossible d\'envoyer le message WhatsApp.')], 500);
                }
            } else {
                Log::error("ForgotPasswordController: Tokens WhatsApp manquants");
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
        $sessionCode = session("reset_code_{$type}");
        $expiresAt = session("reset_code_expires_at_{$type}");
        $target = session("reset_target_{$type}");

        if (!$sessionCode || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['success' => false, 'message' => __('Le code de vérification a expiré ou est invalide.')], 400);
        }

        if ($request->code !== $sessionCode) {
            return response()->json(['success' => false, 'message' => __('Code de vérification incorrect.')], 400);
        }

        session(["reset_verified_{$type}" => $target]);
        session()->forget(["reset_code_{$type}", "reset_code_expires_at_{$type}", "reset_target_{$type}"]);

        return response()->json(['success' => true, 'message' => __('Vérification réussie.')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'g-recaptcha-response.required' => __('Veuillez confirmer que vous n\'êtes pas un robot.'),
            'g-recaptcha-response.recaptcha' => __('La vérification reCAPTCHA a échoué, veuillez réessayer.'),
        ]);

        $type = $request->type;
        if ($type === 'email') {
            $request->validate(['email' => ['required', 'email']]);
            $targetValue = $request->email;
        } else {
            $request->validate(['phone' => ['required', 'string']]);
            $targetValue = $request->phone;
        }

        if (session("reset_verified_{$type}") !== $targetValue || empty($targetValue)) {
            return back()->withErrors(['verification' => __('Veuillez vérifier votre ' . ($type === 'email' ? 'email' : 'numéro de téléphone') . ' avant de réinitialiser le mot de passe.')])->withInput();
        }

        $user = User::where($type, $targetValue)->first();

        if (!$user) {
            return back()->withErrors(['verification' => __('Utilisateur non trouvé.')])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget("reset_verified_{$type}");

        return redirect()->route('login')->with('success', __('Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'));
    }
}
