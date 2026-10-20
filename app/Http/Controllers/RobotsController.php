<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RobotsController extends Controller
{
    public function index()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /team-management/\n";
        $content .= "Disallow: /positions\n";
        $content .= "Disallow: /teams\n";
        $content .= "Disallow: /members\n";
        $content .= "Disallow: /add-blog\n";
        $content .= "Disallow: /blog-list\n";
        $content .= "Disallow: /edit-blog\n";
        $content .= "Disallow: /add-project\n";
        $content .= "Disallow: /project-list\n";
        $content .= "Disallow: /edit-project\n";
        $content .= "Disallow: /website-settings\n";
        $content .= "Disallow: /email-list\n";
        $content .= "Disallow: /website-contacts\n";
        $content .= "Disallow: /join-list\n";
        $content .= "Disallow: /todo\n";
        $content .= "Disallow: /profile\n";
        $content .= "Disallow: /notifications\n\n";
        $content .= "Sitemap: " . config('app.url') . "/sitemap.xml\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}