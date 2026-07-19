<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaticPage;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = StaticPage::where('slug', $slug)->first();
        
        // If the admin hasn't created the page yet, show a friendly placeholder instead of a 404 error
        if (!$page) {
            $page = new StaticPage([
                'title' => ucwords(str_replace('-', ' ', $slug)),
                'content' => "<div class='text-center py-5 my-5'>
                                <i class='fa-solid fa-person-digging fa-4x text-muted mb-3 opacity-50'></i>
                                <h3 class='fw-bold text-secondary'>Page Under Construction</h3>
                                <p class='text-muted'>The content for this page has not been added yet. Please check back later.</p>
                              </div>"
            ]);
        }

        return view('pages.show', compact('page'));
    }

    // Admin Methods
    public function index()
    {
        $pages = StaticPage::all();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:static_pages',
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        StaticPage::create($request->only('slug', 'title', 'content'));

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit($id)
    {
        $page = StaticPage::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $page = StaticPage::findOrFail($id);
        $page->update($request->only('title', 'content'));

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $page = StaticPage::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }
}
