<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'News Portal') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 antialiased">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-900">
                                {{ config('app.name', 'News Portal') }}
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <form action="{{ route('home') }}" method="GET" class="flex">
                                <input type="text" 
                                       name="search" 
                                       placeholder="Search articles..."
                                       value="{{ request('search') }}"
                                       class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white mt-12">
            <div class="mx-auto max-w-7xl overflow-hidden px-6 py-8 sm:py-12 lg:px-8">
                <p class="mt-10 text-center text-xs leading-5 text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'News Portal') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html> 