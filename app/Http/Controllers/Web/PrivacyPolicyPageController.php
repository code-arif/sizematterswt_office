<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PrivacyPolicyPageController extends Controller
{
    /**
     * Display the public privacy policy page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $page = Page::where('key', 'privacy-policyy')
            ->where('is_active', true)
            ->first();

        // If no content has been saved yet, create a placeholder
        if (!$page) {
            $page = new Page();
            $page->title = 'Privacy Policy';
            $page->content = '<p class="text-center py-5">
                <i class="ri-shield-line" style="font-size: 3rem; opacity: 0.5;"></i>
                <br><br>
                <strong>Content coming soon.</strong>
                <br>
                We are currently preparing our Privacy Policy.
                <br>
                Please check back later.
            </p>';
        }

        return view('web.pages.privacy-policy', compact('page'));
    }
}
