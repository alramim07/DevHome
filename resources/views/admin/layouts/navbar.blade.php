            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    {{-- Notification Bell --}}
                    <div class="nav-item dropdown me-3" id="notificationDropdown">
                        <a href="#" class="nav-link position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="fa fa-bell text-white"></i>
                            <span id="notifDot"
                                  class="position-absolute bg-danger border border-secondary rounded-circle"
                                  style="display:none; width:9px; height:9px; top:6px; right:4px;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0 p-0"
                             style="min-width:300px; max-width:340px;">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-dark">
                                <span class="text-white fw-semibold small">Notifications</span>
                                <button type="button" id="markAllRead"
                                        class="btn btn-sm btn-link text-primary p-0 text-decoration-none small">
                                    Mark all as read
                                </button>
                            </div>
                            <div id="notifList">
                                <div class="text-center text-secondary py-3 small">No new notifications</div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="{{ $user->image ? media_url($user->image) : asset('images/default-user.png') }}" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">{{ $user->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="{{ route('profile') }}" class="dropdown-item">My Profile</a>
                            
                            <a href="{{ route('logout') }}" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>