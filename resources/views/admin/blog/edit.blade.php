@extends('admin.layouts.app')
@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary rounded h-100 p-4">
        <h6 class="mb-4">Edit Blog: {{ $blog->title }}</h6>
        <form action="{{ route('blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="blog_title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $blog->title) }}" required>
                        @error('title')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="blog_slug"
                            class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $blog->slug) }}">
                        @error('slug')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Excerpt <span class="text-muted small">(optional short
                                summary)</span></label>
                        <textarea name="excerpt" rows="2" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt) }}</textarea>
                        @error('excerpt')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" id="description" style="height: auto;">{{ old('content', $blog->content) }}</textarea>
                        @error('content')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Draft
                                (hidden from public)</option>
                            <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>
                                Published</option>
                        </select>
                        @error('status')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror"
                            accept="image/*">
                        @if ($blog->cover_image)
                        <img src="{{ media_url($blog->cover_image) }}" alt="" class="mt-2 rounded" width="120">
                        @endif
                        @error('cover_image')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="add-btn">
                        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
                        <a href="{{ route('blog.list') }}" class="btn btn-outline-secondary m-2">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('blog_title').addEventListener('input', function () {
        let slug = this.value.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('blog_slug').value = slug;
    });
</script>
@endpush
