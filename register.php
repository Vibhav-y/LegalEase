<?php
session_start();
require_once 'includes/functions.php';

// Check if user is already logged in
if (isAuthenticated()) {
    header('Location: Userdashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $age = intval($_POST['age'] ?? 0);
    $gender = trim($_POST['gender'] ?? '');

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($age) || empty($gender)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        $result = registerUser($name, $email, $password, $age, $gender);
        if ($result['success']) {
            $_SESSION['registration_success'] = $result['message'];
            header('Location: login.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LegalEase</title>
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
    <style>
        .register-container {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .dark .register-container {
            background: linear-gradient(135deg, rgba(31,41,55,0.1) 0%, rgba(31,41,55,0) 100%);
            border-color: rgba(255,255,255,0.1);
        }

        .input-group {
            position: relative;
            transition: all 0.3s ease;
        }

        .input-group input, .input-group select {
            transition: all 0.3s ease;
            height: 3.5rem;
            padding-left: 3rem;
            font-size: 1.1rem;
        }

        .input-group:focus-within {
            transform: translateY(-2px);
        }

        .input-group:focus-within input, .input-group:focus-within select {
            border-color: #1D4E89;
            box-shadow: 0 0 0 2px rgba(29,78,137,0.2);
        }

        .dark .input-group:focus-within input, .dark .input-group:focus-within select {
            border-color: #D4AF37;
            box-shadow: 0 0 0 2px rgba(212,175,55,0.2);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .input-group:focus-within .input-icon {
            color: #1D4E89;
        }

        .dark .input-group:focus-within .input-icon {
            color: #D4AF37;
        }

        .register-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .password-strength {
            height: 4px;
            transition: all 0.3s ease;
        }

        .password-strength.weak {
            background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
            width: 33%;
        }

        .password-strength.medium {
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
            width: 66%;
        }

        .password-strength.strong {
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            width: 100%;
        }

        .terms-checkbox {
            transition: all 0.3s ease;
        }

        .terms-checkbox:checked {
            background-color: #1D4E89;
        }

        .dark .terms-checkbox:checked {
            background-color: #D4AF37;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mx-auto px-4 pt-24 pb-8">
        <div class="max-w-md mx-auto register-container rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-2">Create Account</h1>
                <p class="text-gray-600 dark:text-gray-400">Join LegalEase today</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <p class="font-medium"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && isset($_GET['pending'])): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <p class="font-medium">Registration Successful!</p>
                    <p class="text-sm">Your account is pending admin approval. You will receive an email once your account is approved.</p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="name" name="name" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter your full name">
                </div>

                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter your email">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Create a password">
                    <div class="password-strength mt-2 rounded-full"></div>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Confirm your password">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="input-group">
                        <i class="fas fa-birthday-cake input-icon"></i>
                        <input type="number" id="age" name="age" placeholder="Age" min="0" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="input-group">
                        <i class="fas fa-venus-mars input-icon"></i>
                        <select id="gender" name="gender" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                        class="terms-checkbox h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600">
                    <label for="terms" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        I agree to the <a href="#" class="text-primary hover:text-blue-800 dark:text-blue-400">Terms of Service</a> and <a href="#" class="text-primary hover:text-blue-800 dark:text-blue-400">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit"
                    class="register-btn w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a href="login.php" class="text-primary hover:text-blue-800 dark:text-blue-400 font-medium">Login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Theme handling
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Add animation to form elements
        document.querySelectorAll('.input-group').forEach((group, index) => {
            group.style.animationDelay = `${index * 0.1}s`;
            group.classList.add('animate-fade-in-up');
        });

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.querySelector('.password-strength');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthIndicator.className = 'password-strength mt-2 rounded-full';
            if (strength <= 1) {
                strengthIndicator.classList.add('weak');
            } else if (strength <= 2) {
                strengthIndicator.classList.add('medium');
            } else {
                strengthIndicator.classList.add('strong');
            }
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            });
        }
    </script>
</body>
</html> 