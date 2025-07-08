@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded z-50">Skip to main content</a>
<div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Privacy Policy</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Last updated: {{ date('F j, Y') }}
            </p>
            
            <div class="text-gray-700 dark:text-gray-300 space-y-6">
                <p>This Privacy Policy describes how we collect, use, and protect your information when you visit our website.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">Information We Collect</h2>
                <p>We collect information you provide directly to us, such as when you subscribe to our newsletter or contact us.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">How We Use Your Information</h2>
                <p>We use the information we collect to provide, maintain, and improve our services.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us.</p>
            </div>
        </div>
    </div>
</div>
@endsection 