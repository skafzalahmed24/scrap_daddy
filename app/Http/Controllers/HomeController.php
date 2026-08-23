<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Subcategory;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', 1)->latest()->get();
        $webBanners = $banners->where('type', 'web')->values();
        $mobileBanners = $banners->where('type', 'mobile')->values();
        $categories = Category::where('status', 1)->latest()->get();
        $subcategories = Subcategory::where('status', 1)->latest()->take(15)->get();

        return view('welcome', compact('webBanners', 'mobileBanners', 'categories', 'subcategories'));
    }

    public function categories()
    {
        $firstCategory = Category::where('status', 1)->first();
        if ($firstCategory) {
            return redirect()->route('category.show', $firstCategory->uuid);
        }
        return redirect('/');
    }

    public function category($id)
    {
        $allCategories = Category::where('status', 1)->get();
        $category = Category::findOrFail($id);
        $subcategories = Subcategory::where('category_id', $id)->where('status', 1)->get();
        return view('category', compact('category', 'subcategories', 'allCategories'));
    }

    public function faqs()
    {
        $faqs = \App\Models\Faq::where('status', 1)->get();
        return view('customer.faqs', compact('faqs'));
    }
}
