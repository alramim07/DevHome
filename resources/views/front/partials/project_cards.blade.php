<style>
    .project-card-zoom {
        opacity: 0;
        transform: scale(0.4);
        transform-origin: center;
        transition: opacity .6s ease, transform .6s cubic-bezier(0.22, 1, 0.36, 1);
        will-change: opacity, transform;
    }

    .project-card-zoom.is-zoomed {
        opacity: 1;
        transform: scale(1);
    }
</style>

@forelse ($projects as $project)
    <div class="oc tf bl/2 gn/3 qb">
        <div class="k eb project-card-zoom" x-data="{ shown: false }" x-intersect.once="shown = true"
            :class="shown ? 'is-zoomed' : ''" :style="'transition-delay: ' + {{ $loop->index }} * 70 + 'ms'">
            <div class="ke se imgnte">
                <img src="{{ asset("$project->thumbnail") }}" alt="portfolio" class="oc productimg" />
            </div>
            <div class="k ca ma -ud-mt-20 ne cf hj fg ag yg hi">
                <span class="yh ta qb fh nh">
                    @if (!empty($project->tech_stack) && is_array($project->tech_stack))
                        {{ \Illuminate\Support\Str::limit(implode(', ', $project->tech_stack), 30, '...') }}
                    @else
                        No Tech Stack
                    @endif
                </span>
                <h3 class="zh mj va dh lh">{{ $project->title }}</h3>
                <a href="{{ route('project.view', $project->slug) }}" class="vi ui rb oe ve rf gg fh nh li aj">
                    View Details
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="oc tf w-full text-center py-10 center-text">
        @if (Route::is('team'))
            <h4 class="zh mj va dh lh">{{ Str::title($user->name) }} currently don't have any projects</h4>
            <p>Stay tuned for further updates</p>
        @else
            <h4 class="zh mj va dh lh">No projects found for this technology.</h4>
            <p>Try selecting a different filter.</p>
        @endif
    </div>
@endforelse
