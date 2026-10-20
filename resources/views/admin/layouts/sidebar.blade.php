<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-secondary navbar-dark">
        <a href="{{ route('dashboard') }}" class="navbar-brand mx-4 mb-3">
            <img class="text-primary" src="{{ media_url($siteSettings?->logo) ?? 'default-icon.png' }}"
                alt="Logo" style="height: 50px;">
        </a>
        <div class="d-flex align-items-center ms-4 mb-4"> 
            <div class="position-relative">
                {{-- Using your asset helper for the perfect circle image --}}
                <img class="rounded-circle"
                    src="{{ $user->image ? media_url($user->image) : asset('images/default-user.png') }}" alt=""
                    style="width: 40px; height: 40px; object-fit: cover;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ $user->name }}</h6>
                <span>{{ $user->designation }}</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            {{-- Dashboard Link --}}
            <a href="{{ route('dashboard') }}"
                class="nav-item nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>

            {{-- Projects Dropdown --}}
            <div class="nav-item dropdown">
                {{-- Parent is 'active' if any project route is visited --}}
                <a href="#"
                    class="nav-link dropdown-toggle {{ request()->routeIs(['add.project', 'list.project']) ? 'active' : '' }}"
                    data-bs-toggle="dropdown">
                    <i class="fa fa-laptop me-2"></i>Projects
                </a>
                {{-- Dropdown is 'show' if a child route is active --}}
                <div
                    class="dropdown-menu bg-transparent border-0 {{ request()->routeIs(['add.project', 'list.project']) ? 'show' : '' }}">
                    <a href="{{ route('add.project') }}"
                        class="dropdown-item {{ request()->routeIs('add.project') ? 'active text-primary' : '' }}">Add
                        Projects</a>
                    <a href="{{ route('list.project') }}"
                        class="dropdown-item {{ request()->routeIs('list.project') ? 'active text-primary' : '' }}">Projects
                        List</a>
                </div>
            </div>

            <a href="{{ route('website.settings') }}" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Website
                Settings</a>
            <a href="{{ route('email.list') }}" class="nav-item nav-link"><i
                    class="fa fa-keyboard me-2"></i>E-mails</a>
            @if (auth()->id() === 1)
            <a href="{{ route('website.contacts') }}" class="nav-item nav-link"><i
                    class="fa fa-keyboard me-2"></i>Website Contacts</a>
            <a href="{{ route('dev.list') }}" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Dev List</a>
            <a href="{{ route('join.list') }}" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Join Requests</a>
            @endif
            <a href="{{ route('todo') }}" class="nav-item nav-link"><i class="fa fa-table me-2"></i>To Do List</a>
        </div>
    </nav>
</div>
