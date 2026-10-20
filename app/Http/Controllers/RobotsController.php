<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RobotsController extends Controller
{
    public function index()
    {
        $lines = [
            "User-agent: *",
            "Allow: /",
            "",
            "Disallow: /admin/",
            "Disallow: /login",
            "Disallow: /dashboard",
            "Disallow: /team-management/",
            "Disallow: /positions",
            "Disallow: /teams",
            "Disallow: /members",
            "Disallow: /add-blog",
            "Disallow: /blog-list",
            "Disallow: /edit-blog",
            "Disallow: /add-project",
            "Disallow: /project-list",
            "Disallow: /edit-project",
            "Disallow: /website-settings",
            "Disallow: /email-list",
            "Disallow: /website-contacts",
            "Disallow: /join-list",
            "Disallow: /todo",
            "Disallow: /profile",
            "Disallow: /notifications",
            "",
            "Sitemap: " . config('app.url') . "/sitemap.xml",
            "",
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}