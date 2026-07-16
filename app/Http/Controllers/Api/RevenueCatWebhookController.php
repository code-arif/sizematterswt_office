<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RevenueCatWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Optional: Secure your webhook by checking an Authorization header
        $secret = env('REVENUECAT_WEBHOOK_SECRET');
        // if ($secret) {
        //     Log::warning('RevenueCat Webhook: Invalid authorization header');
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        Log::info($request->all());

        $authorizationHeader = $request->header('Authorization', '');

        if (!$secret || !hash_equals($secret, $authorizationHeader)) {
            Log::warning('Unauthorized RevenueCat webhook request.');

            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // Ensure this is an event from RevenueCat
        if (!isset($payload['event'])) {
            return response()->json(['message' => 'Ignored, no event data'], 200);
        }

        $event = $payload['event'];
        $eventType = $event['type'] ?? '';
        $appUserId = $event['app_user_id'] ?? null;

        // Try to find the user in our database using the app_user_id.
        // Assuming app_user_id is something like "user_{id}" or just the "{id}"
        // The frontend should set this properly via Purchases.configure()
        $userId = $this->extractUserId($appUserId);
        $user = User::find($userId);

        if (!$user) {
            Log::error('RevenueCat Webhook: User not found for app_user_id: ' . $appUserId);
            return response()->json(['message' => 'User not found'], 200); // return 200 so RC doesn't retry
        }

        $productId = $event['product_id'] ?? null;
        $entitlementId = 'premium_access'; // Using the default we discussed
        $expiresAtMs = $event['expiration_at_ms'] ?? null;

        $expiresAt = $expiresAtMs ? Carbon::createFromTimestampMs($expiresAtMs) : null;

        switch ($eventType) {
            case 'INITIAL_PURCHASE':
            case 'RENEWAL':
            case 'NON_RENEWING_PURCHASE':
            case 'UNCANCELLATION':
                $this->updateOrCreateSubscription($user, $appUserId, $entitlementId, $productId, 'active', $expiresAt);
                break;

            case 'CANCELLATION':
                // Still active until expires_at, but marked as cancelled
                $this->updateOrCreateSubscription($user, $appUserId, $entitlementId, $productId, 'cancelled', $expiresAt);
                break;

            case 'EXPIRATION':
                $this->updateOrCreateSubscription($user, $appUserId, $entitlementId, $productId, 'expired', $expiresAt);
                break;

            case 'BILLING_ISSUE':
                // Could optionally mark as 'billing_issue' or wait for EXPIRATION
                Log::info("RevenueCat Webhook: Billing issue for user $userId");
                break;

            default:
                Log::info("RevenueCat Webhook: Unhandled event type $eventType for user $userId");
                break;
        }

        return response()->json(['message' => 'Success'], 200);
    }

    private function updateOrCreateSubscription($user, $appUserId, $entitlementId, $productId, $status, $expiresAt)
    {
        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'revenuecat_app_user_id' => $appUserId,
                'entitlement_id' => $entitlementId,
                'product_id' => $productId,
                'status' => $status,
                'expires_at' => $expiresAt,
            ]
        );
    }

    private function extractUserId($appUserId)
    {
        if (!$appUserId) return null;

        // If your frontend sends "user_123", we extract 123.
        // If it just sends "123", we use it as is.
        if (str_starts_with($appUserId, 'user_')) {
            return str_replace('user_', '', $appUserId);
        }

        return $appUserId;
    }
}
