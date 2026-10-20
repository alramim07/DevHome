@extends('front.layouts.master')

@section('content')
<section class="ng um pn og vm qn">
  <div class="a">
    <div class="-ud-mx-4 sb wd yd">
      <div class="oc tf em/12 fn/12">
        <div class="oc">
          <h1 class="animate_top zh mj za eh lh rh nk wk ml ql">
            {{ $blog->title }}
          </h1>

          <div class="animate_top sb wd xd pg">
            <div class="va _a sb xd xk">
              @if ($author && $author->image)
              <div class="ab yb qc ke qe">
                <img src="{{ media_url($author->image) }}" alt="image" class="oc">
              </div>
              @endif
              <p class="mh">
                By
                <a href="#" class="_i">{{ $author?->name ?? 'DevHome Team' }}</a>
              </p>
            </div>
            <div class="va sb xd">
              <p class="_a sb xd fh mh yk">
                <span class="bb">
                  <svg width="15" height="15" viewBox="0 0 15 15" class="jf">
                    <path
                      d="M3.8958 8.67529H3.10715C2.96376 8.67529 2.86816 8.77089 2.86816 8.91428V9.67904C2.86816 9.82243 2.96376 9.91802 3.10715 9.91802H3.8958C4.03919 9.91802 4.13479 9.82243 4.13479 9.67904V8.91428C4.13479 8.77089 4.03919 8.67529 3.8958 8.67529Z">
                    </path>
                    <path
                      d="M6.429 8.67529H5.64035C5.49696 8.67529 5.40137 8.77089 5.40137 8.91428V9.67904C5.40137 9.82243 5.49696 9.91802 5.64035 9.91802H6.429C6.57239 9.91802 6.66799 9.82243 6.66799 9.67904V8.91428C6.66799 8.77089 6.57239 8.67529 6.429 8.67529Z">
                    </path>
                    <path
                      d="M8.93779 8.67529H8.14914C8.00575 8.67529 7.91016 8.77089 7.91016 8.91428V9.67904C7.91016 9.82243 8.00575 9.91802 8.14914 9.91802H8.93779C9.08118 9.91802 9.17678 9.82243 9.17678 9.67904V8.91428C9.17678 8.77089 9.08118 8.67529 8.93779 8.67529Z">
                    </path>
                    <path
                      d="M11.472 8.67529H10.6833C10.5399 8.67529 10.4443 8.77089 10.4443 8.91428V9.67904C10.4443 9.82243 10.5399 9.91802 10.6833 9.91802H11.472C11.5915 9.91802 11.6871 9.82243 11.6871 9.67904V8.91428C11.6871 8.77089 11.5915 8.67529 11.472 8.67529Z">
                    </path>
                    <path
                      d="M3.8958 11.1606H3.10715C2.96376 11.1606 2.86816 11.2562 2.86816 11.3996V12.1644C2.86816 12.3078 2.96376 12.4034 3.10715 12.4034H3.8958C4.03919 12.4034 4.13479 12.3078 4.13479 12.1644V11.3996C4.13479 11.2562 4.03919 11.1606 3.8958 11.1606Z">
                    </path>
                    <path
                      d="M6.429 11.1606H5.64035C5.49696 11.1606 5.40137 11.2562 5.40137 11.3996V12.1644C5.40137 12.3078 5.49696 12.4034 5.64035 12.4034H6.429C6.57239 12.4034 6.66799 12.3078 6.66799 12.1644V11.3996C6.66799 11.2562 6.57239 11.1606 6.429 11.1606Z">
                    </path>
                    <path
                      d="M8.93779 11.1606H8.14914C8.00575 11.1606 7.91016 11.2562 7.91016 11.3996V12.1644C7.91016 12.3078 8.00575 12.4034 8.14914 12.4034H8.93779C9.08118 12.4034 9.17678 12.3078 9.17678 12.1644V11.3996C9.17678 11.2562 9.08118 11.1606 8.93779 11.1606Z">
                    </path>
                    <path
                      d="M11.472 11.1606H10.6833C10.5399 11.1606 10.4443 11.2562 10.4443 11.3996V12.1644C10.4443 12.3078 10.5399 12.4034 10.6833 12.4034H11.472C11.5915 12.4034 11.6871 12.3078 11.6871 12.1644V11.3996C11.6871 11.2562 11.5915 11.1606 11.472 11.1606Z">
                    </path>
                  </svg>
                </span>
                {{ ($blog->published_at ?? $blog->created_at)->format('d M Y') }}
              </p>
            </div>
          </div>

          @if ($blog->cover_image)
          <div class="animate_top ke me xf">
            <img src="{{ media_url($blog->cover_image) }}" alt="{{ $blog->title }}" class="oc">
          </div>
          @endif

          <div class="animate_top fo project_content">
            {!! $blog->content !!}
          </div>

          {{-- Contact With Us (subject pre-filled with this post's title) --}}
          @include('front.layouts.blog-contact')
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Suggested posts: up to 3 other published articles --}}
@if ($suggested->isNotEmpty())
<section class="ke eg mm mn">
  <div class="a">
    <div class="oc tf">
      <div class="animate_top la ib nd yg">
        <h2 class="pa yg bh lh sh zh mj ml rl _n ao">Suggested Posts</h2>
        <p class="mh uh ol sl">More articles you might enjoy...</p>
      </div>
    </div>
    <div class="sb wd xd pg mt-4">
      <div class="oc tf" style="display: flex; flex-wrap: wrap; gap: 24px;">
        @foreach ($suggested as $s)
        <div style="flex: 1 1 260px; max-width: 340px;">
          <a href="{{ route('front.blog', $s->slug) }}" class="oc" style="text-decoration: none; color: inherit;">
            @if ($s->cover_image)
            <img src="{{ media_url($s->cover_image) }}" alt="{{ $s->title }}" class="oc"
              style="height: 180px; object-fit: cover; border-radius: 12px;">
            @endif
            <h5 class="mt-3" style="color: #1D2433;">{{ $s->title }}</h5>
            <p class="mh">{{ \Illuminate\Support\Str::limit(strip_tags($s->excerpt ?? $s->content), 100) }}</p>
            <p class="mh">{{ ($s->published_at ?? $s->created_at)->format('d M Y') }}</p>
          </a>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

@endsection
