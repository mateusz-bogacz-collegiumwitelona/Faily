@php
    use Illuminate\Support\Facades\Cache;

    $locale = app()->getLocale();
    $userId = auth()->id() ?? 'guest';
    $cacheKey = "navbar_{$locale}_{$userId}";
    $cacheTTL = 60 * 60;
@endphp

@if (Cache::has($cacheKey) && !request()->has('refresh_cache'))
    {!! Cache::get($cacheKey) !!}
@else
    @php
        ob_start();
    @endphp

    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid nav-wrapper px-2">

                <a class="navbar-brand text-color fs-4 me-1" href="{{ url('/') }}" style="text-decoration: none;">
                    <img src="{{ asset('images/includes/logo.png') }}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top fw-bold">
                    <span>Faily</span>
                </a>

                <div class="d-flex align-items-center order-lg-last">
                    <!-- Przycisk języka - zawsze widoczny -->
                    <div class="dropdown mx-1">
                        <a class="btn btn-gradient-nav btn-sm py-1 px-2" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            @if (app()->getLocale() == 'pl')
                                <span><img src="{{ asset('images/includes/pl.png') }}" alt="Poland" width="20" height="15"></span>
                            @elseif (app()->getLocale() == 'en')
                                <span><img src="{{ asset('images/includes/gb-eng.png') }}" alt="England" width="20" height="15"></span>
                            @elseif (app()->getLocale() == 'es')
                                <span><img src="{{ asset('images/includes/es.png') }}" alt="Spain" width="20" height="15"></span>
                            @elseif (app()->getLocale() == 'jpn')
                                <span><img src="{{ asset('images/includes/jp.png') }}" alt="Japan" width="20" height="15"></span>
                            @elseif (app()->getLocale() == 'ua')
                                <span><img src="{{ asset('images/includes/ua.png') }}" alt="Ukraine" width="20" height="15"></span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-color {{ app()->getLocale() == 'pl' ? 'active' : '' }}" href="{{ route('language.change', 'pl') }}"><img src="{{ asset('images/includes/pl.png') }}" alt="Poland" width="20" height="15">Polski</a></li>
                            <li><a class="dropdown-item text-color {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.change', 'en') }}"><img src="{{ asset('images/includes/gb-eng.png') }}" alt="England" width="20" height="15">English</a></li>
                            <li><a class="dropdown-item text-color {{ app()->getLocale() == 'es' ? 'active' : '' }}" href="{{ route('language.change', 'es') }}"><img src="{{ asset('images/includes/es.png') }}" alt="Spain" width="20" height="15">Español</a></li>
                            <li><a class="dropdown-item text-color {{ app()->getLocale() == 'jpn' ? 'active' : '' }}" href="{{ route('language.change', 'jpn') }}"><img src="{{ asset('images/includes/jp.png') }}" alt="Japan" width="20" height="15">日本語</a></li>
                            <li><a class="dropdown-item text-color {{ app()->getLocale() == 'ua' ? 'active' : '' }}" href="{{ route('language.change', 'ua') }}"><img src="{{ asset('images/includes/ua.png') }}" alt="Ukraine" width="20" height="15"> Українська</a></li>
                        </ul>
                    </div>

                    @auth
                        <!-- Przycisk konta dla zalogowanych - zawsze widoczny -->
                        <div class="dropdown mx-1">
                            <a class="btn btn-gradient-nav btn-sm py-1 px-2" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('messages.navbar.account') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item text-color" href="{{ url('/account') }}">{{ __('messages.navbar.goToAccount') }}</a></li>
                                <li><a class="dropdown-item text-color" href="{{ route('my-applications') }}">{{ __('messages.navbar.myApplications') }}</a></li>
                                <li><a class="dropdown-item text-color" href="{{ url('/my_events') }}">{{ __('messages.navbar.myEvents') }}</a></li>
                                <li><a class="dropdown-item text-color" href="{{ url('/help') }}">{{ __('messages.navbar.help') }}</a></li>
                                <li><a class="dropdown-item text-color" href="{{ url('/profile/dashboard') }}">{{ __('messages.navbar.settings') }}</a></li>
                                @if(Auth::user()->role === 'admin')
                                    <li class="nav-item">
                                        <a class="dropdown-item text-color" href="{{ route('admin.dashboard') }}">{{ __('messages.title.adminDashboard') }}</a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider bg-white"></li>
                                <li>
                                    <!-- PLACEHOLDER dla formularza wylogowania - zostanie zastąpiony poza cache -->
                                    <div id="logout-form-placeholder"></div>
                                </li>
                            </ul>
                        </div>

                        <!-- Hamburger - tylko dla zalogowanych z białą ikonką -->
                        <button class="navbar-toggler ms-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon text-white" style="filter: brightness(0) invert(1);"></span>
                        </button>
                    @else
                        <!-- Przycisk logowania dla niezalogowanych - zawsze widoczny -->
                        <a href="{{ route('login') }}" class="btn btn-gradient-nav btn-sm py-1 px-2 mx-1">
                            {{ __('messages.navbar.signIn') }}
                        </a>
                    @endauth

                </div>

                <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarContent">
                    @auth
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item my-1 my-lg-0 mx-lg-1">
                                <a class="btn btn-gradient-nav btn-sm w-100" href="{{ url('/map') }}">{{ __('messages.navbar.eventMap') }}</a>
                            </li>
                            <li class="nav-item my-1 my-lg-0 mx-lg-1">
                                <a class="btn btn-gradient-nav btn-sm w-100" href="{{ url('/event_list') }}">{{ __('messages.navbar.events') }}</a>
                            </li>
                            <li class="nav-item my-1 my-lg-0 mx-lg-1">
                                <a class="btn btn-gradient-nav btn-sm w-100" href="{{ url('/about') }}">{{ __('messages.navbar.developers') }}</a>
                            </li>
                            <li class="nav-item my-1 my-lg-0 mx-lg-1">
                                <a class="btn btn-gradient-nav btn-sm w-100" href="{{ url('/add_event') }}">{{ __('messages.navbar.newEvent') }}</a>
                            </li>
                        </ul>
                    @endauth
                </div>
            </div>
        </nav>
    </header>
    @php
        $navbar = ob_get_clean();
        Cache::put($cacheKey, $navbar, $cacheTTL);
        echo $navbar;

        $navbarKeys = Cache::get('navbar_keys', []);
        if(!in_array($cacheKey, $navbarKeys)) {
            $navbarKeys[] = $cacheKey;
            Cache::put('navbar_keys', $navbarKeys, 60 * 60 * 24 * 30);
        }
    @endphp
@endif

@auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const placeholder = document.getElementById('logout-form-placeholder');
            if (placeholder) {
                placeholder.innerHTML = `
                <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="dropdown-item text-color" style="border: none; background: none; width: 100%; text-align: left;">
                        {{ __('messages.navbar.signOut') }}
                </button>
            </form>
`;
            }
        });
    </script>
@endauth
