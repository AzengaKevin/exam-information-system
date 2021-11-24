<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @hasSection ('title')
    <title>@yield('title') - {{ config('app.name') }}</title>
    @else
    <title>{{ config('app.name') }}</title>
    @endif
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')
    <livewire:styles />
</head>

<body class="antialiased">

    @yield('body')

    @stack('modals')
    <script src="{{ mix('js/app.js') }}"></script>
    <livewire:scripts />
    @stack('scripts')
</body>

</html>