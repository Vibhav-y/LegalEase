<?php
session_start();
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LegalEase - Online Legal Consultation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
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
    <style>
        /* Enhanced Hero Section */
        .hero-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-content {
            position: relative;
            z-index: 10;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
        }

        .hero-description {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto 2rem auto;
            line-height: 1.8;
        }

        .hero-btn-primary {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            border: 2px solid transparent;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .hero-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, #C5A028 0%, #D4AF37 100%);
        }

        .hero-btn-secondary {
            background: white;
            color: #1D4E89;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .hero-btn-secondary:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        /* Enhanced Feature Cards */
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .dark .feature-card {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1D4E89, #2C6CB0);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Stats Cards */
        .stat-card {
            background: linear-gradient(135deg, #1D4E89 0%, #2C6CB0 100%);
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .stat-card {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            z-index: 1;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Step Cards */
        .step-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dark .step-card {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .step-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .step-number {
            background: linear-gradient(135deg, #1D4E89, #2C6CB0);
            box-shadow: 0 4px 15px rgba(29, 78, 137, 0.3);
        }

        .dark .step-number {
            background: linear-gradient(135deg, #D4AF37, #C5A028);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        /* Enhanced FAQ Section */
        .faq-item {
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .dark .faq-item {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .faq-button {
            transition: all 0.3s ease;
        }

        .faq-item.active {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Testimonial Cards */
        .testimonial-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .testimonial-card {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .testimonial-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Buttons */
        .btn-primary, .btn-secondary {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary::before, .btn-secondary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-primary:hover::before, .btn-secondary:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.6s ease-out forwards;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #1D4E89, #2C6CB0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        /* Premium Gold Accents */
        .gold-gradient {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
        }

        .gold-text {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .premium-border {
            border: 2px solid #D4AF37;
        }

        .premium-card {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(197, 160, 40, 0.05) 100%);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        /* Update stat cards with gold accents */
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1D4E89, #2C6CB0);
        }

        .dark .stat-card::after {
            background: linear-gradient(90deg, #D4AF37, #C5A028);
        }

        /* Update Feature Highlight Cards */
        .feature-highlight-card {
            @apply p-8 rounded-2xl transition-all duration-500;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(29, 78, 137, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            min-height: 250px;
        }

        .dark .feature-highlight-card {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .feature-highlight-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1D4E89, #2C6CB0);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .dark .feature-highlight-card::before {
            background: linear-gradient(90deg, #D4AF37, #C5A028);
        }

        .feature-highlight-card:hover::before {
            transform: scaleX(1);
        }

        .feature-highlight-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .icon-highlight {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(29, 78, 137, 0.1), rgba(29, 78, 137, 0.05));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.5s ease;
            position: relative;
            border: 1px solid rgba(29, 78, 137, 0.2);
        }

        .dark .icon-highlight {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(197, 160, 40, 0.05));
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .icon-highlight i {
            color: #1D4E89;
            transition: all 0.5s ease;
        }

        .dark .icon-highlight i {
            color: #1D4E89;
        }

        .feature-highlight-card:hover .icon-highlight {
            background: linear-gradient(135deg, #1D4E89, #2C6CB0);
            transform: rotateY(360deg);
        }

        .dark .feature-highlight-card:hover .icon-highlight {
            background: linear-gradient(135deg, #D4AF37, #C5A028);
        }

        .feature-highlight-card:hover .icon-highlight i {
            color: white;
        }

        .feature-highlight-card h3 {
            color: #1D4E89;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .dark .feature-highlight-card h3 {
            color: #D4AF37;
        }

        .feature-highlight-card p {
            color: #666;
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        .dark .feature-highlight-card p {
            color: #9CA3AF;
        }

        .feature-highlight-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1D4E89, transparent);
            opacity: 0;
            transition: all 0.5s ease;
        }

        .dark .feature-highlight-card::after {
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }

        .feature-highlight-card:hover::after {
            opacity: 1;
            width: 80%;
        }

        /* Update Trust Indicators Section */
        .icon-container {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: rgba(255, 255, 255, 0.1);
        }

        .dark .icon-container {
            background: rgba(212, 175, 55, 0.1);
        }

        .icon-container.gold-gradient {
            background: linear-gradient(135deg, #1D4E89, #2C6CB0);
        }

        .dark .icon-container.gold-gradient {
            background: linear-gradient(135deg, #D4AF37, #C5A028);
        }

        /* Update CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1D4E89 0%, #2C6CB0 100%);
        }

        .dark .cta-section {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
        }

        /* Trust Cards Styling */
        .trust-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dark .trust-card {
            background: rgba(31, 41, 55, 0.95);
        }

        .trust-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .trust-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(197, 160, 40, 0) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .trust-card:hover::after {
            opacity: 1;
        }

        .trust-icon-container {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: #1D4E89;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dark .trust-icon-container {
            background: #1D4E89;
        }

        .trust-icon-container i {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .trust-card:hover .trust-icon-container {
            background: #1D4E89;
            transform: scale(1.1);
        }

        .dark .trust-card:hover .trust-icon-container {
            background: linear-gradient(135deg, #D4AF37, #C5A028);
            transform: scale(1.1);
        }

        .trust-icon-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            transition: transform 0.4s ease;
        }

        .trust-card:hover .trust-icon-container::before {
            transform: translateX(100%);
        }

        .trust-divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #1D4E89, transparent);
            margin: 1.5rem auto 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .trust-divider {
            background: linear-gradient(90deg, transparent, #1D4E89, transparent);
        }

        .trust-card:hover .trust-divider {
            width: 100px;
            background: linear-gradient(90deg, transparent, #1D4E89, transparent);
        }

        .dark .trust-card:hover .trust-divider {
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
        }

        .service-card {
            @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden;
        }
        .service-card:hover {
            transform: translateY(-5px);
        }

        /* Enhanced Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse {
            animation: pulse 4s ease-in-out infinite;
        }

        .animate-slide-in {
            animation: slideIn 1s ease-out forwards;
        }

        /* Enhanced Service Cards */
        .service-card {
            @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden;
            position: relative;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1D4E89, #2C6CB0);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        /* Enhanced How It Works Section */
        .step-card {
            position: relative;
            overflow: hidden;
        }

        .step-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(29, 78, 137, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .step-card:hover::after {
            opacity: 1;
        }

        /* Enhanced FAQ Section */
        .faq-item {
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateX(10px);
        }

        /* Enhanced CTA Section */
        .cta-button {
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .cta-button:hover::before {
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero-bg">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-6xl font-bold mb-6 leading-tight">Legal Services Made Simple</h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Access professional legal consultation and document generation from the comfort of your home.</p>
            <div class="flex justify-center space-x-6">
                <a href="register.php" class="hero-btn-primary">
                    Get Started
                </a>
                <a href="services.php" class="hero-btn-secondary">
                    Explore Services
                </a>
            </div>
        </div>
    </div>

    <!-- Features Highlight -->
    <div class="py-20 px-10 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-20">
                <span class="text-primary dark:text-white">Our Features</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="feature-highlight-card group flex flex-col items-center justify-center">
                    <div class="icon-highlight">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mt-8 mb-6 text-primary dark:text-white text-center">24/7 Availability</h3>
                    <p class="text-gray-600 dark:text-gray-300 px-4 text-center">Access legal help anytime, anywhere</p>
                </div>
                <div class="feature-highlight-card group flex flex-col items-center justify-center">
                    <div class="icon-highlight">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mt-8 mb-6 text-primary dark:text-white text-center">Secure & Confidential</h3>
                    <p class="text-gray-600 dark:text-gray-300 px-4 text-center">Your data is protected with us</p>
                </div>
                <div class="feature-highlight-card group flex flex-col items-center justify-center">
                    <div class="icon-highlight">
                        <i class="fas fa-hand-holding-usd text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mt-8 mb-6 text-primary dark:text-white text-center">Affordable Rates</h3>
                    <p class="text-gray-600 dark:text-gray-300 px-4 text-center">Quality legal services at fair prices</p>
                </div>
                <div class="feature-highlight-card group flex flex-col items-center justify-center">
                    <div class="icon-highlight">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mt-8 mb-6 text-primary dark:text-white text-center">Expert Lawyers</h3>
                    <p class="text-gray-600 dark:text-gray-300 px-4 text-center">Connect with verified professionals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Indicators -->
    <div class="py-20 bg-gradient-to-b from-white to-gray-100 dark:from-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16">
                <span class="text-primary dark:text-white">Why Choose LegalEase</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="trust-card p-8 animate-slide-up">
                    <div class="trust-icon-container">
                        <i class="fas fa-users text-4xl text-white"></i>
                    </div>
                    <h3 class="text-5xl font-bold text-primary dark:text-white mb-2">10,000+</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Happy Clients</p>
                    <div class="trust-divider"></div>
                </div>
                <div class="trust-card p-8 animate-slide-up" style="animation-delay: 0.2s">
                    <div class="trust-icon-container">
                        <i class="fas fa-gavel text-4xl text-white"></i>
                    </div>
                    <h3 class="text-5xl font-bold text-primary dark:text-white mb-2">500+</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Legal Professionals</p>
                    <div class="trust-divider"></div>
                </div>
                <div class="trust-card p-8 animate-slide-up" style="animation-delay: 0.4s">
                    <div class="trust-icon-container">
                        <i class="fas fa-clock text-4xl text-white"></i>
                    </div>
                    <h3 class="text-5xl font-bold text-primary dark:text-white mb-2">24/7</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Support Available</p>
                    <div class="trust-divider"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div id="services" class="py-16 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center mb-12">
                <h2 class="text-4xl font-bold text-center text-primary dark:text-white mb-4 animate-slide-in">Our Services</h2>
                <div class="w-24 h-1 bg-primary rounded-full animate-pulse"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="service-card group">
                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1575505586569-646b2ca898fc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Legal Consultation" 
                             class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 group-hover:text-primary transition-colors duration-300">Legal Consultation</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Get expert legal advice from qualified professionals through secure video calls or chat.</p>
                        <div class="space-y-3">
                            <div class="flex items-center group-hover:translate-x-2 transition-transform duration-300">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">24/7 Availability</span>
                            </div>
                            <div class="flex items-center group-hover:translate-x-2 transition-transform duration-300">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Secure Communication</span>
                            </div>
                            <div class="flex items-center group-hover:translate-x-2 transition-transform duration-300">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Expert Legal Advice</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="service-card group">
                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Document Generation" 
                             class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Document Generation</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Create professional legal documents with our easy-to-use document generator.</p>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Customizable Templates</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Instant Generation</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Legal Compliance</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="service-card group">
                    <div class="relative overflow-hidden rounded-2xl">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent"></div>
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="Mobile Access" 
                             class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Mobile Access</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Access our services anytime, anywhere through our mobile-friendly platform.</p>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Responsive Design</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Real-time Updates</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-primary mr-3"></i>
                                <span class="text-gray-700 dark:text-gray-200">Secure Access</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-20 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16 text-primary dark:text-white animate-slide-in">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="step-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent rounded-2xl transform group-hover:scale-105 transition-transform duration-500"></div>
                    <div class="relative p-8">
                        <div class="text-4xl font-bold text-primary mb-6 animate-float">01</div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-primary transition-colors duration-300">Create Account</h3>
                        <p class="text-gray-600 dark:text-gray-300">Sign up in minutes and complete your profile to get started with our services.</p>
                        <div class="mt-8">
                            <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Create Account" 
                                 class="rounded-xl shadow-lg transform group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                </div>

                <div class="step-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent rounded-2xl transform group-hover:scale-105 transition-transform duration-500"></div>
                    <div class="relative p-8">
                        <div class="text-4xl font-bold text-primary mb-6 animate-float">02</div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-primary transition-colors duration-300">Choose Service</h3>
                        <p class="text-gray-600 dark:text-gray-300">Select from our range of legal services that best suits your needs.</p>
                        <div class="mt-8">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Choose Service" 
                                 class="rounded-xl shadow-lg transform group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                </div>

                <div class="step-card group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent rounded-2xl transform group-hover:scale-105 transition-transform duration-500"></div>
                    <div class="relative p-8">
                        <div class="text-4xl font-bold text-primary mb-6 animate-float">03</div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 group-hover:text-primary transition-colors duration-300">Get Started</h3>
                        <p class="text-gray-600 dark:text-gray-300">Connect with lawyers and get instant help for your legal matters.</p>
                        <div class="mt-8">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Get Started" 
                                 class="rounded-xl shadow-lg transform group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="max-w-7xl mx-auto mb-16 px-4 sm:px-6 lg:px-8 pt-20">
        <h2 class="text-3xl font-bold text-center text-primary dark:text-white mb-12 animate-slide-in">Frequently Asked Questions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="faq-item bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                    <button class="w-full p-6 text-left focus:outline-none group" onclick="toggleFAQ('faq1')">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-primary dark:group-hover:text-blue-400 transition-colors duration-300">How do I book a legal consultation?</h3>
                            <i class="fas fa-chevron-down text-primary dark:text-blue-400 transform transition-transform duration-300 group-hover:rotate-180" id="faq1-icon"></i>
                        </div>
                        <div class="mt-4 text-gray-600 dark:text-gray-400 hidden transition-all duration-300 ease-in-out" id="faq1-content">
                            You can book a consultation by selecting a lawyer from our directory, choosing an available time slot, and completing the booking form. Our system will confirm your appointment via email.
                        </div>
                    </button>
                </div>
                <div class="faq-item bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                    <button class="w-full p-6 text-left focus:outline-none group" onclick="toggleFAQ('faq2')">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-primary dark:group-hover:text-blue-400 transition-colors duration-300">What types of legal documents can I generate?</h3>
                            <i class="fas fa-chevron-down text-primary dark:text-blue-400 transform transition-transform duration-300 group-hover:rotate-180" id="faq2-icon"></i>
                        </div>
                        <div class="mt-4 text-gray-600 dark:text-gray-400 hidden transition-all duration-300 ease-in-out" id="faq2-content">
                            Our platform supports various legal documents including contracts, agreements, wills, and more. Each document is customizable to meet your specific needs.
                        </div>
                    </button>
                </div>
                <div class="faq-item bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                    <button class="w-full p-6 text-left focus:outline-none group" onclick="toggleFAQ('faq3')">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white group-hover:text-primary dark:group-hover:text-blue-400 transition-colors duration-300">How secure is my information?</h3>
                            <i class="fas fa-chevron-down text-primary dark:text-blue-400 transform transition-transform duration-300 group-hover:rotate-180" id="faq3-icon"></i>
                        </div>
                        <div class="mt-4 text-gray-600 dark:text-gray-400 hidden transition-all duration-300 ease-in-out" id="faq3-content">
                            We use industry-standard encryption and security measures to protect your data. All information is stored securely and only accessible to authorized personnel.
                        </div>
                    </button>
                </div>
            </div>
            <div class="relative">
                <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-300">
                    <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                         alt="FAQ Illustration" 
                         class="object-cover w-full h-full">
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action Section -->
    <div class="py-20 bg-gradient-to-r from-primary to-blue-700 dark:from-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-8 animate-slide-in">Ready to Get Started?</h2>
            <p class="text-xl text-white/90 mb-12 max-w-2xl mx-auto">Join thousands of satisfied clients who trust LegalEase for their legal needs</p>
            <div class="flex justify-center gap-6">
                <a href="register.php" class="cta-button bg-white text-primary px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    Get Started
                </a>
                <a href="#services" class="cta-button border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white/10 transition-all duration-300">
                    Learn More
                </a>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-primary dark:text-white">What Our Clients Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mx-8">
                <!-- Testimonial 1 -->
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg transition-all duration-500 hover:shadow-xl hover:-translate-y-2 flex flex-col relative overflow-hidden group h-[300px]">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="flex items-center mb-6 relative z-10">
                        <img src="assets/images/testimonial1.jpg" alt="Sarah Johnson" class="w-14 h-14 rounded-full mr-4 ring-2 ring-primary/20 group-hover:ring-primary/50 transition-all duration-500">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors duration-500">Sarah Johnson</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">Business Owner</p>
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 relative z-10 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-500 text-lg leading-relaxed">"LegalEase has transformed how I handle legal matters. The platform is intuitive and the lawyers are exceptional."</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg transition-all duration-500 hover:shadow-xl hover:-translate-y-2 flex flex-col relative overflow-hidden group h-[300px]">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="flex items-center mb-6 relative z-10">
                        <img src="assets/images/testimonial2.jpg" alt="Michael Brown" class="w-14 h-14 rounded-full mr-4 ring-2 ring-primary/20 group-hover:ring-primary/50 transition-all duration-500">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors duration-500">Michael Brown</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">Startup Founder</p>
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 relative z-10 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-500 text-lg leading-relaxed">"The document generation feature saved me countless hours. It's like having a legal team at your fingertips."</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg transition-all duration-500 hover:shadow-xl hover:-translate-y-2 flex flex-col relative overflow-hidden group h-[300px]">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="flex items-center mb-6 relative z-10">
                        <img src="assets/images/testimonial3.jpg" alt="Emily Davis" class="w-14 h-14 rounded-full mr-4 ring-2 ring-primary/20 group-hover:ring-primary/50 transition-all duration-500">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary transition-colors duration-500">Emily Davis</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">Freelancer</p>
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 relative z-10 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-500 text-lg leading-relaxed">"I was able to get legal advice within minutes. The convenience and quality of service is unmatched."</p>
                </div>
            </div>
        </div>
    </section>

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

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animate elements on scroll
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.animate-slide-up, .animate-slide-right');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementBottom = element.getBoundingClientRect().bottom;
                if (elementTop < window.innerHeight && elementBottom > 0) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        };

        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);

        // FAQ Accordion
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const button = item.querySelector('.faq-button');
            button.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                // Close all FAQ items
                faqItems.forEach(faq => faq.classList.remove('active'));
                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

        // Smooth reveal animation for elements
        const revealElements = document.querySelectorAll('.feature-card, .stat-card, .step-card, .testimonial-card');

        const revealOnScroll = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    revealOnScroll.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        revealElements.forEach(element => {
            element.style.opacity = "0";
            revealOnScroll.observe(element);
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-bg');
            hero.style.backgroundPositionY = scrolled * 0.5 + 'px';
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

        // Add smooth transition for testimonial cards
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        testimonialCards.forEach(card => {
            card.style.transition = 'transform 0.5s ease, box-shadow 0.5s ease';
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
                card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });
        });
    </script>
</body>
</html> 