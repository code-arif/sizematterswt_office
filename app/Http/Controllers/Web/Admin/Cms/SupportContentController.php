<?php

namespace App\Http\Controllers\Web\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportContentController extends Controller
{
    /**
     * Show the support content management page.
     */
    public function index()
    {
        $page = Page::where('key', 'support-content')->first();
        return view('web.admin.support-content.index', compact('page'));
    }

    /**
     * Show the edit form (same as index).
     */
    public function edit()
    {
        return $this->index();
    }

    /**
     * Store or update the support content.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        Page::updateOrCreate(
            ['key' => 'support-content'],
            [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                // 'meta_title'       => $request->meta_title,
                // 'meta_description' => $request->meta_description,
            ]
        );

        return response()->json(['message' => 'Support Content updated successfully.']);
    }

    /**
     * Update via PATCH (delegates to store).
     */
    public function update(Request $request, $id)
    {
        return $this->store($request);
    }
}
