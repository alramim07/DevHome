<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Dev Home | Admin</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ media_url($siteSettings?->fav_icon) ?? 'default-icon.png' }}"
        type="image/x-icon">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('admin/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Template Stylesheet -->
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        @include('admin.layouts.sidebar')
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            @include('admin.layouts.navbar')
            <!-- Navbar End -->
            @include('admin.layouts.messages')

            @yield('content')


            <!-- Footer Start -->
            @include('admin.layouts.footer')
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin/lib/chart/chart.min.js') }}"></script>
    <script src="{{ asset('admin/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('admin/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('admin/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('admin/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('admin/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('admin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('admin/js/main.js') }}"></script>
    <!-- Text Editor (Self-hosted TinyMCE) -->
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: 'textarea#description',
            license_key: 'gpl',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media',
                'image', // Added 'image' plugin
                'searchreplace', 'table', 'visualblocks', 'wordcount', 'autoresize'
            ],
            // Added 'image' to the toolbar
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | link image media table | align lineheight | checklist numlist bullist | emoticons charmap | removeformat',

            // Tell TinyMCE to allow file picking for both images and media
            file_picker_types: 'image media',

            file_picker_callback: function(cb, value, meta) {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');

                // Automatically set the file filter based on which button you clicked
                if (meta.filetype === 'image') {
                    input.setAttribute('accept', 'image/*');
                } else if (meta.filetype === 'media') {
                    input.setAttribute('accept', 'video/*,audio/*');
                }

                input.onchange = function() {
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function() {
                        const id = 'blobid' + (new Date()).getTime();
                        const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        const base64 = reader.result.split(',')[1];
                        const blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        // This callback now sends the correct data back to the correct dialog
                        cb(blobInfo.blobUri(), {
                            title: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                };

                input.click();
            },

            min_height: 500,
            autoresize_bottom_margin: 20,
            promotion: false,
            statusbar: false
        });
    </script>
    <script>
    (function () {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        var typeIcons = {
            email:        'fa-envelope',
            contact:      'fa-comments',
            join_request: 'fa-user-plus'
        };

        function renderNotifications(data) {
            var dot  = document.getElementById('notifDot');
            var list = document.getElementById('notifList');
            if (!dot || !list) return;

            if (!data.count) {
                dot.style.display = 'none';
                list.innerHTML = '<div class="text-center text-secondary py-3 small">No new notifications</div>';
                return;
            }

            dot.style.display = 'block';

            var html = data.notifications.map(function (n) {
                var icon = typeIcons[n.type] || 'fa-bell';
                var body = n.body
                    ? '<div class="text-secondary" style="font-size:0.73rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + n.body + '</div>'
                    : '';
                return '<a href="' + n.url + '" class="d-flex align-items-start px-3 py-2 border-bottom border-dark text-decoration-none notif-link">'
                    + '<div class="me-2 mt-1"><i class="fa ' + icon + ' text-primary small"></i></div>'
                    + '<div style="overflow:hidden;flex:1;">'
                    + '<div class="text-white small fw-semibold" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + n.title + '</div>'
                    + body
                    + '<div class="text-primary" style="font-size:0.7rem;">' + n.created_at + '</div>'
                    + '</div></a>';
            }).join('');

            list.innerHTML = html;
        }

        function fetchNotifications() {
            $.getJSON('/notifications/feed', renderNotifications).fail(function () {});
        }

        $(document).ready(function () {
            fetchNotifications();
            setInterval(fetchNotifications, 60000);

            $(document).on('click', '#markAllRead', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $.post('/notifications/mark-read', { _token: csrfToken }, function () {
                    renderNotifications({ count: 0, notifications: [] });
                });
            });
        });
    }());
    </script>
    @stack('scripts')
</body>

</html>
