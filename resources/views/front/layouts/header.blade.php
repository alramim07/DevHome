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
        <div class="mh ii oe cf yh xi rf gg">
          <div class="ob j ul v _" :class="navigationOpen ? '!-ud-visible' : 'f'">
            <label class="qb ka k">
              <input type="checkbox" :value="darkMode" @change="darkMode = !darkMode" class="td oc ec ei j n ga ka" />
              <!-- Icon Sun -->
              <svg class="jf fj" width="25" height="25" viewBox="0 0 25 25" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M12.0908 18.6363C10.3549 18.6363 8.69 17.9467 7.46249 16.7192C6.23497 15.4916 5.54537 13.8268 5.54537 12.0908C5.54537 10.3549 6.23497 8.69 7.46249 7.46249C8.69 6.23497 10.3549 5.54537 12.0908 5.54537C13.8268 5.54537 15.4916 6.23497 16.7192 7.46249C17.9467 8.69 18.6363 10.3549 18.6363 12.0908C18.6363 13.8268 17.9467 15.4916 16.7192 16.7192C15.4916 17.9467 13.8268 18.6363 12.0908 18.6363ZM12.0908 16.4545C13.2481 16.4545 14.358 15.9947 15.1764 15.1764C15.9947 14.358 16.4545 13.2481 16.4545 12.0908C16.4545 10.9335 15.9947 9.8236 15.1764 9.00526C14.358 8.18692 13.2481 7.72718 12.0908 7.72718C10.9335 7.72718 9.8236 8.18692 9.00526 9.00526C8.18692 9.8236 7.72718 10.9335 7.72718 12.0908C7.72718 13.2481 8.18692 14.358 9.00526 15.1764C9.8236 15.9947 10.9335 16.4545 12.0908 16.4545ZM10.9999 0.0908203H13.1817V3.36355H10.9999V0.0908203ZM10.9999 20.8181H13.1817V24.0908H10.9999V20.8181ZM2.83446 4.377L4.377 2.83446L6.69082 5.14828L5.14828 6.69082L2.83446 4.37809V4.377ZM17.4908 19.0334L19.0334 17.4908L21.3472 19.8046L19.8046 21.3472L17.4908 19.0334ZM19.8046 2.83337L21.3472 4.377L19.0334 6.69082L17.4908 5.14828L19.8046 2.83446V2.83337ZM5.14828 17.4908L6.69082 19.0334L4.377 21.3472L2.83446 19.8046L5.14828 17.4908ZM24.0908 10.9999V13.1817H20.8181V10.9999H24.0908ZM3.36355 10.9999V13.1817H0.0908203V10.9999H3.36355Z"
                  fill="" />
              </svg>
              <!-- Icon Sun -->
              <!-- Icon Moon -->
              <svg class="vb ej" width="21" height="21" viewBox="0 0 21 21" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_512_11103)">
                  <path
                    d="M8.83697 5.88205C8.8368 7.05058 9.18468 8.19267 9.83625 9.16268C10.4878 10.1327 11.4135 10.8866 12.4953 11.3284C13.5772 11.7701 14.766 11.8796 15.9103 11.6429C17.0546 11.4062 18.1025 10.8341 18.9203 9.99941V10.0834C18.9203 14.7243 15.1584 18.4862 10.5175 18.4862C5.87667 18.4862 2.11475 14.7243 2.11475 10.0834C2.11475 5.44259 5.87667 1.68066 10.5175 1.68066H10.6016C10.042 2.22779 9.59754 2.88139 9.29448 3.60295C8.99143 4.32451 8.83587 5.09943 8.83697 5.88205ZM3.7953 10.0834C3.79469 11.5833 4.29571 13.0403 5.21864 14.2226C6.14157 15.4049 7.4334 16.2446 8.88857 16.608C10.3437 16.9715 11.8787 16.8379 13.2491 16.2284C14.6196 15.6189 15.7469 14.5686 16.4516 13.2446C15.1974 13.54 13.8885 13.5102 12.6492 13.1578C11.4098 12.8054 10.281 12.1422 9.36988 11.2311C8.45877 10.32 7.79557 9.19119 7.44318 7.95181C7.0908 6.71243 7.06093 5.40357 7.3564 4.1494C6.28049 4.72259 5.38073 5.57759 4.75343 6.62288C4.12614 7.66817 3.79495 8.86438 3.7953 10.0834Z"
                    fill="currentColor" />
                </g>
                <defs>
                  <clipPath id="clip0_512_11103">
                    <rect width="20.1667" height="20.1667" fill="white" transform="translate(0.434204)" />
                  </clipPath>
                </defs>
              </svg>
              <!-- Icon Moon -->
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>