<?php

namespace App\Http\Controllers;

use App\Models\ScrapArticle;
use App\Models\ScrapArticleImage;
use App\Services\HomeImage;
use Illuminate\Http\Request;

/** Admin: "Know your scrap" articles (cover, description, gallery banners). */
class AdminScrapArticleController extends Controller
{
    public function index()
    {
        return view('admin.scrap-articles', [
            'articles' => ScrapArticle::withCount('images')->orderBy('sort')->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.scrap-article-form', ['article' => new ScrapArticle(['is_active' => true])]);
    }

    public function edit($id)
    {
        return view('admin.scrap-article-form', ['article' => ScrapArticle::with('images')->findOrFail($id)]);
    }

    private function rules(bool $creating): array
    {
        return [
            'title' => 'required|string|max:160',
            'summary' => 'nullable|string|max:300',
            'content' => 'nullable|string|max:20000',
            'cover_image' => ($creating ? 'required|' : 'nullable|') . HomeImage::RULE,
            'images' => 'nullable|array|max:10',
            'images.*' => HomeImage::RULE,
            'sort' => 'nullable|integer|min:0|max:999',
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true));

        $article = ScrapArticle::create([
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
            'cover_image' => HomeImage::store($request->file('cover_image')),
            'is_active' => $request->boolean('is_active', true),
            'sort' => (int) ($data['sort'] ?? 0),
        ]);
        $this->addGallery($request, $article);

        return redirect()->route('admin.scrap-articles.edit', $article->id)->with('success', 'Article created.');
    }

    public function update(Request $request, $id)
    {
        $article = ScrapArticle::findOrFail($id);
        $data = $request->validate($this->rules(false));

        $article->fill([
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort' => (int) ($data['sort'] ?? 0),
        ]);
        if ($request->hasFile('cover_image')) {
            HomeImage::delete($article->cover_image);
            $article->cover_image = HomeImage::store($request->file('cover_image'));
        }
        $article->save();

        // remove ticked gallery images
        foreach ((array) $request->input('remove_images', []) as $imgId) {
            $img = ScrapArticleImage::where('scrap_article_id', $article->id)->find($imgId);
            if ($img) {
                HomeImage::delete($img->image);
                $img->delete();
            }
        }
        $this->addGallery($request, $article);

        return back()->with('success', 'Article saved.');
    }

    private function addGallery(Request $request, ScrapArticle $article): void
    {
        $next = (int) ScrapArticleImage::where('scrap_article_id', $article->id)->max('sort');
        foreach ((array) $request->file('images', []) as $file) {
            ScrapArticleImage::create([
                'scrap_article_id' => $article->id,
                'image' => HomeImage::store($file),
                'sort' => ++$next,
            ]);
        }
    }

    public function destroy($id)
    {
        $article = ScrapArticle::with('images')->findOrFail($id);
        foreach ($article->images as $img) {
            HomeImage::delete($img->image);
        }
        HomeImage::delete($article->cover_image);
        $article->delete();

        return redirect()->route('admin.scrap-articles.index')->with('success', 'Article deleted.');
    }
}
