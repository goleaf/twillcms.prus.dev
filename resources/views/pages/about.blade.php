@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">About Our News Portal</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                Your trusted source for the latest news, insights, and stories that matter most.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Our Mission</h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                We are dedicated to delivering accurate, timely, and comprehensive news coverage that keeps you informed about the world around you. Our commitment to journalistic integrity and unbiased reporting ensures that you receive the facts you need to make informed decisions.
            </p>

            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">What We Cover</h2>
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Technology</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Latest developments in tech, innovation, and digital transformation.
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Business</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Market trends, economic insights, and corporate developments.
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Science</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Discoveries, research breakthroughs, and scientific advancements.
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Health</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Health news, medical breakthroughs, and wellness insights.
                    </p>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Our Values</h2>
            <div class="space-y-6 mb-12">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Accuracy</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            We verify our sources and fact-check our content to ensure accuracy and reliability.
                        </p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Transparency</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            We maintain transparency in our reporting and clearly identify our sources.
                        </p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Timeliness</h3>
                        <p class="text-gray-600 dark:text-gray-300">
                            We deliver news as it happens, keeping you up-to-date with the latest developments.
                        </p>
                    </div>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Contact Us</h2>
            <div class="bg-gray-50 dark:bg-gray-800 p-8 rounded-lg">
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Have a story tip, feedback, or questions? We'd love to hear from you.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">contact@newsportal.com</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">123 News Street, Media City, MC 12345</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">+1 (555) 123-4567</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 