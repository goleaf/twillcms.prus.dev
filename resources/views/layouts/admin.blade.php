<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <h1 class="text-xl font-semibold text-gray-900">
                            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
                        </h1>
                        <div class="hidden md:flex space-x-4">
                            <a href="/admin/articles" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Articles') }}</a>
                            <a href="/admin/tags" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Tags') }}</a>
                            <a href="/admin/statistics" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Statistics') }}</a>
                            <a href="/admin/analytics" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">{{ __('Analytics') }}</a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/" target="_blank" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">
                            View Site
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>
</html> 