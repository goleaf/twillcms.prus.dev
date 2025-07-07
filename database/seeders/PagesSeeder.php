<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'excerpt' => 'Learn about how we collect, use, and protect your personal information.',
                'content' => '<div class="prose max-w-none">
                    <h2>Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you create an account, subscribe to our newsletter, or contact us for support.</p>
                    
                    <h3>Types of Information</h3>
                    <ul>
                        <li><strong>Personal Information:</strong> Name, email address, and any other information you choose to provide</li>
                        <li><strong>Usage Data:</strong> Information about how you use our website, including pages visited and time spent</li>
                        <li><strong>Device Information:</strong> Information about the device you use to access our services</li>
                    </ul>

                    <h2>How We Use Your Information</h2>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Provide, maintain, and improve our services</li>
                        <li>Send you newsletters and updates if you have subscribed</li>
                        <li>Respond to your inquiries and provide customer support</li>
                        <li>Analyze usage patterns to improve user experience</li>
                    </ul>

                    <h2>Information Sharing</h2>
                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>

                    <h2>Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

                    <h2>Cookies</h2>
                    <p>We use cookies to enhance your experience on our website. You can choose to disable cookies through your browser settings.</p>

                    <h2>Contact Information</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us using the information provided on our Contact page.</p>

                    <p><em>Last updated: January 2025</em></p>
                </div>',
                'meta_title' => 'Privacy Policy - NewsHub',
                'meta_description' => 'Our privacy policy explains how we handle your personal information and protect your privacy.',
                'meta_keywords' => 'privacy policy, data protection, personal information, cookies, GDPR',
                'published' => true,
                'published_at' => Carbon::now(),
                'position' => 1,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'excerpt' => 'Read our terms and conditions for using NewsHub services.',
                'content' => '<div class="prose max-w-none">
                    <h2>Acceptance of Terms</h2>
                    <p>By accessing and using NewsHub, you accept and agree to be bound by the terms and provision of this agreement.</p>

                    <h2>Use License</h2>
                    <p>Permission is granted to temporarily download one copy of the materials on NewsHub for personal, non-commercial transitory viewing only.</p>

                    <h3>This license shall not allow you to:</h3>
                    <ul>
                        <li>Modify or copy the materials</li>
                        <li>Use the materials for any commercial purpose or for any public display</li>
                        <li>Attempt to reverse engineer any software contained on the website</li>
                        <li>Remove any copyright or other proprietary notations from the materials</li>
                    </ul>

                    <h2>Disclaimer</h2>
                    <p>The materials on NewsHub are provided on an "as is" basis. NewsHub makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

                    <h2>Limitations</h2>
                    <p>In no event shall NewsHub or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on NewsHub, even if NewsHub or a NewsHub authorized representative has been notified orally or in writing of the possibility of such damage.</p>

                    <h2>Accuracy of Materials</h2>
                    <p>The materials appearing on NewsHub could include technical, typographical, or photographic errors. NewsHub does not warrant that any of the materials on its website are accurate, complete, or current.</p>

                    <h2>Links</h2>
                    <p>NewsHub has not reviewed all of the sites linked to our website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by NewsHub of the site.</p>

                    <h2>Modifications</h2>
                    <p>NewsHub may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>

                    <p><em>Last updated: January 2025</em></p>
                </div>',
                'meta_title' => 'Terms of Service - NewsHub',
                'meta_description' => 'Terms and conditions for using NewsHub services and content.',
                'meta_keywords' => 'terms of service, terms and conditions, legal, usage policy',
                'published' => true,
                'published_at' => Carbon::now(),
                'position' => 2,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'excerpt' => 'Get in touch with the NewsHub team for inquiries, support, or feedback.',
                'content' => '<div class="prose max-w-none">
                    <h2>Get in Touch</h2>
                    <p>We would love to hear from you! Whether you have a question about our content, need support, or just want to provide feedback, we are here to help.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 my-8">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">General Inquiries</h3>
                            <p>For general questions about NewsHub, our content, or services:</p>
                            <p class="mt-2">
                                <strong>Email:</strong> <a href="mailto:info@newshub.com" class="text-blue-600 hover:underline">info@newshub.com</a><br>
                                <strong>Phone:</strong> <a href="tel:+1234567890" class="text-blue-600 hover:underline">+1 (234) 567-890</a>
                            </p>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">Editorial Team</h3>
                            <p>For editorial inquiries, press releases, or content submissions:</p>
                            <p class="mt-2">
                                <strong>Email:</strong> <a href="mailto:editorial@newshub.com" class="text-blue-600 hover:underline">editorial@newshub.com</a><br>
                                <strong>Phone:</strong> <a href="tel:+1234567891" class="text-blue-600 hover:underline">+1 (234) 567-891</a>
                            </p>
                        </div>
                    </div>

                    <h2>Office Address</h2>
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <address class="not-italic">
                            <strong>NewsHub Headquarters</strong><br>
                            123 Media Street<br>
                            Suite 456<br>
                            News City, NC 12345<br>
                            United States
                        </address>
                    </div>

                    <h2>Business Hours</h2>
                    <p>Our team is available during the following hours:</p>
                    <ul>
                        <li><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM EST</li>
                        <li><strong>Saturday:</strong> 10:00 AM - 4:00 PM EST</li>
                        <li><strong>Sunday:</strong> Closed</li>
                    </ul>

                    <h2>Social Media</h2>
                    <p>Follow us on social media for the latest updates:</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-blue-600 hover:underline">Twitter</a>
                        <a href="#" class="text-blue-600 hover:underline">Facebook</a>
                        <a href="#" class="text-blue-600 hover:underline">LinkedIn</a>
                        <a href="#" class="text-blue-600 hover:underline">Instagram</a>
                    </div>

                    <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                        <p class="text-sm"><strong>Note:</strong> For urgent matters outside of business hours, please use our emergency contact form or call our main number and leave a detailed message.</p>
                    </div>
                </div>',
                'meta_title' => 'Contact Us - NewsHub',
                'meta_description' => 'Contact NewsHub team for inquiries, support, or feedback. Multiple ways to reach us.',
                'meta_keywords' => 'contact, support, feedback, inquiries, customer service',
                'published' => true,
                'published_at' => Carbon::now(),
                'position' => 3,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('Static pages seeded successfully!');
    }
}
