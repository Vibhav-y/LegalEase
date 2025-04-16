<?php
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'User') : '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<nav class="bg-white/90 dark:bg-gray-800/90 shadow-lg fixed w-full z-50 backdrop-blur-md">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-2">
                <?php include 'logo.php'; ?>
            </div>

            <div class="flex items-center space-x-6">
                <a href="index.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Home</a>
                <a href="about.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">About</a>
                <a href="services.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Services</a>
                <a href="docs.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Documents</a>
                <a href="contact.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Contact</a>
                
                <?php if ($isLoggedIn): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-primary hover:text-blue-800 dark:text-blue-400 transition-colors duration-200">
                            <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white font-semibold"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                            </div>
                            <span class="hidden md:inline"><?php echo htmlspecialchars($userName); ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right scale-95 group-hover:scale-100">
                            <a href="Userdashboard.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-user-circle mr-2"></i>My Profile
                            </a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                    <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-300 dark:hover:bg-gray-600">
                        <span class="dark:hidden">🌙</span>
                        <span class="hidden dark:inline">☀️</span>
                    </button>
                <?php else: ?>
                    <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200 hover:bg-gray-300 dark:hover:bg-gray-600">
                        <span class="dark:hidden">🌙</span>
                        <span class="hidden dark:inline">☀️</span>
                    </button>
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="px-4 py-2 text-primary border-2 border-primary hover:bg-primary hover:text-white dark:text-blue-400 dark:border-blue-400 dark:hover:bg-blue-600 dark:hover:text-white rounded-md transition-all duration-300 font-medium hover:shadow-md">
                            Login
                        </a>
                        <a href="register.php" class="px-4 py-2 bg-primary text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-md transition-all duration-300 font-medium hover:shadow-md">
                            Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<style>
    .nav-link {
        position: relative;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: currentColor;
        transition: width 0.3s ease;
    }
    
    .nav-link:hover::after {
        width: 100%;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .group:hover .group-hover\:animate-fadeIn {
        animation: fadeIn 0.2s ease-out forwards;
    }
</style>

<div class="pt-16">
    <!-- Your existing content -->
</div> 