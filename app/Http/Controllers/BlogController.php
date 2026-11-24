<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Models\ScrapArticle;

/** Public blog ("Know your scrap" articles): readable by everyone and built for search engines. */
class BlogController extends Controller
{
    public function index()
    {
        $section = HomeSection::where('key', 'know_scrap')->first();

        return view('blog', [
            'articles' => ScrapArticle::where('is_active', true)->orderBy('sort')->orderByDesc('id')->paginate(9),
            'heading' => $section->title ?? 'Know your scrap',
            'intro' => $section->description ?? 'Guides on what each kind of scrap is, how it is recycled and how to get the best value.',
        ]);
    }

    public function show(string $slug)
    {
        $article = ScrapArticle::with('images')->where('is_active', true)->where('slug', $slug)->firstOrFail();
        $more = ScrapArticle::where('is_active', true)->where('id', '!=', $article->id)->orderBy('sort')->orderByDesc('id')->take(4)->get();

        return view('blog-article', compact('article', 'more'));
    }

    /** Old /learn/{id} links keep working and point search engines to the new address (301). */
    public function legacy($id)
    {
        $article = ScrapArticle::where('is_active', true)->findOrFail($id);

        return redirect()->route('blog.show', $article->slug, 301);
    }

    /** /sitemap.xml: home, blog and every published article. */
    public function sitemap()
    {
        $articles = ScrapArticle::where('is_active', true)->orderByDesc('updated_at')->get(['slug', 'updated_at']);

        $urls = [
            ['loc' => url('/'), 'lastmod' => null, 'freq' => 'weekly', 'prio' => '1.0'],
            ['loc' => route('blog.index'), 'lastmod' => optional($articles->first())->updated_at, 'freq' => 'weekly', 'prio' => '0.8'],
        ];
        foreach ($articles as $a) {
            $urls[] = ['loc' => route('blog.show', $a->slug), 'lastmod' => $a->updated_at, 'freq' => 'monthly', 'prio' => '0.7'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if ($u['lastmod']) {
                $xml .= '    <lastmod>' . $u['lastmod']->toAtomString() . "</lastmod>\n";
            }
            $xml .= "    <changefreq>{$u['freq']}</changefreq>\n    <priority>{$u['prio']}</priority>\n  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /** /robots.txt: crawlers may read the public site (not the admin or customer area) and are told where the sitemap is. */
    public function robots()
    {
        $body = "User-agent: *\nAllow: /\nAllow: /customer/request-pickup\nDisallow: /admin\nDisallow: /customer\nDisallow: /api\n\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
