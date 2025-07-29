<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8" />
    <!-- Mínimo requerido -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO básico para login -->
    <title>Login | Temperature & Humidity Monitoring</title>
    <meta name="description" content="Login to access the Inteva IoT dashboard for real-time monitoring.">
    <meta name="author" content="Samuel Pinzón">

    <!-- Ícono del sitio -->
    <link rel="icon" href="{{ asset('images/inteva_logos/inteva_products_simple_logo.png') }}">

    @vite(['resources/css/app.css', 'resources/css/login.css', 'resources/js/app.js', 'resources/js/login.js'])
</head>
<body>

    <div class="logo-container">
        {{-- Aquí podrías poner tu logo como imagen si quieres --}}
        <img class="logo-img" src="{{ asset('images/inteva_logos/inteva_products_simple_logo.png') }}" alt="inteva_logo.png" />
    </div>

    @if ($errors->any())
        <div class="error-card" id="error-card">
                @foreach ($errors->all() as $error)
                    <p class="error-log">{{ $error }}</p>
                @endforeach
            <button class="error-button" id="hidden-error-card">✖</button>
        </div>
    @endif

    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form__group">
            <label class="form__label" for="email">Email address</label>
            <input class="form__input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
        </div>

        <div class="form__group">
            <div class="form__row">
                <label class="form__label" for="password">Password</label>
            </div>
            <input class="form__input" id="password" type="password" name="password" required autocomplete="current-password" />
        </div>

        <div class="form__group">
            <button class="form__submit-button" type="submit">Log in</button>
        </div>
    </form>

</body>
</html>
