<?php
session_start();
require_once 'includes/functions.php';

$error = '';
$success = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = 'Please fill in all required fields';
        } else {
            // TODO: Implement email sending functionality
            $success = 'Thank you for your message! We will get back to you soon.';
        }
    } catch (Exception $e) {
        $error = 'An error occurred while sending your message';
        error_log("Contact form error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - LegalEase</title>
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
        <h1 class="text-4xl font-bold text-primary dark:text-gray-200 mb-8 text-center">Contact Us</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 max-w-2xl mx-auto" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 max-w-2xl mx-auto" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <!-- Contact Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transform hover:scale-[1.02] transition-transform duration-300">
                <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-6">Send us a Message</h2>
                <form method="POST" action="" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" id="name" name="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <input type="text" id="subject" name="subject" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" name="message" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200"></textarea>
                    </div>
                    <div>
                        <button type="submit"
                            class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transform hover:scale-[1.02] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">Contact Information</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="text-3xl mr-4">📧</div>
                            <div>
                                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Email</h3>
                                <p class="text-gray-600 dark:text-gray-400">support@legalease.com</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="text-3xl mr-4">📞</div>
                            <div>
                                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Phone</h3>
                                <p class="text-gray-600 dark:text-gray-400">(123) 456-7890</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="text-3xl mr-4">📍</div>
                            <div>
                                <h3 class="font-semibold text-gray-700 dark:text-gray-300">Office Hours</h3>
                                <p class="text-gray-600 dark:text-gray-400">Monday - Friday: 9:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 transform hover:scale-[1.02] transition-transform duration-300">
                    <h2 class="text-2xl font-semibold text-primary dark:text-gray-200 mb-4">Office Locations</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300">Headquarters</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                123 Legal Street<br>
                                Suite 100<br>
                                New York, NY 10001
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300">West Coast Office</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                456 Justice Avenue<br>
                                Suite 200<br>
                                San Francisco, CA 94105
                            </p>
                        </div>
                    </div>
                </div>
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