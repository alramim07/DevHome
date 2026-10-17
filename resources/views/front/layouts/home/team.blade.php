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

        .swiper-slide {
            height: auto;
        }
    </style>
@endpush
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
                        <div class="la cb md">
                            <div class="xa ke oe">
                                <img src="{{ media_url($member->image) }}" alt="image" class="oc" />
                            </div>
                            <div class="yg">
                                <h4 class="zh mj ta nh">
                                    @if (!$member->slug)
                                        {{ $member->name }}
                                    @else
                                        <a href="{{ route('team', $member->slug) }}">{{ $member->name }}</a>
                                    @endif
                                </h4>
                                <p class="xa ih xh oh newpad">{{ $member->designation }}</p>

                                <div class="sb xd yd d-flex align-items-center">
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
            <div class="swiper-pagination" style="bottom: 0;"></div>
        </div>
    </div>
</section>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.team-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 1300,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
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
            });
        });
    </script>
@endpush
