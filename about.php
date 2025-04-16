<?php
session_start();
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LegalEase</title>
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

    <div class="container mx-auto px-4 py-12">
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-2xl mb-16 group">
    <div class="absolute inset-0 bg-gradient-to-r from-primary to-blue-900 opacity-90 transition-opacity duration-500 group-hover:opacity-100"></div>
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1589998059171-988d887df646')] bg-cover bg-center transform scale-105 group-hover:scale-100 transition-transform duration-700"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8">
        <div class="text-center transform translate-y-0 group-hover:-translate-y-2 transition-transform duration-500">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl drop-shadow-lg">
                About LegalEase
            </h1>
            <p class="mt-6 max-w-3xl mx-auto text-xl text-gray-100 drop-shadow-lg">
                Your trusted partner in legal consultation and document management
            </p>
            <div class="mt-8 flex justify-center space-x-4">
                <a href="#mission" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105">
                    Learn More
                </a>
                <a href="contact.php" class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-all duration-300 transform hover:scale-105">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>


        <!-- Mission Section -->
        <div class="max-w-7xl mx-auto mb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold text-primary dark:text-white">Our Mission</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        At LegalEase, we're dedicated to making legal services accessible and efficient for everyone. 
                        Our platform connects you with experienced lawyers and provides tools for managing your legal documents.
                    </p>
                    <div class="flex space-x-4">
                        <div class="flex-1 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <i class="fas fa-balance-scale text-3xl text-primary dark:text-blue-400 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Legal Expertise</h3>
                            <p class="text-gray-600 dark:text-gray-400">Access to qualified lawyers across various specializations</p>
                        </div>
                        <div class="flex-1 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <i class="fas fa-file-contract text-3xl text-primary dark:text-blue-400 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Document Management</h3>
                            <p class="text-gray-600 dark:text-gray-400">Secure and organized document handling</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                             alt="Legal Consultation" 
                             class="object-cover w-full h-full">
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-center text-primary dark:text-white mb-12">Why Choose LegalEase?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 dark:bg-opacity-20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-2xl text-primary dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">24/7 Availability</h3>
                    <p class="text-gray-600 dark:text-gray-400">Access legal services anytime, anywhere through our online platform.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 dark:bg-opacity-20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-2xl text-primary dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Secure & Confidential</h3>
                    <p class="text-gray-600 dark:text-gray-400">Your data and documents are protected with enterprise-grade security.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="w-16 h-16 bg-primary bg-opacity-10 dark:bg-opacity-20 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-2xl text-primary dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Cost-Effective</h3>
                    <p class="text-gray-600 dark:text-gray-400">Quality legal services at competitive rates with transparent pricing.</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="max-w-7xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-center text-primary dark:text-white mb-12">Meet Our Team</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="aspect-w-1 aspect-h-1 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Sarah Johnson" 
                             class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Sarah Johnson</h3>
                        <p class="text-primary dark:text-blue-400 mb-2">CEO & Founder</p>
                        <p class="text-gray-600 dark:text-gray-400">20+ years of legal experience</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="aspect-w-1 aspect-h-1 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Michael Chen" 
                             class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Michael Chen</h3>
                        <p class="text-primary dark:text-blue-400 mb-2">CTO</p>
                        <p class="text-gray-600 dark:text-gray-400">Tech innovation expert</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="aspect-w-1 aspect-h-1 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Emily Rodriguez" 
                             class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Emily Rodriguez</h3>
                        <p class="text-primary dark:text-blue-400 mb-2">Legal Director</p>
                        <p class="text-gray-600 dark:text-gray-400">Specialized in corporate law</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <div class="aspect-w-1 aspect-h-1 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="David Wilson" 
                             class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">David Wilson</h3>
                        <p class="text-primary dark:text-blue-400 mb-2">Head of Operations</p>
                        <p class="text-gray-600 dark:text-gray-400">Customer experience specialist</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-primary rounded-2xl p-12 text-center">
                <h2 class="text-3xl font-bold text-white mb-6">Ready to Get Started?</h2>
                <p class="text-xl text-gray-100 mb-8">Join thousands of satisfied clients who trust LegalEase for their legal needs.</p>
                <div class="flex justify-center space-x-4">
                    <a href="register.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                        Sign Up Now
                    </a>
                    <a href="contact.php" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors duration-200">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-2xl font-bold mb-6">LegalEase</h3>
                    <p class="text-gray-400">Making legal services accessible to everyone.</p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="services.php" class="text-gray-400 hover:text-white transition-colors duration-200">Services</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="privacy.php" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-6">Contact</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-envelope mr-3"></i>
                            support@legalease.com
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-phone mr-3"></i>
                            (123) 456-7890
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-map-marker-alt mr-3"></i>
                            123 Legal Street, Suite 100
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> LegalEase. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Theme handling
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        function toggleFAQ(id) {
            const content = document.getElementById(id + '-content');
            const icon = document.getElementById(id + '-icon');
            
            // Toggle content visibility with smooth animation
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.classList.add('rotate-180');
            } else {
                content.style.maxHeight = '0';
                setTimeout(() => {
                    content.classList.add('hidden');
                }, 300);
                icon.classList.remove('rotate-180');
            }
        }

        // Initialize FAQ items
        document.querySelectorAll('[id$="-content"]').forEach(content => {
            content.style.maxHeight = '0';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.3s ease-in-out';
        });
    </script>
</body>
</html> 