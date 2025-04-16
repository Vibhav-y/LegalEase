<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'includes/functions.php';
require_once __DIR__ . '/database/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user information from session
$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
$createdAt = $_SESSION['created_at'] ?? date('Y-m-d H:i:s');

// Get user's appointments
$appointments = getClientAppointments($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - LegalEase</title>
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

    <div class="container mx-auto px-4 pt-24 pb-8">
        <div class="max-w-4xl mx-auto">
            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-primary dark:text-white">My Profile</h1>
                        <p class="text-gray-600 dark:text-gray-400">Manage your account settings and preferences</p>
                    </div>
                    <div class="h-24 w-24 rounded-full bg-primary flex items-center justify-center shadow-lg">
                        <span class="text-white text-4xl font-bold"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                            <p class="text-lg text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($userName); ?></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <p class="text-lg text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($userEmail); ?></p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Status</label>
                            <p class="mt-1">
                                <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-2"></i>Active
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Member Since</label>
                            <p class="text-lg text-gray-900 dark:text-white font-medium"><?php echo date('F Y', strtotime($createdAt)); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-primary dark:text-white mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="edit_profile.php" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 group">
                            <i class="fas fa-user-edit text-primary dark:text-blue-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                            <span class="text-gray-900 dark:text-white">Edit Profile</span>
                        </a>
                        <button onclick="showLogoutModal()" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 group">
                            <i class="fas fa-sign-out-alt text-primary dark:text-blue-400 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                            <span class="text-gray-900 dark:text-white">Logout</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-primary dark:text-white">My Appointments</h2>
                        <p class="text-gray-600 dark:text-gray-400">View and manage your appointments</p>
                    </div>
                    <a href="appointment.php" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Book New Appointment</span>
                    </a>
                </div>
                
                <?php if (empty($appointments)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">No appointments scheduled</p>
                        <a href="appointment.php" class="inline-block bg-primary text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                            Book Your First Appointment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($appointments as $appointment): ?>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-primary dark:text-gray-200"><?php echo htmlspecialchars($appointment['lawyer_name']); ?></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($appointment['specialization']); ?></p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        <?php 
                                        switch($appointment['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                                break;
                                            case 'confirmed':
                                                echo 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                        }
                                        ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                    </p>
                                    <?php if ($appointment['notes']): ?>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-sticky-note mr-2"></i>
                                            <?php echo htmlspecialchars($appointment['notes']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($appointment['status'] === 'pending'): ?>
                                        <form method="POST" action="appointment.php" class="mt-4">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <input type="hidden" name="update_status" value="cancelled">
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                                <i class="fas fa-times"></i>
                                                <span>Cancel Appointment</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-sign-out-alt text-4xl text-primary dark:text-blue-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Logout Confirmation</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to logout?</p>
                <div class="flex justify-center space-x-4">
                    <button onclick="hideLogoutModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <a href="logout.php" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

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

        // Logout Modal Functions
        function showLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
            document.getElementById('logoutModal').classList.add('flex');
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
            document.getElementById('logoutModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('logoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
        });
    </script>
</body>
</html> 