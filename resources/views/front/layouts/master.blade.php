<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dev Home</title>
    <link rel="icon" href="{{ media_url($settings?->fav_icon) ?? 'default-icon.png' }}" type="image/x-icon">
    <link href="{{ asset('front/css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="c af" x-data="{
    page: 'home',
    'loaded': true,
    'darkMode': true,
    'stickyMenu': false,
    'navigationOpen': false,
    'scrollTop': false,
    scrollSpy() {
        const offset = 80;
        let current = 'home';
        document.querySelectorAll('section[id]').forEach((s) => {
            if (s.offsetTop - offset <= window.scrollY) {
                current = s.id;
            }
        });
        this.page = current;
    }
}" x-init="let savedMode = localStorage.getItem('darkMode'); darkMode = savedMode === null ? true : JSON.parse(savedMode); $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value))); scrollSpy()" @scroll.window="scrollSpy()" :class="{ 'c af': darkMode === true }">
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded" x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })" class="i l n cd lc ha af" :class="darkMode ? 'af' : 'cf'">
        <div class="e"></div>
    </div>
    <!-- ===== Preloader End ===== -->

    <!-- ===== Header Start ===== -->

    @include('front.layouts.header')

    <!-- ===== Header End ===== -->

    <main>

        @yield('content')



    </main>
    <!-- ===== Footer Start ===== -->
    @include('front.layouts.footer')
    <!-- ====== Footer Section Start -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    <script defer src="{{ asset('front/js/bundle.js') }}"></script>
    <script>
        window.addEventListener('hashchange', function () {
            history.replaceState(null, '', window.location.pathname + window.location.search);
        });
    </script>
</body>

</html>
