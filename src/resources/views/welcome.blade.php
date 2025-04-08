<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Faily</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: rgb(224, 223, 220);">
<!-- Navbar poza aplikacją Vue -->
@include('includes.navbar')

<!-- Aplikacja Vue -->
<div id="app">
    <main>
        <div class="map-fullwidth">
            <leaflet-map :center="[51.2101, 16.1619]" :zoom="7"></leaflet-map>
        </div>
    </main>
</div>

@include('includes.footer')
</body>
</html>
