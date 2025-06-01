<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.addEvent') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main text-color">
<script>
    window.locale = "{{ app()->getLocale() }}";
</script>
@include('includes.navbar')
<div class="page-container" id="app">
    <main class="py-4">
        <div class="container">
            <event-form
                csrf-token="{{ csrf_token() }}"
                store-route="{{ route('events.store') }}"
            ></event-form>
        </div>
    </main>
    @include('includes.footer')
</div>
</body>
</html>
