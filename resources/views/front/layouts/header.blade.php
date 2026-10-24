<header class="i l n oc fa lg qm" :class="{ 'cf kj ji mg rm li ri' : stickyMenu }"
  @scroll.window="stickyMenu = (window.scrollY > 0) ? true : false">
  <div class="a _l xd zd k">
    <div class="oc dm/4 sb xd zd">
      <a href="{{ route('home') }}">
        @if ($settings?->logo && $settings?->logo_dark)
          <img class="qd hlogo fj" src="{{ media_url($settings->logo) }}" height="50px" alt="Logo Light" />
          <img class="qd hlogo darkl ej" src="{{ media_url($settings->logo_dark) }}" height="50px" alt="Logo Dark" />
        @elseif ($settings?->logo)
          <img class="qd hlogo" src="{{ media_url($settings->logo) }}" height="50px" alt="Logo" />
        @elseif ($settings?->logo_dark)
          <img class="qd hlogo" src="{{ media_url($settings->logo_dark) }}" height="50px" alt="Logo Dark" />
        @else
          <img class="qd hlogo fj" src="images/logo-light.svg" height="50px" alt="Logo Light" />
          <img class="qd hlogo ej" src="images/logo-dark.svg" height="50px" alt="Logo Dark" />
        @endif
      </a>

      <div class="sb xd" style="gap: 12px;">
        <!-- Mobile Theme Toggle BTN -->
        <div class="header-theme-mobile">
          <button type="button"
                  class="celestial-toggle-btn"
                  @click="darkMode = !darkMode"
                  :aria-label="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                  :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
            <svg class="celestial-svg"
                 :class="{ 'is-sun': darkMode, 'is-moon': !darkMode }"
                 width="30" height="30" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <radialGradient id="celestialSunGradMob" cx="35%" cy="35%" r="65%">
                  <stop offset="0%" stop-color="#FFF59D" />
                  <stop offset="40%" stop-color="#FFB300" />
                  <stop offset="85%" stop-color="#FF8F00" />
                  <stop offset="100%" stop-color="#E65100" />
                </radialGradient>
                <linearGradient id="celestialRayGradMob" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FFE082" />
                  <stop offset="50%" stop-color="#FFA000" />
                  <stop offset="100%" stop-color="#FF6F00" />
                </linearGradient>
                <radialGradient id="celestialMoonGradMob" cx="25%" cy="30%" r="75%">
                  <stop offset="0%" stop-color="#FFFDE7" />
                  <stop offset="20%" stop-color="#F8FAFC" />
                  <stop offset="50%" stop-color="#94A3B8" />
                  <stop offset="80%" stop-color="#475569" />
                  <stop offset="100%" stop-color="#334155" />
                </radialGradient>
                <mask id="celestialEclipseMaskMob" maskUnits="userSpaceOnUse">
                  <rect x="0" y="0" width="36" height="36" fill="white" />
                  <circle class="celestial-eclipse-disk"
                          :style="darkMode ? 'transform: translate(25px, -25px);' : 'transform: translate(0px, 0px);'"
                          cx="24.5" cy="11.5" r="7.8" fill="black" />
                </mask>
              </defs>
              <g class="celestial-rays"
                 :style="darkMode ? 'opacity: 1; transform: scale(1) rotate(0deg);' : 'opacity: 0; transform: scale(0.2) rotate(135deg);'"
                 stroke="url(#celestialRayGradMob)" stroke-width="2.4" stroke-linecap="round">
                <line x1="18" y1="2" x2="18" y2="5.5" />
                <line x1="18" y1="30.5" x2="18" y2="34" />
                <line x1="2" y1="18" x2="5.5" y2="18" />
                <line x1="30.5" y1="18" x2="34" y2="18" />
                <line x1="6.7" y1="6.7" x2="9.2" y2="9.2" />
                <line x1="26.8" y1="26.8" x2="29.3" y2="29.3" />
                <line x1="6.7" y1="29.3" x2="9.2" y2="26.8" />
                <line x1="26.8" y1="9.2" x2="29.3" y2="6.7" />
              </g>
              <g mask="url(#celestialEclipseMaskMob)">
                <circle class="celestial-sun-body"
                        :style="darkMode ? 'opacity: 1;' : 'opacity: 0;'"
                        cx="18" cy="18" r="8.5" fill="url(#celestialSunGradMob)" />
                <circle class="celestial-moon-body"
                        :style="darkMode ? 'opacity: 0;' : 'opacity: 1;'"
                        cx="18" cy="18" r="8.5" fill="url(#celestialMoonGradMob)"
                        stroke="#334155" stroke-width="0.8" />
                <g class="celestial-craters"
                   :style="darkMode ? 'opacity: 0; transform: scale(0.5);' : 'opacity: 1; transform: scale(1);'">
                  <circle cx="13.5" cy="18" r="1.4" fill="#1E293B" opacity="0.5" />
                  <circle cx="16.5" cy="22.5" r="1.1" fill="#1E293B" opacity="0.45" />
                  <circle cx="15.2" cy="13.8" r="0.9" fill="#1E293B" opacity="0.4" />
                </g>
              </g>
            </svg>
          </button>
        </div>

        <!-- Hamburger Toggle BTN -->
        <button class="am qb" @click="navigationOpen = !navigationOpen">
          <span class="qb k td xc fc">
            <span class="du-block j o oc ec">
              <span class="qb k n l ff lj te zc hc oa ti si mi" :class="{ '_c ni': !navigationOpen }"></span>
              <span class="qb k n l ff lj te zc hc oa ti si oi" :class="{ '_c bo': !navigationOpen }"></span>
              <span class="qb k n l ff lj te zc hc oa ti si pi" :class="{ '_c qi': !navigationOpen }"></span>
            </span>
            <span class="du-block j o oc ec rd">
              <span class="qb ff lj te ti si ni j w n ad ec" :class="{ 'ic mi': !navigationOpen }"></span>
              <span class="qb ff lj te ti si bo j l y oc hc" :class="{ 'ic co': !navigationOpen }"></span>
            </span>
          </span>
        </button>
        <!-- Hamburger Toggle BTN -->
      </div>
    </div>

    <div class="oc fm/4 ic bm h tl _l xd zd" :class="{ 'f cf ij do jc mc le oe mb pf': navigationOpen }">
      <nav class="new-nav">
        <ul class="sb hm vd gm ce km">
          <li class="nav__menu sm" :class="{ 'tm' : stickyMenu }">
            <a href="{{ route('home') }}" class="zh mj _i nj" :class="{'bi' :page === 'home'}">Home</a>
          </li>
          <li class="nav__menu sm" :class="{ 'tm' : stickyMenu }">
            <a href="/#about" class="zh mj _i nj" :class="{'bi' :page === 'about'}">About Us</a>
          </li>
          <li class="nav__menu sm" :class="{ 'tm' : stickyMenu }">
            <a href="/#projects" class="zh mj _i nj" :class="{'bi' :page === 'projects'}">Projects</a>
          </li>
          <li class="nav__menu sm" :class="{ 'tm' : stickyMenu }">
            <a href="/#team" class="zh mj _i nj" :class="{'bi' :page === 'team'}">Team</a>
          </li>
          <li class="nav__menu sm" :class="{ 'tm' : stickyMenu }">
            <a href="/#contact" class="zh mj _i nj" :class="{'bi' :page === 'contact'}">Contact</a>
          </li>
        </ul>
      </nav>

      <div class="sb xd be nb vl">
        <div class="header-theme-desktop">
          <button type="button"
                  class="celestial-toggle-btn"
                  @click="darkMode = !darkMode"
                  :aria-label="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                  :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
            <svg class="celestial-svg"
                 :class="{ 'is-sun': darkMode, 'is-moon': !darkMode }"
                 width="32" height="32" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <!-- Sun Core: vibrant warm gold to fiery amber -->
                <radialGradient id="celestialSunGrad" cx="35%" cy="35%" r="65%">
                  <stop offset="0%" stop-color="#FFF59D" />
                  <stop offset="40%" stop-color="#FFB300" />
                  <stop offset="85%" stop-color="#FF8F00" />
                  <stop offset="100%" stop-color="#E65100" />
                </radialGradient>

                <!-- Sun Rays Gradient -->
                <linearGradient id="celestialRayGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FFE082" />
                  <stop offset="50%" stop-color="#FFA000" />
                  <stop offset="100%" stop-color="#FF6F00" />
                </linearGradient>

                <!-- Moon Core: rich luminous pearl to deep slate/indigo for high contrast against white mode -->
                <radialGradient id="celestialMoonGrad" cx="25%" cy="30%" r="75%">
                  <stop offset="0%" stop-color="#FFFDE7" />
                  <stop offset="20%" stop-color="#F8FAFC" />
                  <stop offset="50%" stop-color="#94A3B8" />
                  <stop offset="80%" stop-color="#475569" />
                  <stop offset="100%" stop-color="#334155" />
                </radialGradient>

                <!-- Eclipse Shadow Mask: slides across the central sphere -->
                <mask id="celestialEclipseMask" maskUnits="userSpaceOnUse">
                  <rect x="0" y="0" width="36" height="36" fill="white" />
                  <circle class="celestial-eclipse-disk"
                          :style="darkMode ? 'transform: translate(25px, -25px);' : 'transform: translate(0px, 0px);'"
                          cx="24.5" cy="11.5" r="7.8" fill="black" />
                </mask>
              </defs>

              <!-- Solar Rays (Full Sun: rotates & expands in dark mode, contracts & fades in light mode) -->
              <g class="celestial-rays"
                 :style="darkMode ? 'opacity: 1; transform: scale(1) rotate(0deg);' : 'opacity: 0; transform: scale(0.2) rotate(135deg);'"
                 stroke="url(#celestialRayGrad)" stroke-width="2.4" stroke-linecap="round">
                <line x1="18" y1="2" x2="18" y2="5.5" />
                <line x1="18" y1="30.5" x2="18" y2="34" />
                <line x1="2" y1="18" x2="5.5" y2="18" />
                <line x1="30.5" y1="18" x2="34" y2="18" />
                <line x1="6.7" y1="6.7" x2="9.2" y2="9.2" />
                <line x1="26.8" y1="26.8" x2="29.3" y2="29.3" />
                <line x1="6.7" y1="29.3" x2="9.2" y2="26.8" />
                <line x1="26.8" y1="9.2" x2="29.3" y2="6.7" />
              </g>

              <!-- Main Celestial Sphere with Eclipse Mask -->
              <g mask="url(#celestialEclipseMask)">
                <!-- Sun Body (shown in Dark mode) -->
                <circle class="celestial-sun-body"
                        :style="darkMode ? 'opacity: 1;' : 'opacity: 0;'"
                        cx="18" cy="18" r="8.5" fill="url(#celestialSunGrad)" />

                <!-- Moon Body (shown in Light mode: deep defining stroke and rich lunar gradient) -->
                <circle class="celestial-moon-body"
                        :style="darkMode ? 'opacity: 0;' : 'opacity: 1;'"
                        cx="18" cy="18" r="8.5" fill="url(#celestialMoonGrad)"
                        stroke="#334155" stroke-width="0.8" />

                <!-- Lunar Surface Details / Craters (prominent contrast in Light mode) -->
                <g class="celestial-craters"
                   :style="darkMode ? 'opacity: 0; transform: scale(0.5);' : 'opacity: 1; transform: scale(1);'">
                  <circle cx="13.5" cy="18" r="1.4" fill="#1E293B" opacity="0.5" />
                  <circle cx="16.5" cy="22.5" r="1.1" fill="#1E293B" opacity="0.45" />
                  <circle cx="15.2" cy="13.8" r="0.9" fill="#1E293B" opacity="0.4" />
                </g>
              </g>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .header-theme-mobile {
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .header-theme-desktop {
      display: none;
    }

    @media (min-width: 1024px) {
      .header-theme-mobile {
        display: none !important;
      }
      .header-theme-desktop {
        display: inline-flex !important;
        align-items: center;
      }
    }

    .celestial-toggle-btn {
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      padding: 4px;
      margin: 0;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      outline: none;
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      -webkit-tap-highlight-color: transparent;
    }

    .celestial-toggle-btn:hover {
      transform: scale(1.18) rotate(10deg);
    }

    .celestial-toggle-btn:active {
      transform: scale(0.92);
    }

    .celestial-svg {
      overflow: visible;
      transition: filter 0.65s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .celestial-svg.is-sun {
      filter: drop-shadow(0 0 6px rgba(255, 179, 0, 0.8)) drop-shadow(0 0 14px rgba(255, 111, 0, 0.4));
    }

    .celestial-svg.is-moon {
      filter: drop-shadow(0 3px 6px rgba(15, 23, 42, 0.45)) drop-shadow(0 1px 2px rgba(15, 23, 42, 0.3)) drop-shadow(0 0 1px rgba(15, 23, 42, 0.6));
    }

    .celestial-toggle-btn:hover .celestial-svg.is-moon {
      filter: drop-shadow(0 4px 10px rgba(15, 23, 42, 0.6)) drop-shadow(0 1px 3px rgba(15, 23, 42, 0.4)) drop-shadow(0 0 2px rgba(15, 23, 42, 0.8));
    }

    .celestial-eclipse-disk {
      transform-origin: 18px 18px;
      transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .celestial-rays {
      transform-origin: 18px 18px;
      transition: transform 0.65s cubic-bezier(0.34, 1.25, 0.64, 1), opacity 0.45s ease;
    }

    .celestial-sun-body,
    .celestial-moon-body {
      transition: opacity 0.5s ease;
    }

    .celestial-craters {
      transform-origin: 18px 18px;
      transition: opacity 0.45s ease, transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
  </style>
</header>