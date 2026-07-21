<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class SupportPageController extends Controller
{
    /**
     * Display the public support page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $page = Page::where('key', 'support-content')
            ->where('is_active', true)
            ->first();

        // If no content has been saved yet, create a placeholder
        if (!$page) {
            $page = new Page();
            $page->title = 'Support Center';
            $page->content = '<p class="text-center py-5">
                <i class="ri-customer-service-2-line" style="font-size: 3rem; opacity: 0.5;"></i>
                <br><br>
                <strong>Content coming soon.</strong>
                <br>
                We are currently preparing this page with helpful information.
                <br>
                Please check back later.
            </p>';
        }

        return view('web.pages.support', compact('page'));
    }
}
