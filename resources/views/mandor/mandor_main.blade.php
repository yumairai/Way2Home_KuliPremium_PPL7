<!DOCTYPE html>

<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Mandor Dashboard - Way2Home</title>
    <link href="{{ asset('css/mandor/mandor_main.css') }}" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/ui/dialog.css') }}">
    @stack('styles')
</head>

<body class="mandor-page">
    @include('partials.navbar_mandor')
    <main class="mandor-shell mandor-main">
        @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.w2h-dialog')
    @include('partials.w2h-flash')
    <script src="{{ asset('js/ui/dialog.js') }}"></script>
    @stack('scripts')
</body>

</html>
