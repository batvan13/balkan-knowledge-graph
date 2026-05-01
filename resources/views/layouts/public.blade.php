<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'BKG'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">
            <a href="/" class="text-sm font-semibold tracking-wide text-gray-700 hover:text-gray-900">
                {{ config('app.name', 'BKG') }}
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 mt-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6">
            <p class="text-xs text-gray-400">Balkan Knowledge Graph</p>
        </div>
    </footer>

</body>
</html>
