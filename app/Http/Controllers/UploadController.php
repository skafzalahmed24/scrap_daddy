<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB per image
        ]);

        $uploadedPaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/orders', $filename, 'public');
                $uploadedPaths[] = 'storage/' . $path;
            }
        }

        return response()->json([
            'success' => true,
            'paths' => $uploadedPaths,
            'message' => 'Images uploaded successfully.',
        ]);
    }
}
