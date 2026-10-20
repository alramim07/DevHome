<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

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
        $baseUrl = config('app.url');
        $urls = [];

        // Static pages (always accessible)
        $staticPages = [
            ['url' => $baseUrl, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => $baseUrl . '/login', 'changefreq' => 'monthly', 'priority' => '0.3'],
            ['url' => $baseUrl . '/team', 'changefreq' => 'weekly', 'priority' => '0.5'], // Team listing page
        ];

        foreach ($staticPages as $page) {
            $urls[] = $this->buildUrlEntry($page);
        }

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

        // Team member public pages (if team slug route exists)
        // Only include if you have a public team member view route

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