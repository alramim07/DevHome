<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    public function create(DashboardService $service)
    {
        $data = $service->getStats(Auth::user());
        $data['blog'] = null;

        return view('admin.blog.add', $data);
    }

    public function store(Request $request)
    {
        $data = $this->validatePost($request);

        $blog = new Blog();
        $blog->user_id = Auth::id();
        $blog->title = $data['title'];
        $blog->slug = $this->resolveSlug($data['slug'] ?? null, $data['title']);
        $blog->content = $data['content'];
        $blog->excerpt = $data['excerpt'] ?? null;
        $blog->status = $data['status'];
        $blog->published_at = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('cover_image')) {
            $blog->cover_image = 'storage/' . $request->file('cover_image')->store('blog_covers', 'public');
        }

        $blog->save();

        // Clear sitemap cache
        Cache::forget('sitemap');

        return redirect()->route('blog.list')->with('success', 'Blog post created successfully!');
    }

    public function list(Request $request, DashboardService $service)
    {
        $user = Auth::user();
        $scope = $request->query('scope', 'my');

        if ($scope === 'all' && $user->isSuperAdmin()) {
            $blogs = Blog::with('author')->latest()->get();
        } else {
            $blogs = Blog::where('user_id', $user->id)->with('author')->latest()->get();
            $scope = 'my';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'scope' => $scope,
                'count' => $blogs->count(),
                'html' => view('admin.blog.partials.table_rows', [
                    'blogs' => $blogs,
                    'isAll' => $scope === 'all',
                ])->render(),
            ]);
        }

        $data = $service->getStats($user);
        $data['blogs'] = $blogs;
        $data['currentScope'] = $scope;

        return view('admin.blog.list', $data);
    }

    public function edit($id)
    {
        $data = [
            'user' => Auth::user(),
            'siteSettings' => \App\Models\Website_settings::first(),
            'blog' => Blog::findOrFail($id),
        ];

        return view('admin.blog.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $data = $this->validatePost($request, $blog);

        $blog->title = $data['title'];
        $blog->slug = $this->resolveSlug($data['slug'] ?? null, $data['title'], $blog);
        $blog->content = $data['content'];
        $blog->excerpt = $data['excerpt'] ?? null;
        $blog->status = $data['status'];

        if ($data['status'] === 'published' && ! $blog->published_at) {
            $blog->published_at = now();
        }
        if ($data['status'] !== 'published') {
            $blog->published_at = null;
        }

        if ($request->hasFile('cover_image')) {
            $blog->cover_image = 'storage/' . $request->file('cover_image')->store('blog_covers', 'public');
        }

        $blog->save();

        // Clear sitemap cache
        Cache::forget('sitemap');

        return redirect()->route('blog.list')->with('success', 'Blog post updated successfully!');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        $user = Auth::user();
        if (! $user->isSuperAdmin() && $blog->user_id !== $user->id) {
            abort(403);
        }

        if ($blog->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(media_path($blog->cover_image));
        }

        $blog->delete();

        // Clear sitemap cache
        Cache::forget('sitemap');

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully!'
            ]);
        }

        return redirect()->route('blog.list')->with('success', 'Blog post deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No blog posts selected.'], 422);
        }

        $user = Auth::user();
        $query = Blog::whereIn('id', $ids);
        if (! $user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }
        $blogs = $query->get();

        foreach ($blogs as $blog) {
            if ($blog->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(media_path($blog->cover_image));
            }
            $blog->delete();
        }

        Cache::forget('sitemap');

        $count = $blogs->count();
        $msg = "{$count} " . Str::plural('blog post', $count) . ' deleted successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('blog.list')->with('success', $msg);
    }

    protected function validatePost(Request $request, ?Blog $blog = null): array
    {
        $slugRules = ['nullable', 'string', 'max:255', 'alpha_dash'];
        if ($blog) {
            $slugRules[] = 'unique:blogs,slug,' . $blog->id;
        } else {
            $slugRules[] = 'unique:blogs,slug';
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => $slugRules,
            'content' => ['required', 'string', 'max:1000000'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);
    }

    protected function resolveSlug(?string $slugInput, string $title, ?Blog $ignore = null): string
    {
        $slug = Str::slug($slugInput ?: $title);
        if ($slug === '') {
            $slug = 'post';
        }

        $base = $slug;
        $n = 1;
        while (Blog::where('slug', $slug)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }
}
