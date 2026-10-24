@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .social-link i {
            font-size: 24px;
        }
        .team-swiper {
            padding-bottom: 40px;
            overflow: hidden;
        }

        /* Fallback: if Swiper is not initialized (carousel not working), center slides via CSS */
        .team-swiper:not(.swiper-initialized) .swiper-wrapper {
            display: flex;
            justify-content: center !important;
            flex-wrap: wrap;
        }

        .team-swiper .swiper-slide {
            height: auto;
            display: flex;
            justify-content: center;
        }

        .team-member-card {
            width: 100%;
            max-width: 12.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .team-member-img-wrap {
            width: 100%;
            aspect-ratio: 1952 / 2158; /* Exact aspect ratio of Al-Ramim's card photo */
            overflow: hidden;
            border-radius: 0.375rem;
            background-color: #1e293b;
        }

        .team-member-img-wrap .team-member-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            display: block;
        }

        .team-member-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .team-member-name {
            min-height: 2.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.25rem;
        }

        .team-member-role {
            min-height: 1.25rem;
        }

        .team-member-socials {
            margin-top: auto;
        }
    </style>
@endpush
@php
    $teamCount = count($teams);
@endphp
<section id="team" class="ke eg mm mn">
    <div class="a">
        <div class="-ud-mx-4 sb wd">
            <div class="oc tf">
                <div class="animate_top la ib kd yg wl">
                    <span class="yh ta qb ah nh">Our Team</span>
                    <h2 class="zh mj va bh lh lk ll">Our Team Members</h2>
                </div>
            </div>
        </div>

        <div class="swiper team-swiper -ud-mx-4">
            <div class="swiper-wrapper">
                @foreach ($teams as $member)
                    <div class="swiper-slide">
                        <div class="la cb md team-member-card">
                            <div class="xa ke oe team-member-img-wrap">
                                <img src="{{ media_url($member->image) }}" alt="image" class="oc team-member-img" />
                            </div>
                            <div class="yg team-member-body">
                                <h4 class="zh mj ta nh team-member-name">
                                    @if (!$member->slug)
                                        {{ $member->name }}
                                    @else
                                        <a href="{{ route('team', $member->slug) }}">{{ $member->name }}</a>
                                    @endif
                                </h4>
                                <p class="xa ih xh oh newpad team-member-role">{{ $member->designation }}</p>

                                <div class="sb xd yd d-flex align-items-center team-member-socials">
                                    @php
                                        $platforms = [
                                            'facebook' => [
                                                'url' => $member->facebook_url,
                                                'icon' => 'bx bxl-facebook-circle',
                                            ],
                                            'github' => ['url' => $member->github_url, 
                                            'icon' => 'bx bxl-github'],
                                            'linkedin' => [
                                                'url' => $member->linkedin_url,
                                                'icon' => 'bx bxl-linkedin-square',
                                            ],
                                        ];
                                    @endphp
                                    @foreach ($platforms as $platform => $data)
                                        <a href="{{ $data['url'] }}" class="_i na ai social-link" target="_blank">
                                            <i class="{{ $data['icon'] }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($teamCount > 1)
                <div class="swiper-pagination" style="bottom: 0;"></div>
            @endif
        </div>
    </div>
</section>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var teamCount = {{ $teamCount }};
            if (teamCount <= 0) return;

            var swiperOptions = {
                slidesPerView: 1,
                spaceBetween: 20,
                watchOverflow: true,
                centerInsufficientSlides: true,
                loop: teamCount > 4,
                breakpoints: {
                    640: {
                        slidesPerView: 2
                    },
                    1024: {
                        slidesPerView: 3
                    },
                    1280: {
                        slidesPerView: 4
                    },
                },
            };

            if (teamCount > 1) {
                swiperOptions.pagination = {
                    el: '.swiper-pagination',
                    clickable: true,
                };
            }

            if (teamCount > 4) {
                swiperOptions.autoplay = {
                    delay: 2000,
                    disableOnInteraction: false,
                };
            }

            new Swiper('.team-swiper', swiperOptions);
        });
    </script>
@endpush
