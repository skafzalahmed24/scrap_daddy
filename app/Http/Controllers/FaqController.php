<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $faqs = $query->latest()->paginate(10);
        return response()->json($faqs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $data = $request->only(['question', 'answer', 'status']);
        $faq = Faq::create($data);

        return response()->json(['message' => 'FAQ created successfully', 'faq' => $faq]);
    }

    public function show(string $id)
    {
        $faq = Faq::findOrFail($id);
        return response()->json($faq);
    }

    public function update(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $data = $request->only(['question', 'answer', 'status']);
        $faq->update($data);

        return response()->json(['message' => 'FAQ updated successfully', 'faq' => $faq]);
    }

    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json(['message' => 'FAQ deleted successfully']);
    }

    public function toggleStatus(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);
        
        $request->validate([
            'status' => 'required|boolean'
        ]);

        $faq->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully', 'faq' => $faq]);
    }
}
