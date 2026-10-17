<section id="about" class="ke eg mm mn">
  <style>
    .about-img-wrap {
      opacity: 0;
      transition: opacity .8s ease, transform .8s ease;
    }

    .about-img-wrap.from-top {
      transform: translateY(-60px);
    }

    .about-img-wrap.from-bottom {
      transform: translateY(60px);
    }

    .about-img-wrap.is-revealed {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
  <div class="a">
    <div class="-ud-mx-4 sb fe wd xd zd">
      <div class="oc tf cm/12">
        <div class="k la sb wb fd">
          <div class="j l da ra gd ne about-img-wrap from-top" x-data="{ shown: false }"
            x-intersect.once="shown = true" :class="shown ? 'is-revealed' : ''">
            <img src="{{ asset('storage/' . $settings->about_us_img1) }}" alt="about image"
              class="oc ne resize" loading="lazy" decoding="async" />
            <span class="j p -ud-right-14 -ud-z-10">
              <svg width="61" height="47" viewBox="0 0 61 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="15.6667" cy="30.9998" r="1.66667" transform="rotate(-90 15.6667 30.9998)" fill="#3056D3" />
                <circle cx="44.6667" cy="30.9998" r="1.66667" transform="rotate(-90 44.6667 30.9998)" fill="#3056D3" />
                <circle cx="1.66667" cy="30.9998" r="1.66667" transform="rotate(-90 1.66667 30.9998)" fill="#3056D3" />
                <circle cx="29.9999" cy="30.9998" r="1.66667" transform="rotate(-90 29.9999 30.9998)" fill="#3056D3" />
                <circle cx="58.9999" cy="30.9998" r="1.66667" transform="rotate(-90 58.9999 30.9998)" fill="#3056D3" />
                <circle cx="15.6667" cy="16.3333" r="1.66667" transform="rotate(-90 15.6667 16.3333)" fill="#3056D3" />
                <circle cx="44.6667" cy="16.3333" r="1.66667" transform="rotate(-90 44.6667 16.3333)" fill="#3056D3" />
                <circle cx="1.66667" cy="16.3333" r="1.66667" transform="rotate(-90 1.66667 16.3333)" fill="#3056D3" />
                <circle cx="29.9999" cy="16.3333" r="1.66667" transform="rotate(-90 29.9999 16.3333)" fill="#3056D3" />
                <circle cx="58.9999" cy="16.3333" r="1.66667" transform="rotate(-90 58.9999 16.3333)" fill="#3056D3" />
                <circle cx="15.6667" cy="45.3336" r="1.66667" transform="rotate(-90 15.6667 45.3336)" fill="#3056D3" />
                <circle cx="15.6667" cy="1.66683" r="1.66667" transform="rotate(-90 15.6667 1.66683)" fill="#3056D3" />
                <circle cx="44.6667" cy="45.3336" r="1.66667" transform="rotate(-90 44.6667 45.3336)" fill="#3056D3" />
                <circle cx="44.6667" cy="1.66683" r="1.66667" transform="rotate(-90 44.6667 1.66683)" fill="#3056D3" />
                <circle cx="1.66667" cy="45.3336" r="1.66667" transform="rotate(-90 1.66667 45.3336)" fill="#3056D3" />
                <circle cx="1.66667" cy="1.66683" r="1.66667" transform="rotate(-90 1.66667 1.66683)" fill="#3056D3" />
                <circle cx="29.9999" cy="45.3336" r="1.66667" transform="rotate(-90 29.9999 45.3336)" fill="#3056D3" />
                <circle cx="29.9999" cy="1.66683" r="1.66667" transform="rotate(-90 29.9999 1.66683)" fill="#3056D3" />
                <circle cx="58.9999" cy="45.3336" r="1.66667" transform="rotate(-90 58.9999 45.3336)" fill="#3056D3" />
                <circle cx="58.9999" cy="1.66683" r="1.66667" transform="rotate(-90 58.9999 1.66683)" fill="#3056D3" />
              </svg>
            </span>
            <span class="j q -ud-bottom-10 -ud-z-1 qj">
              <svg width="112" height="112" viewBox="0 0 112 112" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M56 96C78.0914 96 96 78.0914 96 56C96 33.9086 78.0914 16 56 16C33.9086 16 16 33.9086 16 56C16 78.0914 33.9086 96 56 96ZM56 112C86.9279 112 112 86.9279 112 56C112 25.0721 86.9279 0 56 0C25.0721 0 0 25.0721 0 56C0 86.9279 25.0721 112 56 112Z"
                  fill="#13C296" />
              </svg>
            </span>
          </div>
          <div class="j o r da sa hd ne about-img-wrap from-bottom" x-data="{ shown: false }"
            x-intersect.once="shown = true" :class="shown ? 'is-revealed' : ''">
            <img src="{{ asset('storage/' . $settings->about_us_img2) }}"
              alt="about image" class="oc ne resize" loading="lazy" decoding="async" />
          </div>
        </div>
      </div>
      <div class="animate_right oc tf dm/2 2xl:ud-w-5/12">
        <div class="rj vl">
          <span class="yh ta qb ah nh">
            WHAT WE BUILD
          </span>
          <h2 class="ua bh lh sh zh mj lk vk ll zm cn vn">
            {{ $settings->what_we_build ?? 'We grow your business with full potential by the innovation.' }}
          </h2>
          <h3 class="va ah lh zh mj">Why Dev Home</h3>
          <p class="wa">
            {{ $settings->why_dev_home ?? 'Proin gravida nibh vel velit auctor aliquet. aks Aenean
            sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum,
            nec sagittis sem, tidiomic consequat ipsum.'}}
          </p>
          <h3 class="va ah lh zh mj">How We Work</h3>
          <p>
            {{ $settings->how_we_work ?? 'Proin gravida nibh vel velit auctor aliquet. aks Aenean
            sollicitudin, lorem quis bibendum auctor.'}}
          </p>
        </div>
      </div>
    </div>
  </div>
</section>