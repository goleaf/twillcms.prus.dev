@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded z-50">Skip to main content</a>
<div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="prose prose-lg max-w-none dark:prose-invert">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Terms of Service</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Last updated: {{ date('F j, Y') }}
            </p>
            
            <div class="text-gray-700 dark:text-gray-300 space-y-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">1. Acceptance of Terms</h2>
                <p>By accessing and using this news portal, you accept and agree to be bound by the terms and provision of this agreement.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">2. Use License</h2>
                <p>Permission is granted to temporarily download one copy of the materials on our news portal for personal, non-commercial transitory viewing only.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">3. Disclaimer</h2>
                <p>The materials on this news portal are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">4. Limitations</h2>
                <p>In no event shall our news portal or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website, even if we or our authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">5. Accuracy of Materials</h2>
                <p>The materials appearing on our news portal could include technical, typographical, or photographic errors. We do not warrant that any of the materials on its website are accurate, complete, or current. We may make changes to the materials contained on its website at any time without notice. However, we do not make any commitment to update the materials.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">6. Links</h2>
                <p>We have not reviewed all of the sites linked to our news portal and are not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by us of the site. Use of any such linked website is at the user's own risk.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">7. Modifications</h2>
                <p>We may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">8. Governing Law</h2>
                <p>These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.</p>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">Contact Information</h2>
                <p>If you have any questions about these Terms of Service, please contact us at legal@newsportal.com.</p>
            </div>
        </div>
    </div>
</div>
@endsection 