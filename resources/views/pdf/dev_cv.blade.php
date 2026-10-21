<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CV Of {{ $user->name }}</title>
    <style>
        {!! file_get_contents(public_path('front/css/style.css')) !!} body {
            
            font-family: 'DejaVu Sans', sans-serif;
            /* Best for PDFs */
        }
    </style>
</head>

<body>
    <section class="">
        <div class="card-container">
            <div class="profile-card" style="width: 1333px !important; max-width: 95% !important; padding: 32px;">
                <div class="profile-body">
                    <div class="profile-main-content">
                        @if ($user->image && file_exists(public_path($user->image)))
                            <img class="profile-avatar"
                                src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path($user->image))) }}">
                        @endif

                        <div class="profile-info">
                            <span class="badge-pro">Active</span>
                            <h4 class="profile-name">{{ Str::title($user->name) }}</h4>
                            <span class="profile-role">{{ $user->designation }}</span>
                        </div>
                    </div>

                    <div class="profile-stacks">
                        <div class="stack-label">Expert Stacks</div>
                        <ul style="list-style: none; padding: 0;">
                            @php
                                $languages = (is_array($user->languages) ? $user->languages : (is_string($user->languages) && !empty($user->languages) ? explode(',', $user->languages) : []));
                            @endphp
                            @foreach ($languages as $language)
                                <li class="stack-item">{{ ucfirst(trim($language)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="profile-section" style="padding: 0px !important">
                    <div class="profile-detail-card">

                        <div class="card-header">
                            <h2 class="card-title">Personal information</h2>
                        </div>

                        <hr class="card-divider" />

                        <div class="profile-grid">
                            <div class="column-spacer">
                                <dl>
                                    <div>
                                        <dt class="profile-label">Full name</dt>
                                        <dd class="profile-value">{{ Str::title($user->name) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="profile-label">Biography</dt>
                                        <dd class="profile-value">{{ $user->biography }}</dd>
                                    </div>
                                    <div>
                                        <dt class="profile-label">Social</dt>
                                        <dd class="social-list">
                                            <a href="{{ $user->facebook_url }}" class="hover:text-white"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                                    viewBox="0 0 30 30" fill="none" stroke="#1877F2" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-facebook-icon lucide-facebook">
                                                    <path
                                                        d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                                                </svg></a>
                                            <a href="{{ $user->linkedin_url }}" class="hover:text-white"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                                    viewBox="0 0 30 30" fill="none" stroke="#0077B5" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-linkedin-icon lucide-linkedin">
                                                    <path
                                                        d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                                                    <rect width="4" height="12" x="2" y="9" />
                                                    <circle cx="4" cy="4" r="2" />
                                                </svg></a>
                                            <a href="{{ $user->github_url }}" class="hover:text-white"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                                    viewBox="0 0 30 30" fill="none" stroke="#000000" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-github-icon lucide-github">
                                                    <path
                                                        d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4" />
                                                    <path d="M9 18c-4.51 2-5-2-7-2" />
                                                </svg></a>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="column-spacer">
                                <dl>
                                    <div>
                                        <dt class="profile-label">Email Adress</dt>
                                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $user->email }}" target="_blank">
                                            <dd class="profile-value iconset"><span><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="#3056d3"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-at-sign-icon lucide-at-sign">
                                                        <circle cx="12" cy="12" r="4" />
                                                        <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8" />
                                                    </svg></span>{{ $user->email }}</dd>
                                        </a>
                                    </div>
                                    <div>
                                        <dt class="profile-label">Tech Stack Skills</dt>
                                        <dd class="software-list">
                                            @php
                                                $languages = (is_array($user->languages) ? $user->languages : (is_string($user->languages) && !empty($user->languages) ? explode(',', $user->languages) : []));
                                            @endphp
                                            @foreach ($languages as $language)
                                                <span
                                                    class="skill-badge profile-value">{{ ucfirst(trim($language)) }}</span>
                                            @endforeach
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="profile-label">Years of Experience</dt>
                                        <dd class="profile-value">{{ $user->experience }}</dd>
                                    </div>
                                </dl>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <section class="ke eg mm mn">
        <div style="margin-top: 30px; margin-bottom: 20px; text-align: center;">
            <h2 class="zh mj va bh lh lk ll">
                Portfolio Of {{ Str::title($user->name) }}
            </h2>
        </div>

        <div class="project-grid">
            @forelse ($projects as $project)
                <div class="project-card no-break">
                    <div class="ke se imgnte">
                        {{-- Use public_path for thumbnails --}}
                        <img src="{{ media_url($project->thumbnail) }}" alt="portfolio" class="productimg" />
                    </div>
                    <div style="padding-top: 10px;">
                        <span
                            style="font-size: 10px; color: #3056d3; font-weight: bold; display: block; margin-bottom: 5px;">
                            @if (!empty($project->tech_stack) && is_array($project->tech_stack))
                                {{ implode(', ', $project->tech_stack) }}
                            @endif
                        </span>
                        <h3 style="font-size: 16px; margin: 0;">{{ $project->title }}</h3>
                        <a href="{{ route('project.view', $project->slug) }}"
                            class="vi ui rb oe ve rf gg fh nh li aj">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div style="width: 100%; text-align: center; padding: 40px;">
                    <h4>No projects found for this profile.</h4>
                </div>
            @endforelse
        </div>



    </section>

    <section class="ke eg mm mn">
        <div style="text-align: center;">
            <p>
                Need a help? Contact With Me
            </p>
        </div>
        <div style="text-align: center; color: #3056d3;">
            <a href="mailto:{{ $user->email }}">
                {{ $user->email }}
            </a>
        </div>

    </section>
</body>

</html>
