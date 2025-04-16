<?php
session_start();
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - LegalEase</title>
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
            <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-8">Terms of Service</h1>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 space-y-8">
                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">1. Acceptance of Terms</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        By accessing and using LegalEase, you accept and agree to be bound by the terms and provision of this agreement.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300">
                        If you do not agree to abide by the above, please do not use this service.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">2. Description of Service</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        LegalEase provides an online platform for legal consultation and document generation. Our services include:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4">
                        <li>Online legal consultations</li>
                        <li>Document generation services</li>
                        <li>Legal information and resources</li>
                        <li>Connection with legal professionals</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">3. User Responsibilities</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        As a user of LegalEase, you agree to:
                    </p>
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 mb-4">
                        <li>Provide accurate and complete information</li>
                        <li>Maintain the security of your account</li>
                        <li>Use the service in compliance with all applicable laws</li>
                        <li>Not engage in any fraudulent or illegal activities</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">4. Intellectual Property</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        All content and materials available on LegalEase, including but not limited to text, graphics, website name, code, images and logos are the intellectual property of LegalEase and are protected by applicable copyright and trademark law.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">5. Limitation of Liability</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        LegalEase shall not be liable for any indirect, incidental, special, consequential or punitive damages resulting from your use of or inability to use the service.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">6. Changes to Terms</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        We reserve the right to modify these terms at any time. We will notify users of any changes by updating the "Last Updated" date of these terms.
                    </p>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
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