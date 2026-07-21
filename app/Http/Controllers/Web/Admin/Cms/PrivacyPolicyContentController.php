<?php

namespace App\Http\Controllers\Web\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrivacyPolicyContentController extends Controller
{
    /**
     * Show the privacy policy content management page.
     */
    public function index()
    {
        $page = Page::where('key', 'privacy-policyy')->first();
        return view('web.admin.privacy-policy-content.index', compact('page'));
    }

    /**
     * Show the edit form (same as index).
     */
    public function edit()
    {
        return $this->index();
    }

    /**
     * Store or update the privacy policy content.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        Page::updateOrCreate(
            ['key' => 'privacy-policyy'],
            [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                // 'meta_title'       => $request->meta_title,
                // 'meta_description' => $request->meta_description,
            ]
        );

        return response()->json(['message' => 'Privacy Policy updated successfully.']);
    }

    /**
     * Update via PATCH (delegates to store).
     */
    public function update(Request $request, $id)
    {
        return $this->store($request);
    }
}
