<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="pump-auth font-sans antialiased">
        <div class="pump-auth__page">
            <main class="pump-auth__shell">
                <a class="pump-auth__brand" href="/" aria-label="The Pump Bar home">
                    <img src="{{ asset('images/the-pump-bar-logo.svg') }}" alt="The Pump Bar">
                </a>

                <section class="pump-auth__card">
                {{ $slot }}
                </section>

                <p class="pump-auth__footer">THE PUMP BAR <span></span> TEAM ACCESS</p>
            </main>
        </div>
    </body>
</html>
