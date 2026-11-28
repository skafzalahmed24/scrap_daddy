<?php

namespace App\Http\Controllers;

use App\Models\HowStep;
use Illuminate\Http\Request;

/** Admin: the steps of "How it works" (website + app). The heading text is edited in Home Content. */
class AdminHowStepController extends Controller
{
    public function index()
    {
        return view('admin.how-steps', ['steps' => HowStep::orderBy('sort')->orderBy('id')->get()]);
    }

    private function data(Request $request): array
    {
        $d = $request->validate([
            'title' => 'required|string|max:80',
            'description' => 'nullable|string|max:300',
            'icon' => 'required|in:' . implode(',', array_keys(HowStep::ICONS)),
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort' => 'nullable|integer|min:0|max:999',
        ]);

        return [
            'title' => $d['title'],
            'description' => $d['description'] ?? null,
            'icon' => $d['icon'],
            'color' => $d['color'],
            'sort' => (int) ($d['sort'] ?? 0),
        ];
    }

    public function store(Request $request)
    {
        HowStep::create($this->data($request) + ['is_active' => true]);

        return back()->with('success', 'Step added.');
    }

    public function update(Request $request, $id)
    {
        HowStep::findOrFail($id)->update($this->data($request) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Step saved.');
    }

    public function destroy($id)
    {
        HowStep::findOrFail($id)->delete();

        return back()->with('success', 'Step removed.');
    }
}
