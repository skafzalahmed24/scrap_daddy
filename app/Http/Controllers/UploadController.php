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
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB per image
        ]);

        $uploadedPaths = [];

        if ($request->hasFile('images')) {
            $targetDir = public_path('uploads/orders');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
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

