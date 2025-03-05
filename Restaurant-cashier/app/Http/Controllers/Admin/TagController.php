<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('admin.tags.index', compact('tags'));
    }

    public function show(Tag $tag)
{
    return response()->json($tag);
}
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags|max:255',
        ]);

        $tag = Tag::create($request->all());

        return response()->json($tag);
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $tag->id . '|max:255',
        ]);

        $tag->update($request->all());

        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['success' => 'Tag deleted successfully.']);
    }
}