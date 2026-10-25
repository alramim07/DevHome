@extends('admin.layouts.app')
@section(section: 'content')
    <div class="container">
        <div class="main-body">
            <div class="row pt-4 px-2 px-md-4">
                <div class="col-lg-4">
                    <div class="card newBack">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <img src="{{ $user->image ? media_url($user->image) : asset('images/default-user.png') }}"
                                    alt="Admin" width="110" width="110"
                                    class="rounded-circle p-1 bg-primary profile-pic-custom">
                                <div class="mt-3">
                                    <h4 class="unamec">{{ $user->name }}</h4>
                                    <p class="text-secondary mb-1 newCOlor">{{ $user->designation }}</p>
                                </div>
                            </div>
                            <hr class="my-4 ">
                            <ul class="list-group box-shad list-group-flush">
                                <li
                                    class="list-group-item bcCol d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-globe me-2 icon-inline" style="color: #7C3AED;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <path
                                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                            </path>
                                        </svg>Site Profile</h6>
                                    @if ($user->slug)
                                        <a href="{{ route('team', $user->slug) }}"
                                            class="text-secondary newCOlor">{{ Str::title($user->name) }}</a>
                                    @else
                                        <span class="text-secondary newCOlor">{{ $user->name }}</span>
                                    @endif
                                </li>
                                <li
                                    class="list-group-item bcCol d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="#fff" class="me-2 icon-inline">
                                            <path
                                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.042-1.416-4.042-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                        </svg>
                                        Github
                                    </h6>
                                    <a href="{{ $user->github_url }}" class="text-secondary newCOlor" target="_blank">
                                        {{ str(trim(parse_url($user->github_url, PHP_URL_PATH), '/'))->ucfirst() }}
                                    </a>

                                </li>
                                <li
                                    class="list-group-item bcCol d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="#1877F2" class="me-2 icon-inline">
                                            <path
                                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                        </svg>
                                        Facebook
                                    </h6>
                                    <a href="{{ $user->facebook_url }}" class="text-secondary newCOlor" target="_blank">
                                        {{str( trim(parse_url($user->facebook_url, PHP_URL_PATH), '/'))->ucfirst() }}
                                    </a>
                                </li>
                                <li
                                    class="list-group-item bcCol d-flex justify-content-between align-items-center flex-wrap">
                                    <h6 class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="#0A66C2" class="me-2 icon-inline">
                                            <path
                                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                        </svg>
                                        LinkedIn
                                    </h6>
                                    <a href="{{ $user->linkedin_url }}" class="text-secondary newCOlor" target="_blank">
                                        {{str( trim(parse_url($user->linkedin_url, PHP_URL_PATH), '/'))->ucfirst() }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <form id="profileUpdateForm" action="{{ route('profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card newBack shadow-sm border-0">
                            <div class="card-header border-bottom border-dark bg-transparent py-3 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0 text-white"><i class="fa fa-user-edit text-primary me-2"></i>Edit Profile</h5>
                                    <small class="text-muted">Manage your personal information, public profile, and account details</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm px-3">
                                    <i class="fa fa-save me-1"></i> Save Changes
                                </button>
                            </div>

                            <div class="card-body p-4">
                                {{-- Section 1: Personal & Professional Details --}}
                                <div class="mb-4">
                                    <h6 class="text-primary text-uppercase font-weight-bold mb-3 small" style="letter-spacing: 0.5px;">
                                        <i class="fa fa-id-card me-1"></i> Personal & Professional Info
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-user"></i></span>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-envelope"></i></span>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Designation / Role</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-briefcase"></i></span>
                                                <input type="text" name="designation" class="form-control"
                                                    value="{{ old('designation', $user->designation) }}" placeholder="e.g. Software Engineer">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Experience</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-clock"></i></span>
                                                <input type="text" name="experience" class="form-control"
                                                    value="{{ old('experience', $user->experience) }}" placeholder="e.g. 3+ Years">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-light small fw-semibold">Public Profile Slug / URL</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark text-muted border-secondary">{{ url('/team') }}/</span>
                                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                                    value="{{ old('slug', $user->slug) }}" placeholder="e.g. {{ Str::slug($user->name) }}">
                                            </div>
                                            @error('slug')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted d-block mt-1">
                                                <i class="fa fa-link me-1"></i> Public profile link: <code>{{ url('/team') }}/{{ $user->slug ?? Str::slug($user->name) }}</code>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-4" style="opacity: 0.25;">

                                {{-- Section 2: Biography & Skills --}}
                                <div class="mb-4">
                                    <h6 class="text-primary text-uppercase font-weight-bold mb-3 small" style="letter-spacing: 0.5px;">
                                        <i class="fa fa-align-left me-1"></i> Biography & Skills
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label text-light small fw-semibold">Professional Biography</label>
                                            <textarea name="biography" class="form-control @error('biography') is-invalid @enderror" rows="3"
                                                placeholder="Brief professional biography...">{{ old('biography', $user->biography) }}</textarea>
                                            @error('biography')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Brief overview shown on your public profile card.</small>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-light small fw-semibold">Tech Stack / Languages</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-code"></i></span>
                                                <input type="text" name="languages" class="form-control"
                                                    value="{{ old('languages', is_array($user->languages) ? implode(', ', $user->languages) : $user->languages) }}"
                                                    placeholder="e.g. PHP, Laravel, JavaScript, Vue.js, MySQL">
                                            </div>
                                            <small class="text-muted">Comma-separated list of technologies and skills.</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-4" style="opacity: 0.25;">

                                {{-- Section 3: Media & Documents --}}
                                <div class="mb-4">
                                    <h6 class="text-primary text-uppercase font-weight-bold mb-3 small" style="letter-spacing: 0.5px;">
                                        <i class="fa fa-folder-open me-1"></i> Media & Documents
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Profile Photo</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-image"></i></span>
                                                <input type="file" name="image" class="form-control" id="formFile" accept="image/*">
                                            </div>
                                            <small class="text-muted">Square image recommended (JPG, PNG).</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Custom CV (PDF)</label>
                                            @if (!empty($user->custom_cv))
                                                @php
                                                    $cvFilename = basename(parse_url($user->custom_cv, PHP_URL_PATH) ?? $user->custom_cv);
                                                @endphp
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark text-muted border-secondary">
                                                        <i class="fa fa-file-pdf text-danger"></i>
                                                    </span>
                                                    <input type="text" class="form-control bg-dark text-light border-secondary" readonly
                                                        value="{{ $cvFilename }}" title="{{ $cvFilename }}">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateCvModal">
                                                        <i class="fa fa-upload me-1"></i> Update CV
                                                    </button>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    Current: <code>{{ $cvFilename }}</code> &bull; <strong>.pdf</strong> up to 10MB
                                                </small>
                                            @else
                                                <div class="input-group">
                                                    <input type="file" name="custom_cv" class="form-control @error('custom_cv') is-invalid @enderror" accept=".pdf">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateCvModal">
                                                        <i class="fa fa-upload me-1"></i> Upload CV
                                                    </button>
                                                </div>
                                                @error('custom_cv')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-1">Allowed formats: <strong>.pdf</strong> (Max 10MB)</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-4" style="opacity: 0.25;">

                                {{-- Section 4: Social Profiles --}}
                                <div class="mb-4">
                                    <h6 class="text-primary text-uppercase font-weight-bold mb-3 small" style="letter-spacing: 0.5px;">
                                        <i class="fa fa-share-alt me-1"></i> Social Profiles
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label text-light small fw-semibold">GitHub</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-light"><i class="fab fa-github"></i></span>
                                                <input type="url" name="github_url" class="form-control"
                                                    value="{{ old('github_url', $user->github_url) }}" placeholder="https://github.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-light small fw-semibold">LinkedIn</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-primary"><i class="fab fa-linkedin-in"></i></span>
                                                <input type="url" name="linkedin_url" class="form-control"
                                                    value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-light small fw-semibold">Facebook</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-info"><i class="fab fa-facebook-f"></i></span>
                                                <input type="url" name="facebook_url" class="form-control"
                                                    value="{{ old('facebook_url', $user->facebook_url) }}" placeholder="https://facebook.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary my-4" style="opacity: 0.25;">

                                {{-- Section 5: Account Security --}}
                                <div class="mb-2">
                                    <h6 class="text-primary text-uppercase font-weight-bold mb-3 small" style="letter-spacing: 0.5px;">
                                        <i class="fa fa-lock me-1"></i> Account Security
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-light small fw-semibold">Change Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-dark border-secondary text-muted"><i class="fa fa-key"></i></span>
                                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                                            </div>
                                            <small class="text-muted">Only fill this if you want to set a new password.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer border-top border-dark bg-transparent py-3 px-4 d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-info-circle me-1"></i> Review your changes before saving.</span>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa fa-save me-1"></i> Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal to Upload / Update Custom CV --}}
    <div class="modal fade" id="updateCvModal" tabindex="-1" aria-labelledby="updateCvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('profile.cv.update') }}" method="POST" enctype="multipart/form-data"
                  class="modal-content bg-secondary text-white border-0 shadow">
                @csrf
                <div class="modal-header border-bottom border-dark">
                    <h5 class="modal-title text-white d-flex align-items-center" id="updateCvModalLabel">
                        <i class="fa fa-file-pdf text-danger me-2"></i> Update Custom CV
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($user->custom_cv))
                        @php
                            $currentCvName = basename(parse_url($user->custom_cv, PHP_URL_PATH) ?? $user->custom_cv);
                        @endphp
                        <div class="mb-3 p-3 rounded bg-dark border border-secondary d-flex align-items-center justify-content-between">
                            <div class="text-truncate me-2">
                                <small class="text-muted d-block">Current Uploaded CV:</small>
                                <span class="text-white small fw-bold">
                                    <i class="fa fa-file-pdf text-danger me-1"></i>
                                    {{ $currentCvName }}
                                </span>
                            </div>
                            @if ($user->slug)
                                <a href="{{ route('cv.custom', $user->slug) }}" class="btn btn-sm btn-outline-info" target="_blank" title="View Current CV">
                                    <i class="fa fa-download"></i> View
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-white fw-bold">Select New Custom CV File</label>
                        <input type="file" name="custom_cv" class="form-control text-white bg-dark border-secondary" accept=".pdf" required>
                    </div>

                    <div class="alert alert-dark border-secondary text-light small mb-0">
                        <div class="d-flex">
                            <i class="fa fa-info-circle text-info me-2 mt-1"></i>
                            <div>
                                <div><strong>Allowed file extensions:</strong> <code>.pdf</code> only.</div>
                                <div class="text-muted mt-1">DevHome Custom CV section currently allows <strong>PDF</strong> documents up to <strong>10 MB</strong> in size.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-dark">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload me-1"></i> Upload & Save CV
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
