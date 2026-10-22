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
    <td colspan="7" class="text-center text-muted py-4">No blog posts found.</td>
</tr>
@endforelse
