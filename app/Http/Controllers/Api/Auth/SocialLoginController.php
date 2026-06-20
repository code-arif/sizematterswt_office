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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    use ApiResponse;

    /*
    |--------------------------------------------------------------------------
    | Google Social Login
    |--------------------------------------------------------------------------
    |
    | Handles Google OAuth login for both mobile and web clients.
    | - Mobile: Client sends access_token obtained from Google Sign-In SDK
    | - Web: Standard OAuth2 redirect flow
    |
    */

    /**
     * Handle Google login via access token (for mobile clients).
     *
     * POST /api/v1/auth/google
     * Body: { "access_token": "google_access_token" }
     *
     * The client obtains this access_token from Google Sign-In SDK on mobile.
     * The server verifies it with Google's OAuth2 API and creates/finds the user.
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray(), 'Validation failed', 422);
        }

        try {
            // Verify the access token with Google's OAuth2 API
            $googleUser = $this->getGoogleUserFromToken($request->access_token);

            if (!$googleUser || empty($googleUser['email'])) {
                return $this->error(null, 'Unable to authenticate with Google. Invalid or expired token.', 401);
            }

            return $this->handleSocialLogin(
                'google',
                $googleUser['sub'] ?? $googleUser['id'],
                $googleUser['email'],
                $googleUser['name'] ?? null,
                $googleUser['picture'] ?? null
            );
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return $this->error(null, 'Google authentication failed. Please try again.', 500);
        }
    }

    /**
     * Google OAuth redirect for web clients.
     *
     * GET /api/v1/auth/google/redirect
     */
    public function googleRedirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Google OAuth callback for web clients.
     *
     * GET /api/v1/auth/google/callback
     */
    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $result = $this->handleSocialLogin(
                'google',
                $googleUser->getId(),
                $googleUser->getEmail(),
                $googleUser->getName(),
                $googleUser->getAvatar()
            );

            // For web callback, redirect to frontend with token
            $frontendUrl = config('app.frontend_url', '/');
            $data = json_decode($result->getContent(), true);

            if ($data['success']) {
                $token = $data['data']['token'];
                return redirect("{$frontendUrl}/auth/social-callback?token={$token}&token_type=bearer&provider=google");
            }

            return redirect("{$frontendUrl}/auth/social-callback?error=authentication_failed");
        } catch (Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            $frontendUrl = config('app.frontend_url', '/');
            return redirect("{$frontendUrl}/auth/social-callback?error=" . urlencode($e->getMessage()));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Apple Social Login
    |--------------------------------------------------------------------------
    |
    | Handles Apple Sign-In for both mobile and web clients.
    | Apple uses identity_token (JWT) instead of traditional OAuth2 access_token.
    |
    | For mobile: Client sends identity_token and optionally name from Apple Sign-In
    | For web: Standard Apple OAuth2 redirect flow via Socialite
    |
    */

    /**
     * Handle Apple login via identity token (for mobile clients).
     *
     * POST /api/v1/auth/apple
     * Body: {
     *     "identity_token": "apple_jwt_identity_token",
     *     "user": {                          // optional, only on first sign-in
     *         "name": "John Doe",
     *         "email": "john@example.com"
     *     }
     * }
     *
     * The identity_token is a JWT issued by Apple after successful Sign-In.
     * We verify it against Apple's public keys using firebase/php-jwt.
     */
    public function appleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity_token' => 'required|string',
            'user.name'      => 'nullable|string|max:100',
            'user.email'     => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->toArray(), 'Validation failed', 422);
        }

        try {
            // Decode and verify the Apple identity token
            $applePayload = $this->verifyAppleIdentityToken($request->identity_token);

            if (!$applePayload || empty($applePayload['sub'])) {
                return $this->error(null, 'Unable to authenticate with Apple. Invalid or expired token.', 401);
            }

            $appleId = $applePayload['sub'];
            $email = $applePayload['email'] ?? $request->input('user.email');
            $name = $request->input('user.name');

            return $this->handleSocialLogin(
                'apple',
                $appleId,
                $email,
                $name,
                null // Apple doesn't provide avatar URLs
            );
        } catch (Exception $e) {
            Log::error('Apple login error: ' . $e->getMessage());
            return $this->error(null, 'Apple authentication failed. Please try again.', 500);
        }
    }

    /**
     * Apple OAuth redirect for web clients.
     *
     * GET /api/v1/auth/apple/redirect
     */
    public function appleRedirect()
    {
        return Socialite::driver('apple')
            ->stateless()
            ->redirect();
    }

    /**
     * Apple OAuth callback for web clients.
     *
     * GET /api/v1/auth/apple/callback
     */
    public function appleCallback()
    {
        try {
            $appleUser = Socialite::driver('apple')->stateless()->user();

            $result = $this->handleSocialLogin(
                'apple',
                $appleUser->getId(),
                $appleUser->getEmail(),
                $appleUser->getName(),
                null
            );

            // For web callback, redirect to frontend with token
            $frontendUrl = config('app.frontend_url', '/');
            $data = json_decode($result->getContent(), true);

            if ($data['success']) {
                $token = $data['data']['token'];
                return redirect("{$frontendUrl}/auth/social-callback?token={$token}&token_type=bearer&provider=apple");
            }

            return redirect("{$frontendUrl}/auth/social-callback?error=authentication_failed");
        } catch (Exception $e) {
            Log::error('Apple callback error: ' . $e->getMessage());
            $frontendUrl = config('app.frontend_url', '/');
            return redirect("{$frontendUrl}/auth/social-callback?error=" . urlencode($e->getMessage()));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Social Login Handler
    |--------------------------------------------------------------------------
    |
    | Common logic for handling both Google and Apple logins.
    | Creates new user if not exists, or logs in existing user.
    |
    */

    /**
     * Handle social login — create user if not exists, or login existing user.
     *
     * @param string $provider    'google' or 'apple'
     * @param string $providerId  The unique ID from the social provider
     * @param string|null $email  User's email address
     * @param string|null $name   User's display name
     * @param string|null $avatar User's avatar URL
     */
    private function handleSocialLogin(
        string $provider,
        string $providerId,
        ?string $email,
        ?string $name,
        ?string $avatar
    ) {
        DB::beginTransaction();

        try {
            // 1. Check if user exists with this provider + provider_id
            $user = User::with('profile')
                ->where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            // 2. If not found by provider, check if email exists (link account)
            if (!$user && !empty($email)) {
                $user = User::with('profile')
                    ->where('email', strtolower($email))
                    ->first();

                if ($user) {
                    // Link the social provider to existing account
                    $user->update([
                        'provider'    => $provider,
                        'provider_id' => $providerId,
                    ]);

                    // Update avatar if not already set
                    if ($avatar && !$user->profile?->avatar) {
                        $this->updateUserAvatar($user, $avatar);
                    }
                }
            }

            // 3. Create new user if not found
            if (!$user) {
                // For Apple with hidden email, we need a placeholder
                if (empty($email)) {
                    $email = strtolower("apple_{$providerId}@privaterelay.appleid.com");
                }

                $user = User::create([
                    'email'             => strtolower($email),
                    'provider'          => $provider,
                    'provider_id'       => $providerId,
                    'status'            => 'active',
                    'email_verified_at' => now(), // Social providers verify email
                ]);

                // Create profile
                $displayName = $name ?? strtolower(str_replace(' ', '_', $email));
                $slug = Helper::generateSlug($displayName);
                $username = Helper::generateUsername($displayName);

                Profile::create([
                    'user_id'  => $user->id,
                    'name'     => $name,
                    'username' => $username,
                    'slug'     => $slug,
                ]);

                // Assign default user role (role_id = 3, same as manual registration)
                DB::table('model_has_roles')->insert([
                    'role_id'    => 3,
                    'model_type' => User::class,
                    'model_id'   => $user->id,
                ]);

                // Set avatar if provided
                if ($avatar) {
                    $this->updateUserAvatar($user, $avatar);
                }

                // Reload with relationships
                $user->load('profile');
            }

            // 4. Check if user is active
            if ($user->status !== 'active') {
                DB::rollBack();
                return $this->error(null, 'User account is not active', 403);
            }

            DB::commit();

            // 5. Refresh roles so JWT claims include the correct role
            $user->load('roles');

            // 6. Generate JWT token
            $token = auth('api')->login($user);
            $expiresIn = auth('api')->factory()->getTTL() * 60;

            // 7. Return success response (same format as LoginController)
            return $this->success(
                'Login successful',
                [
                    'user'       => new UserResource($user),
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $expiresIn,
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Social login ({$provider}) error: " . $e->getMessage());
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Google Token Verification
    |--------------------------------------------------------------------------
    */

    /**
     * Verify a Google access token and retrieve user information.
     *
     * Uses Google's OAuth2 TokenInfo endpoint to validate the token
     * and the UserInfo endpoint to get user details.
     *
     * @param string $accessToken The Google access token
     * @return array|null User information from Google
     */
    private function getGoogleUserFromToken(string $accessToken): ?array
    {
        // Verify the token and get basic info from Google's tokeninfo endpoint
        $tokenInfoResponse = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'access_token' => $accessToken,
        ]);

        if (!$tokenInfoResponse->successful()) {
            return null;
        }

        $tokenInfo = $tokenInfoResponse->json();

        // Verify the token is for our app (audience check)
        $expectedClientId = config('services.google.client_id');
        if (isset($tokenInfo['aud']) && $tokenInfo['aud'] !== $expectedClientId) {
            Log::warning('Google token audience mismatch', [
                'expected' => $expectedClientId,
                'got' => $tokenInfo['aud'] ?? 'null',
            ]);
            return null;
        }

        // Get full user info from Google's userinfo endpoint
        $userInfoResponse = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if (!$userInfoResponse->successful()) {
            // Fallback: use tokeninfo data
            return [
                'id'  => $tokenInfo['sub'] ?? null,
                'sub' => $tokenInfo['sub'] ?? null,
                'email' => $tokenInfo['email'] ?? null,
                'name'  => null,
                'picture' => null,
            ];
        }

        return $userInfoResponse->json();
    }

    /*
    |--------------------------------------------------------------------------
    | Apple Token Verification (using firebase/php-jwt)
    |--------------------------------------------------------------------------
    */

    /**
     * Verify an Apple identity token (JWT) against Apple's public keys.
     *
     * Apple issues identity tokens as JWTs signed with ES256.
     * We fetch Apple's JWKS and verify the token using firebase/php-jwt.
     *
     * @param string $identityToken The Apple identity token JWT
     * @return array|null Decoded token payload
     */
    private function verifyAppleIdentityToken(string $identityToken): ?array
    {
        try {
            // Decode the token header to get the key ID (kid)
            $headerB64 = explode('.', $identityToken)[0] ?? '';
            $header = json_decode(base64_decode(strtr($headerB64, '-_', '+/')), true);

            if (!$header || empty($header['kid'])) {
                Log::warning('Apple token missing kid in header');
                return null;
            }

            $kid = $header['kid'];

            // Fetch Apple's public JWKS (cached for 24 hours)
            $appleKeys = $this->getApplePublicKeys();
            if (!$appleKeys) {
                Log::error('Failed to fetch Apple public keys');
                return null;
            }

            // Build the key set in firebase/php-jwt format
            $keySet = JWK::parseKeySet($appleKeys);

            // Find the matching key
            if (!isset($keySet[$kid])) {
                Log::warning('Apple public key not found for kid: ' . $kid);
                return null;
            }

            // Decode and verify the token
            $decoded = JWT::decode(
                $identityToken,
                $keySet[$kid],
                ['ES256']
            );

            $payload = (array) $decoded;

            // Validate issuer
            $validIssuers = ['https://appleid.apple.com', 'https://appleid.apple.com/auth'];
            if (!in_array($payload['iss'] ?? '', $validIssuers)) {
                Log::warning('Apple token invalid issuer', ['iss' => $payload['iss'] ?? 'null']);
                return null;
            }

            // Validate audience (our app's bundle ID or service ID)
            $validAudiences = array_filter([
                config('services.apple.client_id'),
                config('services.apple.bundle_id'),
            ]);
            if (empty($validAudiences)) {
                Log::error('Apple OAuth is not configured: set APPLE_CLIENT_ID or APPLE_BUNDLE_ID in .env');
                return null;
            }
            if (!in_array($payload['aud'] ?? '', $validAudiences)) {
                Log::warning('Apple token invalid audience', ['aud' => $payload['aud'] ?? 'null']);
                return null;
            }

            return $payload;
        } catch (\Firebase\JWT\ExpiredException $e) {
            Log::warning('Apple token expired: ' . $e->getMessage());
            return null;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            Log::warning('Apple token signature invalid: ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            Log::error('Apple token verification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch Apple's public JWKS (JSON Web Key Set).
     *
     * Apple publishes their public keys at:
     * https://appleid.apple.com/auth/keys
     *
     * @return array|null Apple's public keys
     */
    private function getApplePublicKeys(): ?array
    {
        $cacheKey = 'apple_public_keys';
        $cached = cache()->get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)->get('https://appleid.apple.com/auth/keys');

            if (!$response->successful()) {
                return null;
            }

            $keys = $response->json();

            // Cache for 24 hours (Apple rotates keys infrequently)
            cache()->put($cacheKey, $keys, now()->addHours(24));

            return $keys;
        } catch (Exception $e) {
            Log::error('Failed to fetch Apple public keys: ' . $e->getMessage());
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Avatar Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Download and store a user's avatar from a URL.
     */
    private function updateUserAvatar(User $user, string $avatarUrl): void
    {
        try {
            $response = Http::timeout(10)->get($avatarUrl);

            if ($response->successful()) {
                $extension = pathinfo(parse_url($avatarUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = 'avatars/' . $user->id . '_' . time() . '.' . $extension;




                Storage::disk('public')->put($filename, $response->body());

                if ($user->profile) {
                    $user->profile->update(['avatar' => $filename]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to download avatar for user ' . $user->id . ': ' . $e->getMessage());
        }
    }
}
