<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Google\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class V2SocialLoginController extends Controller
{
    use ApiResponse;

    public function socialSignin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required',
            'provider' => 'required|in:google,facebook,apple',
            'name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray(), 'Validation failed', 422);
        }

        try {
            $provider = $request->provider;

            if ($provider === 'apple') {
                $socialUser = $this->verifyAppleToken($request->access_token, $request->name);
            } elseif ($provider === 'google') {
                $socialUser = $this->verifyGoogleToken($request->access_token);
            } else {
                $socialUserObj = Socialite::driver($provider)->stateless()->userFromToken($request->access_token);
                $socialUser = $socialUserObj ? [
                    'email'  => $socialUserObj->getEmail(),
                    'name'   => $socialUserObj->getName(),
                    'avatar' => $socialUserObj->getAvatar(),
                ] : null;
            }

            if (!$socialUser || empty($socialUser['email'])) {
                return $this->error(null, 'Unauthorized', 401);
            }

            // Check existing user (including soft deleted)
            $user = User::where('email', $socialUser['email'])
                ->first();

            if ($user && $user->deleted_at) {
                return $this->error(null, 'Your account has been deleted.', 410);
            }

            $isNewUser = false;

            if (!$user) {
                $user = User::create([
                    'email' => $socialUser['email'],
                    'password' => bcrypt(Str::random(16)),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                $displayName = $socialUser['name'] ?? $socialUser['email'] ?? 'User';
                $slug = Helper::generateSlug($displayName);
                $username = Helper::generateUsername($displayName);

                $profileData = [
                    'user_id' => $user->id,
                    'name' => $displayName,
                    'username' => $username,
                    'slug' => $slug,
                ];

                if (!empty($socialUser['avatar'])) {
                    $profileData['avatar'] = $socialUser['avatar'];
                }

                Profile::create($profileData);

                $isNewUser = true;
            } elseif (!$user->profile) {
                $displayName = $socialUser['name'] ?? $socialUser['email'] ?? 'User';
                $slug = Helper::generateSlug($displayName);
                $username = Helper::generateUsername($displayName);

                $profileData = [
                    'user_id' => $user->id,
                    'name' => $displayName,
                    'username' => $username,
                    'slug' => $slug,
                ];

                if (!empty($socialUser['avatar'])) {
                    $profileData['avatar'] = $socialUser['avatar'];
                }

                Profile::create($profileData);
            }

            Auth::login($user);
            $token = auth('api')->login($user);
            $expiresIn = auth('api')->factory()->getTTL() * 60;

            return $this->success(
                'User logged in successfully.',
                [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $expiresIn,
                    'is_new_user' => $isNewUser,
                ]
            );
        } catch (Exception $e) {
            return $this->error(['exception' => $e->getMessage()], 'Something went wrong', 500);
        }
    }

    /**
     * VERIFY APPLE TOKEN
     */
    private function verifyAppleToken($idToken, $name = null)
    {
        // Get Apple public keys
        $response = Http::get('https://appleid.apple.com/auth/keys');

        if (!$response->ok()) {
            throw new Exception('Unable to fetch Apple public keys');
        }

        $keys = $response->json();

        // Decode token header
        $header = json_decode(base64_decode(explode('.', $idToken)[0]), true);

        $kid = $header['kid'];

        // Find matching key
        $key = collect($keys['keys'])->firstWhere('kid', $kid);

        if (!$key) {
            throw new Exception('Invalid Apple public key');
        }

        // Convert JWK to PEM
        $publicKeys = JWK::parseKeySet(['keys' => [$key]]);

        // Decode JWT
        $decoded = JWT::decode($idToken, $publicKeys);

        return [
            'email'  => $decoded->email ?? null,
            'name'   => $name ?? 'Apple User',
            'avatar' => null,
        ];
    }
    /**
     * VERIFY GOOGLE TOKEN
     */
    private function verifyGoogleToken($token)
    {
        // If the token has 3 segments separated by dots, it's likely a JWT ID Token
        if (substr_count($token, '.') === 2) {
            $client = new Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($token);

            if ($payload) {
                return [
                    'email'  => $payload['email'],
                    'name'   => $payload['name'] ?? 'Google User',
                    'avatar' => $payload['picture'] ?? null,
                ];
            }

            throw new Exception('Invalid Google ID token');
        }

        // Otherwise, assume it's an Access Token and use Socialite to verify it
        try {
            $socialUserObj = Socialite::driver('google')->stateless()->userFromToken($token);

            if ($socialUserObj) {
                return [
                    'email'  => $socialUserObj->getEmail(),
                    'name'   => $socialUserObj->getName(),
                    'avatar' => $socialUserObj->getAvatar(),
                ];
            }
        } catch (Exception $e) {
            throw new Exception('Invalid Google Access token: ' . $e->getMessage());
        }

        throw new Exception('Invalid Google token');
    }
}
