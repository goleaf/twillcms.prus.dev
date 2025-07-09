@extends('layouts.app')

@section('title', 'About Us')
@section('description', 'Learn more about our news portal and our mission to bring you the latest news and insights.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">About Our News Portal</h1>
        <p class="text-xl text-gray-600 dark:text-gray-300">Bringing you the latest news and insights from around the world</p>
    </div>

    <div class="prose prose-lg max-w-none dark:prose-invert">
        <p>Welcome to our news portal, your trusted source for comprehensive news coverage, in-depth analysis, and timely updates from around the globe.</p>
        
        <h2>Our Mission</h2>
        <p>We are committed to delivering accurate, unbiased, and timely news coverage across a wide range of topics including technology, business, health, science, sports, and entertainment.</p>
        
        <h2>What We Offer</h2>
        <ul>
            <li>Breaking news and live updates</li>
            <li>In-depth analysis and expert commentary</li>
            <li>Comprehensive coverage across multiple categories</li>
            <li>User-friendly interface with advanced search capabilities</li>
        </ul>
        
        <h2>Our Team</h2>
        <p>Our dedicated team of journalists and editors work around the clock to ensure you receive the most accurate and up-to-date information.</p>
        
        <h2>Contact Us</h2>
        <p>Have questions or feedback? We'd love to hear from you. Visit our <a href="{{ route('contact') }}" class="text-indigo-600 hover:text-indigo-500">contact page</a> to get in touch.</p>
    </div>
</div>
@endsection 