<?php
session_start();
require_once 'includes/functions.php';

// Check if user is logged in
$isLoggedIn = isAuthenticated();

// Get all services
$services = [
    [
        'id' => 1,
        'title' => 'Legal Consultation',
        'description' => 'Get expert legal advice from qualified professionals through secure video calls or chat.',
        'price' => 'Starting from $50/hour',
        'category' => 'Consultation',
        'icon' => '⚖️'
    ],
    [
        'id' => 2,
        'title' => 'Document Review',
        'description' => 'Professional review of legal documents with detailed feedback and recommendations.',
        'price' => 'Starting from $75',
        'category' => 'Document',
        'icon' => '📝'
    ],
    [
        'id' => 3,
        'title' => 'Contract Drafting',
        'description' => 'Custom contract drafting services for various legal needs.',
        'price' => 'Starting from $150',
        'category' => 'Document',
        'icon' => '📄'
    ],
    [
        'id' => 4,
        'title' => 'Legal Research',
        'description' => 'Comprehensive legal research services for your case.',
        'price' => 'Starting from $100',
        'category' => 'Research',
        'icon' => '🔍'
    ],
    [
        'id' => 5,
        'title' => 'Business Formation',
        'description' => 'Assistance with business registration and legal setup.',
        'price' => 'Starting from $200',
        'category' => 'Business',
        'icon' => '🏢'
    ],
    [
        'id' => 6,
        'title' => 'Intellectual Property',
        'description' => 'Trademark and copyright registration services.',
        'price' => 'Starting from $250',
        'category' => 'Business',
        'icon' => '💡'
    ]
];

// Filter services if category is selected
$selectedCategory = $_GET['category'] ?? '';
$filteredServices = $services;
if ($selectedCategory) {
    $filteredServices = array_filter($services, function($service) use ($selectedCategory) {
        return $service['category'] === $selectedCategory;
    });
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - LegalEase</title>
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
        <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-8">Our Services</h1>

        <!-- Filter Section -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-4">
                <a href="services.php" 
                   class="px-4 py-2 rounded-md <?php echo !$selectedCategory ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?>">
                    All Services
                </a>
                <?php
                $categories = array_unique(array_column($services, 'category'));
                foreach ($categories as $category):
                ?>
                    <a href="services.php?category=<?php echo urlencode($category); ?>" 
                       class="px-4 py-2 rounded-md <?php echo $selectedCategory === $category ? 'bg-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'; ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($filteredServices as $service): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                    <div class="text-4xl mb-4"><?php echo htmlspecialchars($service['icon']); ?></div>
                    <h3 class="text-xl font-semibold mb-2 text-primary dark:text-gray-200">
                        <?php echo htmlspecialchars($service['title']); ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        <?php echo htmlspecialchars($service['description']); ?>
                    </p>
                    <div class="text-primary dark:text-blue-400 font-semibold mb-4">
                        <?php echo htmlspecialchars($service['price']); ?>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <?php echo htmlspecialchars($service['category']); ?>
                        </span>
                        <?php if ($isLoggedIn): ?>
                            <a href="appointment.php?service=<?php echo urlencode($service['id']); ?>" 
                               class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-800 dark:hover:bg-secondary transition-all duration-300 ease-in-out transform hover:scale-105">
                                Book Now
                            </a>
                        <?php else: ?>
                            <a href="login.php" 
                               class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-800 dark:hover:bg-secondary transition-all duration-300 ease-in-out transform hover:scale-105"
                               onclick="sessionStorage.setItem('redirect_after_login', 'services.php')">
                                Login to Book
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($filteredServices)): ?>
            <div class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-300">No services found in this category.</p>
            </div>
        <?php endif; ?>
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