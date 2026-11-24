<?php

namespace App\Http\Controllers;

use App\Models\BulkEnquiry;
use App\Models\BulkEnquiryField;
use App\Models\HomeBanner;
use App\Models\HomeSection;
use App\Models\ScrapArticle;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/** Public API used by the website and the mobile app Home screen. */
class HomeContentController extends Controller
{
    /** Section texts (keyed by section key), trending items and the bulk enquiry form. */
    public function show()
    {
        $sections = HomeSection::orderBy('sort')->get()->mapWithKeys(fn ($s) => [$s->key => [
            'title' => $s->title,
            'description' => $s->description,
            'button_text' => $s->button_text,
            'tags' => $s->tags ?? [],
            'is_active' => (bool) $s->is_active,
        ]]);

        $trending = Subcategory::where('status', true)->where('is_trending', true)
            ->orderBy('trending_order')->orderBy('name')->take(20)->get()
            ->map(fn ($s) => ['uuid' => $s->uuid, 'name' => $s->name, 'image' => $s->image]);

        $fields = BulkEnquiryField::where('is_active', true)->orderBy('sort')->orderBy('id')->get()
            ->map(fn ($f) => ['id' => $f->id, 'label' => $f->label, 'type' => $f->type, 'options' => $f->options ?? [], 'required' => (bool) $f->required]);

        // Scrolling banners per section (Sell bulk scrap / Scrap your vehicle / Offers)
        $banners = HomeBanner::where('is_active', true)->orderBy('sort')->orderBy('id')->get()
            ->groupBy('section_key')
            ->map(fn ($g) => $g->map(fn ($b) => [
                'id' => $b->id, 'title' => $b->title, 'subtitle' => $b->subtitle, 'image' => $b->image,
                'link_type' => $b->link_type, 'link_value' => $b->link_value,
            ])->values());

        // "Know your scrap" cards
        $articles = ScrapArticle::where('is_active', true)->orderBy('sort')->orderByDesc('id')->take(12)->get()
            ->map(fn ($x) => ['id' => $x->id, 'slug' => $x->slug, 'title' => $x->title, 'summary' => $x->summary, 'cover_image' => $x->cover_image]);

        return response()->json([
            'status' => 1,
            'message' => 'Home content fetched successfully',
            'data' => [
                'sections' => $sections,
                'trending' => $trending,
                'bulk_fields' => $fields,
                // an empty list must still be a JSON object {} for the app
                'banners' => $banners->isEmpty() ? new \stdClass : $banners,
                'articles' => $articles,
            ],
        ]);
    }

    /** One article with its gallery, for the app's detail screen. */
    public function article(Request $request)
    {
        $article = ScrapArticle::with('images')->where('is_active', true)->find((int) $request->input('id'));
        if (!$article) {
            return response()->json(['status' => 0, 'message' => 'This article is not available.'], 200);
        }

        return response()->json(['status' => 1, 'message' => 'OK', 'data' => [
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'summary' => $article->summary,
            'content' => $article->content,
            'cover_image' => $article->cover_image,
            'images' => $article->images->pluck('image')->values(),
        ]]);
    }

    /** Bulk enquiry: name + phone + the admin-defined fields. Guests can send it too. */
    public function submitEnquiry(Request $request)
    {
        $fields = BulkEnquiryField::where('is_active', true)->orderBy('sort')->orderBy('id')->get();

        $attrs = ['name' => 'name', 'phone' => 'phone number'];
        $rules = [
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,15}$/'],
            'answers' => 'nullable|array',
        ];
        foreach ($fields as $f) {
            $r = [$f->required ? 'required' : 'nullable'];
            $r[] = match ($f->type) {
                'number' => 'numeric|min:0',
                'email' => 'email|max:150',
                'phone' => 'regex:/^[0-9+\-\s]{8,15}$/',
                'dropdown' => 'in:' . implode(',', array_map(fn ($o) => str_replace(',', ' ', $o), $f->options ?? [])),
                'textarea' => 'string|max:1000',
                default => 'string|max:255',
            };
            $attrs["answers.{$f->id}"] = $f->label;
            $rules["answers.{$f->id}"] = array_merge([$r[0]], explode('|', $r[1]));
        }

        $v = Validator::make($request->all(), $rules, [], $attrs);
        if ($v->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $v->errors()->first(),
                'data' => ['errors' => $v->errors()],
            ], 200);
        }

        // Keep a readable snapshot (label + value) so later edits to the form do not change old enquiries
        $answers = [];
        foreach ($fields as $f) {
            $value = $request->input("answers.{$f->id}");
            if ($value !== null && $value !== '') {
                $answers[] = ['label' => $f->label, 'value' => (string) $value];
            }
        }

        $user = $request->user('sanctum');
        BulkEnquiry::create([
            'user_uuid' => $user?->uuid,
            'name' => trim($request->name),
            'phone' => trim($request->phone),
            'answers' => $answers,
        ]);

        return response()->json(['status' => 1, 'message' => 'Thanks! Our team will contact you shortly about your bulk scrap.']);
    }
}
