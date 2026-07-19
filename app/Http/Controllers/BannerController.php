<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $banners = $query->latest()->paginate(10);
        return response()->json($banners);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'status' => 'required|boolean',
            'uploads' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,webm,ogg|max:20480', // 20MB max
        ]);

        $data = $request->only(['title', 'short_description', 'status']);

        if ($request->hasFile('uploads')) {
            $file = $request->file('uploads');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            
            // Workaround for OneDrive lock issue
            $fileContents = file_get_contents($file->getRealPath());
            
            if (!file_exists(public_path('uploads/banners'))) {
                mkdir(public_path('uploads/banners'), 0777, true);
            }
            
            file_put_contents(public_path('uploads/banners/' . $filename), $fileContents);
            
            $data['uploads'] = 'uploads/banners/' . $filename;
        }

        $banner = Banner::create($data);

        return response()->json(['message' => 'Banner created successfully', 'banner' => $banner]);
    }

    public function show(string $id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'status' => 'required|boolean',
            'uploads' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,webm,ogg|max:20480',
        ]);

        $data = $request->only(['title', 'short_description', 'status']);

        if ($request->hasFile('uploads')) {
            $file = $request->file('uploads');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            
            $fileContents = file_get_contents($file->getRealPath());
            
            if (!file_exists(public_path('uploads/banners'))) {
                mkdir(public_path('uploads/banners'), 0777, true);
            }
            
            file_put_contents(public_path('uploads/banners/' . $filename), $fileContents);
            
            // Delete old file if exists
            if ($banner->uploads && file_exists(public_path($banner->uploads))) {
                unlink(public_path($banner->uploads));
            }

            $data['uploads'] = 'uploads/banners/' . $filename;
        }

        $banner->update($data);

        return response()->json(['message' => 'Banner updated successfully', 'banner' => $banner]);
    }

    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);
        
        if ($banner->uploads && file_exists(public_path($banner->uploads))) {
            unlink(public_path($banner->uploads));
        }
        
        $banner->delete();

        return response()->json(['message' => 'Banner deleted successfully']);
    }
}
