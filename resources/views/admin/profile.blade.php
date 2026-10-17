@extends('admin.layouts.app')
@section(section: 'content')
    <div class="container">
        <div class="main-body">
            <div class="row container-fluid pt-4 px-4">
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
                        <div class="card newBack">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Full Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="email" class="form-control"
                                            value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Image</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="image" class="form-control" id="formFile">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Custom CV</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="custom_cv" class="form-control" id="formFile">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Designation</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="designation" class="form-control"
                                            value="{{ $user->designation }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Facebook Url</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="facebook_url" class="form-control"
                                            value="{{ $user->facebook_url }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Linkedin Url</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="linkedin_url" class="form-control"
                                            value="{{ $user->linkedin_url }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Github Url</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="github_url" class="form-control"
                                            value="{{ $user->github_url }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Experience</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="experience" class="form-control"
                                            value="{{ $user->experience }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Languages</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="languages" class="form-control"
                                            value="{{ is_array($user->languages) ? implode(', ', $user->languages) : $user->languages }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Change Password</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class=" row container-fluid pt-4 px-4">
                                    <div class="col-sm-9 text-secondary">
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
