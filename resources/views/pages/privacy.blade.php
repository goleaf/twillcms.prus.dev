@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
<main id="main-content" tabindex="-1" class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-50 dark:bg-gray-800 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Privacy Policy') }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                {{ __('Last updated: :date', ['date' => date('F j, Y')]) }}
            </p>
        </div>
    </div>

    <!-- Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-lg max-w-none dark:prose-invert prose-indigo">
        <div>
            <h2>Introduction</h2>
            <p>
                This Privacy Policy describes how our news portal ("we," "our," or "us") collects, uses, and protects your information when you visit our website and use our services.
            </p>

            <h2>Information We Collect</h2>
            <h3>Information You Provide</h3>
            <ul>
                <li><strong>Contact Information:</strong> When you contact us through our contact form, we collect your name, email address, and message content.</li>
                <li><strong>Newsletter Subscription:</strong> If you subscribe to our newsletter, we collect your email address.</li>
                <li><strong>Comments and Feedback:</strong> Any comments or feedback you provide on our articles or through our contact forms.</li>
            </ul>

            <h3>Information We Collect Automatically</h3>
            <ul>
                <li><strong>Usage Data:</strong> Information about how you interact with our website, including pages visited, time spent, and navigation patterns.</li>
                <li><strong>Device Information:</strong> Information about your device, including IP address, browser type, operating system, and screen resolution.</li>
                <li><strong>Cookies:</strong> We use cookies and similar technologies to enhance your browsing experience and analyze website traffic.</li>
            </ul>

            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide and improve our news services</li>
                <li>Respond to your inquiries and provide customer support</li>
                <li>Send you newsletters and updates (if you've subscribed)</li>
                <li>Analyze website usage and improve user experience</li>
                <li>Ensure website security and prevent fraud</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2>Information Sharing</h2>
            <p>We do not sell, trade, or otherwise transfer your personal information to third parties, except in the following circumstances:</p>
            <ul>
                <li><strong>Service Providers:</strong> We may share information with trusted service providers who assist us in operating our website and providing services.</li>
                <li><strong>Legal Requirements:</strong> We may disclose information when required by law or to protect our rights and safety.</li>
                <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the transaction.</li>
            </ul>

            <h2>Data Security</h2>
            <p>
                We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet or electronic storage is 100% secure.
            </p>

            <h2>Cookies and Tracking</h2>
            <p>
                Our website uses cookies to enhance your browsing experience. You can control cookie settings through your browser preferences. Disabling cookies may affect some website functionality.
            </p>

            <h3>Types of Cookies We Use</h3>
            <ul>
                <li><strong>Essential Cookies:</strong> Required for website functionality</li>
                <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
                <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
            </ul>

            <h2>Your Rights</h2>
            <p>Depending on your location, you may have the following rights regarding your personal information:</p>
            <ul>
                <li><strong>Access:</strong> Request access to your personal information</li>
                <li><strong>Correction:</strong> Request correction of inaccurate information</li>
                <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                <li><strong>Portability:</strong> Request a copy of your information in a structured format</li>
                <li><strong>Objection:</strong> Object to certain uses of your information</li>
                <li><strong>Restriction:</strong> Request restriction of processing in certain circumstances</li>
            </ul>

            <h2>Children's Privacy</h2>
            <p>
                Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you believe we have collected information from a child under 13, please contact us immediately.
            </p>

            <h2>Third-Party Links</h2>
            <p>
                Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these external sites. We encourage you to review their privacy policies before providing any personal information.
            </p>

            <h2>International Data Transfers</h2>
            <p>
                Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your information during such transfers.
            </p>

            <h2>Data Retention</h2>
            <p>
                We retain your personal information only for as long as necessary to fulfill the purposes outlined in this privacy policy, unless a longer retention period is required by law.
            </p>

            <h2>Changes to This Privacy Policy</h2>
            <p>
                We may update this privacy policy from time to time. We will notify you of any changes by posting the new privacy policy on this page and updating the "Last updated" date.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have any questions about this privacy policy or our privacy practices, please contact us:
            </p>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg mt-6">
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li><strong>Email:</strong> privacy@newsportal.com</li>
                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                    <li><strong>Address:</strong> 123 News Street, Media City, MC 12345</li>
                </ul>
            </div>
        </div>
    </section>
</main>
@endsection 