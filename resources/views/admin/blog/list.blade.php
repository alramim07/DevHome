@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded h-100 p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Blog List</h6>
            <a href="{{ route('blog.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i>Add
                Blog</a>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blogs as $blog)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($blog->cover_image)
                        <img src="{{ media_url($blog->cover_image) }}" alt="" class="rounded" width="60" height="40"
                            style="object-fit: cover;">
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        {{ $blog->title }}
                        @if ($blog->status === 'published')
                        <a href="{{ route('front.blog', $blog->slug) }}" target="_blank" class="ms-1 small"
                            title="View public page"><i class="fa fa-external-link-alt"></i></a>
                        @endif
                    </td>
                    <td>{{ $blog->author?->name ?? '—' }}</td>
                    <td>
                        <span
                            class="badge bg-{{ $blog->status === 'published' ? 'success' : 'secondary' }}">{{ Str::ucfirst($blog->status) }}</span>
                    </td>
                    <td>{{ $blog->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-sm btn-outline-info">Edit</a>
                        <form action="{{ route('blog.destroy', $blog->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Delete blog post {{ $blog->title }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No blog posts yet —
                        <a href="{{ route('blog.create') }}">write your first one</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
