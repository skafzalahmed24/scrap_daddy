<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Models\Subcategory;
use Illuminate\Http\Request;

/** Admin: edit the text of each Home screen section and choose the Trending items. */
class AdminHomeContentController extends Controller
{
    public function index()
    {
        return view('admin.home-content', [
            'sections' => HomeSection::orderBy('sort')->get(),
            'subcategories' => Subcategory::with('category')->where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function updateSection(Request $request, string $key)
    {
        $section = HomeSection::where('key', $key)->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:60',
            'tags' => 'nullable|string|max:500',   // "Best rates, Compliance, Transparency"
        ]);

        $tags = collect(preg_split('/[,\n]+/', (string) ($data['tags'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($t) => trim($t, " #\t"))
            ->filter()
            ->take(8)
            ->values()
            ->all();

        $section->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'button_text' => $data['button_text'] ?? null,
            'tags' => $tags ?: null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "'{$section->title}' updated.");
    }

    /** Selected sub-categories become "trending", ordered by the numbers the admin typed. */
    public function saveTrending(Request $request)
    {
        $selected = collect($request->input('trending', []))->filter()->values();
        $order = (array) $request->input('order', []);

        Subcategory::query()->update(['is_trending' => false, 'trending_order' => 0]);

        foreach ($selected as $i => $uuid) {
            Subcategory::where('uuid', $uuid)->update([
                'is_trending' => true,
                'trending_order' => (int) ($order[$uuid] ?? ($i + 1)),
            ]);
        }

        return back()->with('success', $selected->count() . ' trending item(s) saved.');
    }
}
