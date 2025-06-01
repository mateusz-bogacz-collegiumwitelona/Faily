{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('messages.title.faily') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero .carousel-item {
        height: 33vh;
        }

        .hero .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        }
    </style>
</head>
<body class="bg-dark text-color d-flex flex-column min-vh-100">
  @include('includes.navbar')

  <main class="flex-fill">
    {{-- HERO + CAROUSEL --}}
    <section class="hero position-relative overflow-hidden">
      <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          @for($i = 1; $i <= 3; $i++)
            <div class="carousel-item @if($i == 1) active @endif">
              <img src="{{ asset('images/welcome/slide'.$i.'.png') }}" class="d-block w-100" alt="Slide {{ $i }}">
              <div class="carousel-caption">
                <h1 class="fw-bold">
                  {{ __('messages.welcome.heroTitle') }}
                </h1>
                <p class="lead mb-4">{{ __('messages.welcome.heroSubtitle') }}</p>
              </div>
            </div>
          @endfor
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </section>

    <section class="py-5 bg-welcome">
            <div class="container text-center">
                <div class="row justify-content-center mb-4">
                    <div class="col-4 col-md-2">
                        <button class="btn btn-gradient w-100 py-3" onclick="showInfo('people')">
                            <div class="small mt-2">{{ __('messages.welcome.explore') }}</div>
                        </button>
                    </div>
                    <div class="col-4 col-md-2">
                        <button class="btn btn-gradient w-100 py-3" onclick="showInfo('calendar')">
                            <div class="small mt-2">{{ __('messages.welcome.events') }}</div>
                        </button>
                    </div>
                    <div class="col-4 col-md-2">
                        <button class="btn btn-gradient w-100 py-3" onclick="showInfo('car')">
                            <div class="small mt-2">{{ __('messages.welcome.rides') }}</div>
                        </button>
                    </div>
                </div>

                <div class="info-section mt-4">
                    <div id="people" class="info-text active">
                        <h4>{{ __('messages.welcome.meetPeople') }}</h4>
                        <p>{{ __('messages.welcome.meetPeopleDesc') }}</p>
                    </div>
                    <div id="calendar" class="info-text d-none">
                        <h4>{{ __('messages.welcome.createEvents') }}</h4>
                        <p>{{ __('messages.welcome.createEventsDesc') }}</p>
                    </div>
                    <div id="car" class="info-text d-none">
                        <h4>{{ __('messages.welcome.rides') }}</h4>
                        <p>{{ __('messages.welcome.needRide') }}</p>
                    </div>
                </div>
            </div>
    </section>



     {{-- GALLERY --}}
    <section class="py-5 text-center">
      <div class="container">
        <h2 class="mb-4">{{ __('messages.welcome.seeHowItWorks') }}</h2>
        <p class="mb-5">{{ __('messages.welcome.mainSubtitle') }}</p>
        <div class="row g-3">
          @for($i = 1; $i <= 6; $i++)
            <div class="col-6 col-md-4">
              <div class="ratio ratio-4x3 overflow-hidden rounded floating delay-{{ $i % 2 + 1 }}">
                <img src="{{ asset('images/welcome/gallery'.$i.'.png') }}"
                     class="img-fluid object-fit-cover" alt="Gallery {{ $i }}">
              </div>
            </div>
          @endfor
        </div>
      </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="py-5 bg-welcome text-center">
      <div class="container">
        <h2 class="mb-4">{{ __('messages.welcome.meetDevelopers') }}</h2>
        <a href="{{ url('/about') }}" class="btn-gradient btn-lg px-5">
          {{ __('messages.welcome.meetDevelopersButton') }}
        </a>
      </div>
    </section>
  </main>

  <script>
    function showInfo(id) {
      document.querySelectorAll('.info-text').forEach(el => el.classList.add('d-none'));
      document.getElementById(id).classList.remove('d-none');
    }
  </script>

  @include('includes.footer')
</body>
</html>
