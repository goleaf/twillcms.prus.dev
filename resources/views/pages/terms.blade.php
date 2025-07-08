@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
<main id="main-content" tabindex="-1" class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-50 dark:bg-gray-800 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Terms of Service') }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300">
                {{ __('Last updated: :date', ['date' => date('F j, Y')]) }}
            </p>
        </div>
    </div>

    <!-- Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 prose prose-lg max-w-none dark:prose-invert prose-indigo">
        <div>
            <h2>Agreement to Terms</h2>
            <p>
                By accessing and using our news portal, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
            </p>

            <h2>Use License</h2>
            <p>
                Permission is granted to temporarily download one copy of the materials on our news portal for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
            </p>
            <ul>
                <li>Modify or copy the materials</li>
                <li>Use the materials for any commercial purpose or for any public display (commercial or non-commercial)</li>
                <li>Attempt to decompile or reverse engineer any software contained on our website</li>
                <li>Remove any copyright or other proprietary notations from the materials</li>
            </ul>
            <p>
                This license shall automatically terminate if you violate any of these restrictions and may be terminated by us at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.
            </p>

            <h2>Disclaimer</h2>
            <p>
                The materials on our news portal are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
            </p>
            <p>
                Further, we do not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.
            </p>

            <h2>Limitations</h2>
            <p>
                In no event shall our news portal or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website, even if we or our authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
            </p>

            <h2>Accuracy of Materials</h2>
            <p>
                The materials appearing on our news portal could include technical, typographical, or photographic errors. We do not warrant that any of the materials on its website are accurate, complete, or current. We may make changes to the materials contained on its website at any time without notice. However, we do not make any commitment to update the materials.
            </p>

            <h2>Links</h2>
            <p>
                We have not reviewed all of the sites linked to our news portal and are not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by us of the site. Use of any such linked website is at the user's own risk.
            </p>

            <h2>User Content</h2>
            <p>
                Our service may allow you to post, link, store, share and otherwise make available certain information, text, graphics, videos, or other material ("Content"). You are responsible for the Content that you post to the service, including its legality, reliability, and appropriateness.
            </p>
            <p>
                By posting Content to the service, you grant us the right and license to use, modify, publicly perform, publicly display, reproduce, and distribute such Content on and through the service.
            </p>

            <h2>Prohibited Uses</h2>
            <p>You may not use our service:</p>
            <ul>
                <li>For any unlawful purpose or to solicit others to perform unlawful acts</li>
                <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
                <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
                <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
                <li>To submit false or misleading information</li>
                <li>To upload or transmit viruses or any other type of malicious code</li>
                <li>To collect or track the personal information of others</li>
                <li>To spam, phish, pharm, pretext, spider, crawl, or scrape</li>
                <li>For any obscene or immoral purpose</li>
                <li>To interfere with or circumvent the security features of the service</li>
            </ul>

            <h2>Account Terms</h2>
            <p>
                When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for safeguarding the password and for all activities that occur under your account.
            </p>

            <h2>Intellectual Property Rights</h2>
            <p>
                The service and its original content, features, and functionality are and will remain the exclusive property of our news portal and its licensors. The service is protected by copyright, trademark, and other laws.
            </p>

            <h2>Termination</h2>
            <p>
                We may terminate or suspend your account and bar access to the service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever and without limitation, including but not limited to a breach of the Terms.
            </p>

            <h2>Indemnification</h2>
            <p>
                You agree to defend, indemnify, and hold harmless our news portal and its licensee and licensors, and their employees, contractors, agents, officers and directors, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney's fees).
            </p>

            <h2>Governing Law</h2>
            <p>
                These Terms shall be interpreted and governed by the laws of the State, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights.
            </p>

            <h2>Changes to Terms</h2>
            <p>
                We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days notice prior to any new terms taking effect.
            </p>

            <h2>Contact Information</h2>
            <p>
                If you have any questions about these Terms of Service, please contact us:
            </p>
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg mt-6">
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li><strong>Email:</strong> legal@newsportal.com</li>
                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                    <li><strong>Address:</strong> 123 News Street, Media City, MC 12345</li>
                </ul>
            </div>
        </div>
    </section>
</main>
@endsection 