<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Get Privacy Policy
     *
     * @return JsonResponse
     */
    public function getPrivacyPolicy(): JsonResponse
    {
        $page = Page::where('key', 'privacy-policy')->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Privacy Policy not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $page,
        ]);
    }

    /**
     * Get Terms & Conditions
     *
     * @return JsonResponse
     */
    public function getTermsConditions(): JsonResponse
    {
        $page = Page::where('key', 'terms-conditions')->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terms & Conditions not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $page,
        ]);
    }
}
