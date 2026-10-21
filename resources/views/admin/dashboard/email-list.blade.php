<div class="col-sm-12 col-md-6 col-xl-4">
    <div class="h-100 bg-secondary rounded p-4 d-flex flex-column">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0">Emails</h6>
            <a href="{{ route('email.list') }}">Show All</a>
        </div>
        <div class="email-scroll-container flex-grow-1" style="max-height: 260px; overflow-y: auto; overflow-x: hidden; padding-right: 4px;">
            @forelse ($dashemails as $emails)
            <div class="d-flex align-items-center border-bottom py-3">                        
                <div class="w-100 ms-2">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-0 small text-truncate" style="max-width: 170px;">{{ $emails->email }}</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">{{ $emails->created_at->diffForHumans() }}</small>
                    </div>
                    <span class="small text-muted">{{ $emails->subject }}</span>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4 small">No emails yet.</div>
            @endforelse
        </div>
    </div>
</div>
