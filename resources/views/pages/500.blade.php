@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded z-50">Skip to main content</a>
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center" id="main-content" tabindex="-1">
        <div>
            <h1 class="text-6xl font-bold text-gray-300 dark:text-gray-600 mb-4">500</h1>
            <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Server Error</h2>
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">Sorry, something went wrong on our end. Please try again later or contact support if the problem persists.</p>
        </div>
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Go to Homepage
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ml-3">
                Contact Support
            </a>
        </div>
        <p class="text-sm text-gray-400 dark:text-gray-500">We have been notified and are working to fix the issue.</p>
    </div>
</div>
@endsection 