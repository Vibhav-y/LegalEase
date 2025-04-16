<?php
session_start();
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - LegalEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#1D4E89',
                        secondary: '#D4AF37',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 pt-24">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-primary dark:text-gray-200 mb-8 text-center">Privacy Policy</h1>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 space-y-8">
                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">1. Introduction</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        At LegalEase, we take your privacy seriously. This Privacy Policy explains how we collect, use, 
                        disclose, and safeguard your information when you use our platform.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, 
                        please do not access the site.
                    </p>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">2. Information We Collect</h2>
                    <h3 class="text-xl font-semibold text-primary dark:text-gray-200 mb-2">Personal Information</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        We collect personal information that you voluntarily provide to us when you register on the platform, 
                        express an interest in obtaining information about us or our products and services, or otherwise 
                        contact us. The personal information we collect may include:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Name</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Email address</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Phone number</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Address</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Payment information</span>
                        </li>
                    </ul>
                    <h3 class="text-xl font-semibold text-primary dark:text-gray-200 mb-2">Usage Data</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        We may also collect information about how you access and use our platform, including:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>IP address</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Browser type</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Device information</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Pages visited</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Time spent on pages</span>
                        </li>
                    </ul>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">3. How We Use Your Information</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        We use the information we collect to:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Provide and maintain our services</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Process your transactions</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Send you updates and marketing communications</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Improve our platform and services</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Comply with legal obligations</span>
                        </li>
                    </ul>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">4. Information Sharing</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        We may share your information with:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Legal professionals providing services through our platform</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Service providers who assist in our operations</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Law enforcement when required by law</span>
                        </li>
                    </ul>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">5. Data Security</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        We implement appropriate technical and organizational security measures to protect your personal 
                        information. However, no method of transmission over the Internet or electronic storage is 100% 
                        secure, and we cannot guarantee absolute security.
                    </p>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">6. Your Rights</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 leading-relaxed">
                        You have the right to:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4 space-y-2">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Access your personal information</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Correct inaccurate information</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Request deletion of your information</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Object to processing of your information</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Request data portability</span>
                        </li>
                    </ul>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">7. Contact Us</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        If you have any questions about this Privacy Policy, please contact us at:
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">
                        Email: privacy@legalease.com<br>
                        Address: 123 Legal Street, Suite 100, New York, NY 10001
                    </p>
                </section>

                <section class="transform hover:scale-[1.01] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">8. Changes to This Policy</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        We may update this Privacy Policy from time to time. We will notify you of any changes by posting 
                        the new Privacy Policy on this page and updating the "Last Updated" date.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">
                        Last Updated: <?php echo date('F d, Y'); ?>
                    </p>
                </section>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">LegalEase</h3>
                    <p class="text-gray-400">Making legal services accessible to everyone.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white">Services</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="privacy.php" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-400">Email: support@legalease.com</li>
                        <li class="text-gray-400">Phone: (123) 456-7890</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> LegalEase. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Theme Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;
            
            // Check for saved theme preference or use system preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                html.classList.toggle('dark', savedTheme === 'dark');
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            }

            // Theme toggle click handler
            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                const isDark = html.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                // Update theme toggle button icon
                const moonIcon = themeToggle.querySelector('.dark\\:hidden');
                const sunIcon = themeToggle.querySelector('.dark\\:inline');
                if (isDark) {
                    moonIcon.classList.add('hidden');
                    sunIcon.classList.remove('hidden');
                } else {
                    moonIcon.classList.remove('hidden');
                    sunIcon.classList.add('hidden');
                }
            });

            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('theme')) {
                    html.classList.toggle('dark', e.matches);
                }
            });
        });
    </script>
</body>
</html> 