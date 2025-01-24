<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FastDict</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dictionary.js'])
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="max-w-md mx-auto py-4 md:pt-16 px-4">
            <h1 class="text-2xl text-white mb-4">FastDict</h1>
            <input id="query" class="px-4 py-2 text-black mb-4 w-full" type="text" autofocus>
            <div id="results"></div>
        </div>
    </body>
</html>
