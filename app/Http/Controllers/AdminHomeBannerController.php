<?php

namespace App\Http\Controllers;

use App\Models\HomeBanner;
use App\Services\HomeImage;
use Illuminate\Http\Request;

/** Admin: the scrolling banners of Sell bulk scrap, Scrap your vehicle and Offers. */
class AdminHomeBannerController extends Controller
{
    public function index(string $section)
    {
        abort_unless(array_key_exists($section, HomeBanner::SECTIONS), 404);

        return view('admin.home-banners', [
            'section' => $section,
            'sectionLabel' => HomeBanner::SECTIONS[$section],
            'banners' => HomeBanner::where('section_key', $section)->orderBy('sort')->orderBy('id')->get(),
            // sensible default tap action per section
            'defaultLink' => ['sell_bulk' => 'bulk_enquiry', 'scrap_vehicle' => 'vehicle', 'offers' => 'none'][$section],
        ]);
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title' => 'nullable|string|max:120',
            'subtitle' => 'nullable|string|max:200',
            'image' => ($imageRequired ? 'required|' : 'nullable|') . HomeImage::RULE,
            'link_type' => 'required|in:' . implode(',', array_keys(HomeBanner::LINKS)),
            'link_value' => 'nullable|url|max:500',
            'sort' => 'nullable|integer|min:0|max:999',
        ];
    }

    public function store(Request $request, string $section)
    {
        abort_unless(array_key_exists($section, HomeBanner::SECTIONS), 404);
        $data = $request->validate($this->rules(true));
        if ($data['link_type'] === 'url' && empty($data['link_value'])) {
            return back()->withInput()->with('error', 'Please enter the web link for this banner.');
        }

        HomeBanner::create([
            'section_key' => $section,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'image' => HomeImage::store($request->file('image')),
            'link_type' => $data['link_type'],
            'link_value' => $data['link_type'] === 'url' ? $data['link_value'] : null,
            'is_active' => true,
            'sort' => (int) ($data['sort'] ?? 0),
        ]);

        return back()->with('success', 'Banner added.');
    }

    public function update(Request $request, $id)
    {
        $banner = HomeBanner::findOrFail($id);
        $data = $request->validate($this->rules(false));
        if ($data['link_type'] === 'url' && empty($data['link_value'])) {
            return back()->with('error', 'Please enter the web link for this banner.');
        }

        $banner->fill([
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'link_type' => $data['link_type'],
            'link_value' => $data['link_type'] === 'url' ? $data['link_value'] : null,
            'is_active' => $request->boolean('is_active'),
            'sort' => (int) ($data['sort'] ?? 0),
        ]);

        if ($request->hasFile('image')) {
            HomeImage::delete($banner->image);
            $banner->image = HomeImage::store($request->file('image'));
        }
        $banner->save();

        return back()->with('success', 'Banner updated.');
    }

    public function destroy($id)
    {
        $banner = HomeBanner::findOrFail($id);
        HomeImage::delete($banner->image);
        $banner->delete();

        return back()->with('success', 'Banner removed.');
    }
}
