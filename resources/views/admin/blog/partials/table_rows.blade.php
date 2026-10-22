@forelse ($blogs as $blog)
<tr id="blog-row-{{ $blog->id }}">
    <td>
        <input class="form-check-input blog-checkbox" type="checkbox" value="{{ $blog->id }}" data-title="{{ $blog->title }}">
    </td>
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
        <button type="button" class="btn btn-sm btn-outline-danger delete-blog-btn" data-id="{{ $blog->id }}" data-title="{{ $blog->title }}">Delete</button>
    </td>
</tr>
@empty
<tr class="no-blogs-row">
    <td colspan="8" class="text-center text-muted py-4">No blog posts found.</td>
</tr>
@endforelse
