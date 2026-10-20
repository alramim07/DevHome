<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Cache::remember('sitemap', 3600, function () {
            return $this->generateSitemap();
        });

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    protected function generateSitemap(): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $urls = [];

        // ONLY include: Homepage + Published Projects + Published Blogs
        // NO dashboard, login, team, admin, or other internal URLs

        // Homepage
        $urls[] = $this->buildUrlEntry([
            'url' => $baseUrl,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        // Published Projects
        $projects = Projects::whereNotNull('slug')
            ->where('slug', '!=', '')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($projects as $project) {
            $urls[] = $this->buildUrlEntry([
                'url' => $baseUrl . '/project/' . $project->slug,
                'lastmod' => $project->updated_at->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);
        }

        // Published Blog Posts
        $blogs = Blog::published()
            ->select('slug', 'updated_at', 'published_at')
            ->orderBy('published_at', 'desc')
            ->get();

        foreach ($blogs as $blog) {
            $lastmod = $blog->updated_at > $blog->published_at 
                ? $blog->updated_at->format('Y-m-d') 
                : $blog->published_at->format('Y-m-d');
            
            $urls[] = $this->buildUrlEntry([
                'url' => $baseUrl . '/blog/' . $blog->slug,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= $url;
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    protected function buildUrlEntry(array $data): string
    {
        $url = '<url>' . "\n";
        $url .= '  <loc>' . htmlspecialchars($data['url']) . '</loc>' . "\n";
        
        if (!empty($data['lastmod'])) {
            $url .= '  <lastmod>' . $data['lastmod'] . '</lastmod>' . "\n";
        }
        
        $url .= '  <changefreq>' . $data['changefreq'] . '</changefreq>' . "\n";
        $url .= '  <priority>' . $data['priority'] . '</priority>' . "\n";
        $url .= '</url>' . "\n";
        
        return $url;
    }

    public function clearCache()
    {
        Cache::forget('sitemap');
        return redirect()->back()->with('success', 'Sitemap cache cleared.');
    }
}