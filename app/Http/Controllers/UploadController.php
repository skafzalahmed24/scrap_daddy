<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UploadController extends Controller
{
    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // max 2MB per image
        ], [
            'images.required' => 'Please choose at least one image.',
            'images.max' => 'You can upload a maximum of 10 images.',
            'images.*.image' => 'Only image files are allowed (JPG, PNG, GIF or WEBP).',
            'images.*.mimes' => 'Only JPG, PNG, GIF or WEBP images are allowed.',
            'images.*.max' => 'Each image must be 2 MB or smaller.',
            'images.*.uploaded' => 'The image could not be uploaded. It may be larger than 2 MB.',
        ]);

        $uploadedPaths = [];

        if ($request->hasFile('images')) {
            $targetDir = public_path('uploads/orders');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                // Extension is derived from the real file content, never from the client's filename
                $filename = time() . '_' . Str::random(10) . '.' . ($file->extension() ?: 'jpg');
                $targetPath = $targetDir . '/' . $filename;
                file_put_contents($targetPath, file_get_contents($file->getRealPath()));
                @chmod($targetPath, 0644);
                $uploadedPaths[] = 'uploads/orders/' . $filename;
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Images uploaded successfully.',
            'data' => [
                'paths' => $uploadedPaths
            ]
        ]);
    }
}

