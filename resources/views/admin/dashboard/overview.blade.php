<div class="container-fluid pt-4 px-4">
  @if ($user->isSuperAdmin())
    {{-- Section 1: Entire Website Total Data (Super Admin Only) --}}
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 text-white">
          <i class="fa fa-globe text-primary me-2"></i>Website Overview
        </h6>
      </div>
      <div class="row g-4">
        <div class="col-sm-6 col-xl-3 d-flex">
          <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
            <i class="fa fa-th-large fa-3x text-primary flex-shrink-0"></i>
            <div class="ms-3 text-end">
              <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Total Projects</p>
              <h5 class="mb-0 text-white">{{ number_format($globalTotalProjects ?? 0) }}</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
          <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
            <i class="fa fa-globe fa-3x text-primary flex-shrink-0"></i>
            <div class="ms-3 text-end">
              <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Total Views</p>
              <h5 class="mb-0 text-white">{{ number_format($globalTotalViews ?? 0) }}</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
          <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
            <i class="fa fa-chart-line fa-3x text-primary flex-shrink-0"></i>
            <div class="ms-3 text-end">
              <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Today's Views</p>
              <h5 class="mb-0 text-white">{{ number_format($globalTodaysViews ?? 0) }}</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3 d-flex">
          <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
            <i class="fa fa-user-plus fa-3x text-primary flex-shrink-0"></i>
            <div class="ms-3 text-end">
              <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Today's Join Requests</p>
              <h5 class="mb-0 text-white">{{ number_format($globalTodaysJoinRequests ?? 0) }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- Section 2: Account Data (Super Admin's own account data, or member account data) --}}
  <div>
    @if ($user->isSuperAdmin())
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 text-white">
          <i class="fa fa-user text-primary me-2"></i>My Account Overview
        </h6>
      </div>
    @endif
    <div class="row g-4">
      <div class="col-sm-6 col-xl-3 d-flex">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
          <i class="fa fa-th-large fa-3x text-primary flex-shrink-0"></i>
          <div class="ms-3 text-end">
            <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Total Projects</p>
            <h5 class="mb-0 text-white">{{ number_format($totalProjects) }}</h5>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3 d-flex">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
          <i class="fa fa-globe fa-3x text-primary flex-shrink-0"></i>
          <div class="ms-3 text-end">
            <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Total Views</p>
            <h5 class="mb-0 text-white">{{ number_format($totalViews) }}</h5>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3 d-flex">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
          <i class="fa fa-chart-line fa-3x text-primary flex-shrink-0"></i>
          <div class="ms-3 text-end">
            <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Today's Views</p>
            <h5 class="mb-0 text-white">{{ number_format($todaysViews) }}</h5>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3 d-flex">
        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-3 p-xl-4 w-100 h-100">
          <i class="fa fa-envelope fa-3x text-primary flex-shrink-0"></i>
          <div class="ms-3 text-end">
            <p class="mb-2 text-muted" style="font-size: 0.88rem; white-space: nowrap;">Today's Emails</p>
            <h5 class="mb-0 text-white">{{ number_format($todaysEmails) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
