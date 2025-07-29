<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Temperature & Humidity Monitoring Web App | Inteva')</title>
        <meta name="description" content="Web application for real-time temperature and humidity monitoring in industrial environments using ESP32.">
        <meta name="keywords" content="ESP32, IoT, temperature monitoring, humidity monitoring, sensor, dashboard">
        <meta name="author" content="Samuel Pinzón">

        <meta property="og:title" content="Temperature & Humidity Monitoring Web App | Inteva Products">
        <meta property="og:description" content="Monitor temperature and humidity in real-time with ESP32. Internal web app for industrial environmental control.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('images/preview.png') }}">

        <link rel="icon" href="{{ asset('images/inteva_logos/inteva_products_simple_logo.png') }}">

        <script src="{{ asset('js/libs/charts/chart.js') }}"></script>
        <script src="{{ asset('js/libs/mqtt.min.js') }}"></script>

        @vite(['resources/js/app.js', 'resources/js/layout.js'])
        @vite(['resources/css/app.css', 'resources/css/layout.css'])
        @php
            $routeName = request()->route()->getName() ?? '';

            $cssByRoute = [
                'dashboard' => 'resources/css/dashboard.css',
                'queries'   => 'resources/css/queries.css',
                'live'      => 'resources/css/live.css',
                'settings'  => 'resources/css/settings.css',
            ];

            $jsByRoute = [
                'dashboard' => 'resources/js/dashboard.js',
                'queries'   => 'resources/js/queries.js',
                'live'      => 'resources/js/live.js',
                'settings'  => 'resources/js/settings.js',
            ];
        @endphp

        @if(isset($cssByRoute[$routeName]))
            @vite($cssByRoute[$routeName])
        @endif

        @if(isset($jsByRoute[$routeName]))
            @vite($jsByRoute[$routeName])
        @endif

        @stack('styles')
        @stack('scripts')

    </head>

    <body>

        <div class="main-wrapper" role="document">

            <aside class="sidebar" role="navigation" aria-label="Sidebar navigation">
                <div class="sidebar__head">
                    <img class="sidebar__logo-img" src="{{ asset('images/inteva_logos/inteva_products_simple_logo.png') }}" alt="Inteva Products logo">
                    <label class="sidebar__toggle-label">
                        <input class="sidebar__checkbox" type="checkbox" id="show-sidebar" aria-label="Toggle sidebar">
                            <i data-lucide="List"></i>
                    </label>
                </div>

                <nav class="sidebar__nav" aria-label="Main menu">
                    <ul class="sidebar__list">
                        <li class="sidebar__item">
                            <a class="sidebar__a" href="{{ route('dashboard') }}" aria-label="Dashboard">
                                <i data-lucide="Layout-Panel-Left" aria-hidden="true"></i>
                                Board
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__a" href="{{ route('queries') }}" aria-label="Queries">
                                <i data-lucide="Database" aria-hidden="true"></i>
                                Queries
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__a" href="{{ route('live') }}" aria-label="Live monitoring">
                                <i data-lucide="Radio" aria-hidden="true"></i>
                                Live
                            </a>
                        </li>
                        <li class="sidebar__item">
                            <a class="sidebar__a" href="{{ route('settings') }}" aria-label="Settings">
                                <i data-lucide="Settings" aria-hidden="true"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <div class="main-content" role="main">

                <header class="header" role="banner" aria-label="Main header">
                    <div class="header__content">
                        <div class="header__main-info">
                            <div class="header__circle" role="presentation" aria-hidden="true"></div>
                            <span class="header__separator" aria-hidden="true">|</span>
                            <h1 class="header__title" id="page-title">@yield('title', 'site data')</h1>
                            <span class="header__separator" aria-hidden="true">|</span>
                            <time class="header__date" datetime="{{ now()->format('Y-m-d') }}">{{ now()->format('F j, Y \a\t H') }}</time>
                            <span class="header__separator" aria-hidden="true">|</span>
                            <a href="{{ route('get-data') }}" class="header__get-data-button">
                                <span class="button-content">Download data </span>
                            </a>
                        </div>
                        <div class="header__aside-info">
                            <div class="header__device-info">
                                <p class="header__device" aria-label="Device identifier">IMO #1</p>
                            </div>
                            <span class="header__separator" aria-hidden="true">|</span>
                            <div class="header__user-setting">
                                <img class="header__user-img" src="{{ asset('images/inteva_logos/user_img.png') }}" alt="User profile image">
                                <a class="header__username" id="user-button">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</a>
                                <form class="header__logout-form" method="POST" action="{{ route('logout') }}" id="user-logout-form" aria-label="Logout form">
                                    @csrf
                                    <button id="user_logout" class="header__logout-button" type="submit">Log out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="main" role="main" aria-labelledby="page-title">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>

                <footer class="footer" role="contentinfo" aria-label="Application footer">
                    <section class="footer__section" aria-labelledby="footer-about">
                        <h3 id="footer-about" class="footer__title">Environmental monitoring system</h3>
                        <p class="footer__paragraph">
                            Web application for real-time temperature and humidity visualization.
                        </p>
                    </section>

                    <section class="footer__section" aria-labelledby="footer-status">
                        <h3 id="footer-status" class="footer__title">Service status</h3>
                        <p class="footer__paragraph">
                            The application receives data via <strong class="footer__strong">HTTP POST requests</strong> from the ESP32.
                        </p>
                        <p class="footer__paragraph">
                            It also supports real-time communication through <strong class="footer__strong">MQTT</strong>.
                        </p>
                    </section>

                    <section class="footer__section" aria-labelledby="footer-update">
                        <h3 id="footer-update" class="footer__title">Take note</h3>
                        <p class="footer__paragraph">
                            If the application fails to load on first attempt or shows a timeout error, please try again — it should load correctly after retrying.
                        </p>
                    </section>

                    <section class="footer__section" aria-labelledby="footer-thresholds">
                        <h3 id="footer-thresholds" class="footer__title">ESP32 Settings</h3>
                        <p class="footer__paragraph">
                            <em>Note:</em> Configuration updates may be saved in the database, but not yet received by the ESP32. 
                            Always ensure the ESP32's current settings match the application’s configuration shown in the <strong>Settings</strong> tab.
                        </p>
                    </section>


                    <section class="footer__section" aria-labelledby="footer-tech">
                        <h3 id="footer-tech" class="footer__title">Technical info</h3>
                        <p class="footer__paragraph">HTML, CSS, JavaScript, Laravel with PHP, Livewire and MariaDB.</p>
                        <a class="footer__a">GitHub repository.</a>
                    </section>

                    <section class="footer__section" aria-labelledby="footer-location">
                        <h3 id="footer-location" class="footer__title">Quote</h3>
                        <p class="footer__paragraph">
                            <q>If you can imagine it, you can program it.</q> — 
                            <cite>Alejandro Miguel Taboada Sanchez, software developer and tech content creator.</cite>
                        </p>
                    </section>



                    <div class="footer__img-container">
                        <img class="footer__main-logo" src="{{ asset('images/inteva_logos/inteva_products_dark_logo.png') }}" alt="Inteva Products logo">
                    </div>

                    <div class="footer__section" aria-labelledby="footer-quote">
                        <p class="footer__paragraph footer__quote" id="footer-quote">“Efficient environmental control for safe operations.”</p>
                        <p class="footer__copyright">
                            <small>&copy; {{ date('Y') }} Inteva Products. All rights reserved.</small>
                        </p>
                    </div>

                </footer>

            </div>
        </div>
        @livewireScripts
    </body>

</html>