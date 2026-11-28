<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\HowStep;
use App\Models\StaticPage;

/** Public information pages: About us, Contact us, FAQs and Services. All content comes from the admin panel / database. */
class SitePagesController extends Controller
{
    public function about()
    {
        return view('site.about', [
            // an "about-us" page written in Admin > Pages replaces the default text
            'page' => StaticPage::where('slug', 'about-us')->first(),
            'steps' => HowStep::active(),
        ]);
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function faqs()
    {
        return view('site.faqs', [
            'faqs' => Faq::where('status', 1)->orderBy('id')->get(),
        ]);
    }

    public function services()
    {
        return view('site.services', [
            'categories' => Category::where('status', 1)->orderBy('title')->get(),
            'steps' => HowStep::active(),
            'howSection' => HomeSection::where('key', 'how_it_works')->first(),
        ]);
    }
}
