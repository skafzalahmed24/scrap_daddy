<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_uuid' => 'required|exists:users,uuid',
            'star_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Feedback::create([
            'user_uuid' => $request->user_uuid,
            'star_rating' => $request->star_rating,
            'comment' => $request->comment,
            'is_approved' => false, // Requires admin approval
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    // Admin methods
    public function adminIndex()
    {
        $feedbacks = Feedback::with('user')->latest()->get();
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function toggleApproval($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->is_approved = !$feedback->is_approved;
        $feedback->save();

        return redirect()->back()->with('success', 'Feedback visibility toggled.');
    }
}
