<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Subcategory::with('category')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
        }

        $subcategories = $query->paginate(10);
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->has('status') ? filter_var($request->status, FILTER_VALIDATE_BOOLEAN) : true;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('subcategories');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $fileContent = file_get_contents($file->getRealPath());
            file_put_contents($destinationPath . '/' . $filename, $fileContent);
            
            $data['image'] = 'subcategories/' . $filename;
        }

        Subcategory::create($data);

        return response()->json(['message' => 'Subcategory created successfully']);
    }

    public function show(string $id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, string $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->except('image');
        $data['status'] = $request->has('status') ? filter_var($request->status, FILTER_VALIDATE_BOOLEAN) : true;

        if ($request->hasFile('image')) {
            if ($subcategory->image && File::exists(public_path($subcategory->image))) {
                File::delete(public_path($subcategory->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('subcategories');
            
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            $fileContent = file_get_contents($file->getRealPath());
            file_put_contents($destinationPath . '/' . $filename, $fileContent);
            
            $data['image'] = 'subcategories/' . $filename;
        }

        $subcategory->update($data);

        return response()->json(['message' => 'Subcategory updated successfully']);
    }

    public function destroy(string $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        
        if ($subcategory->image && File::exists(public_path($subcategory->image))) {
            File::delete(public_path($subcategory->image));
        }

        $subcategory->delete();

        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
