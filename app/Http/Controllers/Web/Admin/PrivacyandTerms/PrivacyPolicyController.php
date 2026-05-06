<?php

namespace App\Http\Controllers\Web\Admin\PrivacyandTerms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;
use Illuminate\Support\Str;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $page = Page::where('key', 'privacy-policy')->first();
        return view('web.admin.privacy-policy.index', compact('page'));
    }

    public function edit()
    {
        $page = Page::where('key', 'privacy-policy')->first();
        return view('web.admin.privacy-policy.index', compact('page'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        Page::updateOrCreate(
            ['key' => 'privacy-policy'],
            [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]
        );

        return response()->json(['message' => 'Privacy Policy updated successfully.']);
    }

    public function update(Request $request, $id)
    {
        return $this->store($request);
    }
}
