<?php
session_start();
require_once 'includes/functions.php';
require_once __DIR__ . '/database/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $clientId = $_SESSION['user_id'];

    if ($appointmentId) {
        if (cancelAppointment($appointmentId, $clientId)) {
            $_SESSION['success_message'] = "Appointment cancelled successfully";
        } else {
            $_SESSION['error_message'] = "Failed to cancel appointment";
        }
    } else {
        $_SESSION['error_message'] = "Invalid appointment ID";
    }

    header('Location: Userdashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate inputs
    $lawyerId = filter_input(INPUT_POST, 'lawyer_id', FILTER_VALIDATE_INT);
    $appointmentDate = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_STRING);
    $appointmentTime = filter_input(INPUT_POST, 'appointment_time', FILTER_SANITIZE_STRING);
    $purpose = filter_input(INPUT_POST, 'purpose', FILTER_SANITIZE_STRING);
    
    if (!$lawyerId) {
        $errors[] = "Please select a lawyer";
    }
    if (!$appointmentDate) {
        $errors[] = "Please select an appointment date";
    }
    if (!$appointmentTime) {
        $errors[] = "Please select an appointment time";
    }
    if (!$purpose) {
        $errors[] = "Please provide the purpose of your appointment";
    }
    
    if (empty($errors)) {
        // Combine date and time
        $datetime = $appointmentDate . ' ' . $appointmentTime;
        
        // Create appointment
        $success = createNewAppointment(
            $_SESSION['user_id'],
            $lawyerId,
            $datetime,
            60, // Default duration of 60 minutes
            $purpose
        );
        
        if ($success) {
            $_SESSION['success_message'] = "Appointment booked successfully!";
            echo "<script>
                alert('Appointment booked successfully!');
                window.location.href = 'Userdashboard.php';
            </script>";
            exit();
        } else {
            $errors[] = "Failed to book appointment. Please try again.";
        }
    }
    
    // If there are errors, store them in session
    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
    }

    // Handle appointment status update
    if (isset($_POST['update_status']) && isset($_POST['appointment_id'])) {
        $appointmentId = $_POST['appointment_id'];
        $status = $_POST['update_status'];
        $userId = $_SESSION['user_id'];
        
        try {
            $pdo = getDBConnection();
            
            // Verify that the appointment belongs to the user and is pending
            $stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ? AND client_id = ? AND status = 'pending'");
            $stmt->execute([$appointmentId, $userId]);
            
            if ($stmt->rowCount() === 0) {
                $_SESSION['error'] = "Appointment not found or cannot be updated.";
            } else {
                // Update appointment status
                if (updateAppointmentStatus($appointmentId, null, $status)) {
                    $_SESSION['success'] = "Appointment " . ($status === 'confirmed' ? 'confirmed' : 'cancelled') . " successfully.";
                } else {
                    $_SESSION['error'] = "Failed to update appointment status. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to update appointment status. Please try again.";
            error_log("Appointment status update error: " . $e->getMessage());
        }
        
        header('Location: Userdashboard.php');
        exit();
    }
}

// Get service ID from URL
$serviceId = isset($_GET['service']) ? (int)$_GET['service'] : 0;

// Get available lawyers
$lawyers = getAvailableLawyers();

// Get user information
$user = getAppointmentUser();

// Initialize variables
$error_message = '';
$success_message = '';
$selected_lawyer = null;

// Add these functions at the top of the file
function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

function isValidBusinessHour($time) {
    $hour = (int)date('H', strtotime($time));
    return ($hour >= 9 && $hour < 17);
}

// Get selected lawyer details if lawyer_id is set
if (isset($_POST['lawyer_id'])) {
    foreach ($lawyers as $lawyer) {
        if ($lawyer['id'] == $_POST['lawyer_id']) {
            $selected_lawyer = $lawyer;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - LegalEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-up {
            animation: slide-up 0.5s ease-out;
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 2s infinite;
        }
        .success-checkmark {
            width: 24px;
            height: 24px;
            background-color: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .check-icon {
            color: white;
            font-size: 14px;
        }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #1D4E89;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #153b6b;
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        .validation-message {
            position: relative;
            padding: 1rem;
            margin-top: 0.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slide-in 0.3s ease-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .validation-message.error {
            background: linear-gradient(135deg, #fff5f5 0%, #fff1f1 100%);
            border-left: 4px solid #ef4444;
        }

        .validation-message.info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #3b82f6;
        }

        .validation-icon {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .validation-icon.error {
            background: #fee2e2;
            color: #ef4444;
        }

        .validation-icon.info {
            background: #dbeafe;
            color: #3b82f6;
        }

        @keyframes slide-in {
            from {
                transform: translateX(-10px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        .time-slot-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            border: 1px dashed #cbd5e1;
        }

        .time-slot-info i {
            color: #3b82f6;
        }
    </style>
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
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-primary dark:text-gray-200 mb-4">Book Your Appointment</h1>
                <p class="text-gray-600 dark:text-gray-400">Schedule a consultation with our expert lawyers</p>
            </div>

            <?php if (isset($_SESSION['error_messages'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                <?php 
                                foreach ($_SESSION['error_messages'] as $error) {
                                    echo htmlspecialchars($error) . "<br>";
                                }
                                unset($_SESSION['error_messages']);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg animate-slide-up" role="alert">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="success-checkmark">
                                <i class="fas fa-check check-icon"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-lg font-medium"><?php echo htmlspecialchars($success_message); ?></p>
                            <p class="text-sm mt-1">We've sent a confirmation to your email</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Appointment Section -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Side - Form -->
                    <div class="lg:col-span-2">
                        <form id="appointmentForm" action="appointment.php" method="POST" class="space-y-6">
                            <div class="space-y-6">
                                <div class="relative">
                                    <label for="lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-user-tie mr-2 text-primary"></i>Select Lawyer
                                    </label>
                                    <select name="lawyer_id" id="lawyer_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-gray-200 transition-all duration-200 appearance-none"
                                            onchange="updateLawyerInfo(this.value)">
                                        <option value="" disabled selected>Select a lawyer</option>
                                        <?php foreach ($lawyers as $lawyer): ?>
                                            <option value="<?php echo $lawyer['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($lawyer['name']); ?>"
                                                    data-specialization="<?php echo htmlspecialchars($lawyer['specialization']); ?>"
                                                    data-experience="<?php echo $lawyer['experience_years']; ?>">
                                                <?php echo htmlspecialchars($lawyer['name']); ?> - 
                                                <?php echo htmlspecialchars($lawyer['specialization']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="absolute right-3 top-10 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="relative">
                                        <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-calendar-alt mr-2 text-primary"></i>Appointment Date
                                        </label>
                                        <input type="date" name="appointment_date" id="appointment_date" required
                                               min="<?php echo date('Y-m-d'); ?>"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-gray-200 transition-all duration-200"
                                               onchange="validateDate(this.value)">
                                        <div id="date-validation" class="validation-message hidden">
                                            <div class="validation-icon">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div class="text-sm font-medium"></div>
                                        </div>
                                    </div>

                                    <div class="relative">
                                        <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-clock mr-2 text-primary"></i>Appointment Time
                                        </label>
                                        <input type="time" name="appointment_time" id="appointment_time" required
                                               min="09:00" max="17:00"
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-gray-200 transition-all duration-200"
                                               onchange="validateTime(this.value)">
                                        <div id="time-validation" class="validation-message hidden">
                                            <div class="validation-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="text-sm font-medium"></div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-comment-alt mr-2 text-primary"></i>Purpose of Appointment
                                    </label>
                                    <textarea name="purpose" id="purpose" rows="4" required
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:text-gray-200 transition-all duration-200"
                                              placeholder="Please describe the purpose of your appointment..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right Side - Lawyer Info and Button -->
                    <div class="lg:col-span-1 flex flex-col justify-between">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl border border-gray-200 dark:border-gray-600">
                            <div id="lawyer-info" class="hidden transform transition-all duration-300 ease-in-out">
                                <div class="text-center mb-6">
                                    <div class="lawyer-avatar w-24 h-24 rounded-full bg-primary flex items-center justify-center shadow-lg mx-auto mb-4">
                                        <i class="fas fa-user text-white text-4xl"></i>
                                    </div>
                                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2" id="lawyer-name"></h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" id="lawyer-specialization"></p>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Rating</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white" id="lawyer-rating"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-briefcase text-primary text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Experience</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white" id="lawyer-experience"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-graduation-cap text-green-500 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Education</p>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white" id="lawyer-education">LLB, Harvard Law School</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Placeholder when no lawyer is selected -->
                            <div id="no-lawyer-selected" class="flex flex-col items-center justify-center h-full">
                                <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center mb-4">
                                    <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-center">Select a lawyer to view their profile</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2 text-center">Choose from our expert legal professionals</p>
                            </div>
                        </div>
                        <!-- Book Appointment Button -->
                        <div class="mt-6">
                            <button type="submit" form="appointmentForm"
                                    class="w-full bg-primary text-white px-8 py-3 rounded-lg hover:bg-blue-800 dark:hover:bg-secondary transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center justify-center space-x-2">
                                <i class="fas fa-calendar-check"></i>
                                <span>Book Appointment</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Guidelines Card -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 mb-6">
                    <i class="fas fa-info-circle text-2xl text-primary"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Appointment Guidelines</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-calendar-check text-green-500 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Available Days</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Monday to Friday</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-clock text-primary mt-1"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Business Hours</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">9:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-user-clock text-yellow-500 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Arrival Time</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Please arrive 5 minutes before your appointment</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-file-alt text-blue-500 mt-1"></i>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">Required Documents</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Bring all necessary documents for your consultation</p>
                            </div>
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

        // Enhanced Date and Time Validation
        function validateDate(date) {
            const dateInput = document.getElementById('appointment_date');
            const validationDiv = document.getElementById('date-validation');
            const selectedDate = new Date(date);
            const day = selectedDate.getDay();
            
            if (day === 0 || day === 6) { // Sunday (0) or Saturday (6)
                validationDiv.className = 'validation-message error';
                validationDiv.querySelector('.validation-icon').className = 'validation-icon error';
                validationDiv.querySelector('.text-sm').textContent = 'Weekends are not available for appointments';
                validationDiv.classList.remove('hidden');
                dateInput.classList.add('shake');
                setTimeout(() => dateInput.classList.remove('shake'), 500);
            } else {
                validationDiv.className = 'validation-message info';
                validationDiv.querySelector('.validation-icon').className = 'validation-icon info';
                validationDiv.querySelector('.text-sm').textContent = 'Selected date is available';
                validationDiv.classList.remove('hidden');
            }
        }

        function validateTime(time) {
            const timeInput = document.getElementById('appointment_time');
            const validationDiv = document.getElementById('time-validation');
            const hour = parseInt(time.split(':')[0]);
            
            if (hour < 9 || hour >= 17) {
                validationDiv.className = 'validation-message error';
                validationDiv.querySelector('.validation-icon').className = 'validation-icon error';
                validationDiv.querySelector('.text-sm').textContent = 'Appointments are only available between 9:00 AM and 5:00 PM';
                validationDiv.classList.remove('hidden');
                timeInput.classList.add('shake');
                setTimeout(() => timeInput.classList.remove('shake'), 500);
            } else {
                validationDiv.className = 'validation-message info';
                validationDiv.querySelector('.validation-icon').className = 'validation-icon info';
                validationDiv.querySelector('.text-sm').textContent = 'Selected time is available';
                validationDiv.classList.remove('hidden');
            }
        }

        // Enhanced lawyer info update
        function updateLawyerInfo(lawyerId) {
            const lawyerInfo = document.getElementById('lawyer-info');
            const noLawyerSelected = document.getElementById('no-lawyer-selected');
            const select = document.getElementById('lawyer_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (lawyerId) {
                document.getElementById('lawyer-name').textContent = selectedOption.dataset.name;
                document.getElementById('lawyer-specialization').textContent = selectedOption.dataset.specialization;
                document.getElementById('lawyer-rating').textContent = selectedOption.dataset.rating + ' / 5.0';
                document.getElementById('lawyer-experience').textContent = selectedOption.dataset.experience + ' years experience';
                
                lawyerInfo.classList.remove('hidden');
                noLawyerSelected.classList.add('hidden');
                lawyerInfo.classList.add('animate-fade-in');
            } else {
                lawyerInfo.classList.add('hidden');
                noLawyerSelected.classList.remove('hidden');
                lawyerInfo.classList.remove('animate-fade-in');
            }
        }

        // Initialize the view
        document.addEventListener('DOMContentLoaded', function() {
            const lawyerSelect = document.getElementById('lawyer_id');
            if (lawyerSelect.value) {
                updateLawyerInfo(lawyerSelect.value);
            } else {
                document.getElementById('no-lawyer-selected').classList.remove('hidden');
            }
        });

        // Add smooth scrolling for form elements
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('focus', () => {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        // Update the form submission to prevent default behavior
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            const lawyerId = document.getElementById('lawyer_id').value;
            const appointmentDate = document.getElementById('appointment_date').value;
            const appointmentTime = document.getElementById('appointment_time').value;
            const purpose = document.getElementById('purpose').value;
            
            if (!lawyerId || !appointmentDate || !appointmentTime || !purpose) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Submit form
            this.submit();
        });
    </script>
</body>
</html> 