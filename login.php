<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'includes/functions.php';

// Check if user is already logged in
if (isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];
            $_SESSION['created_at'] = $result['created_at'];
            
            // Check for redirect URL
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            } else {
                header('Location: Userdashboard.php');
            }
            exit();
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
    <title>Login - LegalEase</title>
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
        .login-container {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .dark .login-container {
            background: linear-gradient(135deg, rgba(31,41,55,0.1) 0%, rgba(31,41,55,0) 100%);
            border-color: rgba(255,255,255,0.1);
        }

        .input-group {
            position: relative;
            transition: all 0.3s ease;
        }

        .input-group input {
            transition: all 0.3s ease;
            height: 3.5rem;
            padding-left: 3rem;
            font-size: 1.1rem;
        }

        .input-group:focus-within {
            transform: translateY(-2px);
        }

        .input-group:focus-within input {
            border-color: #1D4E89;
            box-shadow: 0 0 0 2px rgba(29,78,137,0.2);
        }

        .dark .input-group:focus-within input {
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

        .login-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .social-login-btn {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .dark .social-login-btn {
            border-color: rgba(255,255,255,0.1);
        }

        .social-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .dark .social-login-btn:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mx-auto px-4 pt-24 pb-8">
        <div class="max-w-md mx-auto login-container rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-2">Welcome Back</h1>
                <p class="text-gray-600 dark:text-gray-400">Login to your LegalEase account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <p class="font-medium"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['registration_success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <p class="font-medium"><?php echo htmlspecialchars($_SESSION['registration_success']); ?></p>
                </div>
                <?php unset($_SESSION['registration_success']); ?>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && isset($_GET['pending'])): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <p class="font-medium">Registration Successful!</p>
                    <p class="text-sm">Your account is pending admin approval. You will receive an email once your account is approved.</p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter your email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter your password">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                    </div>
                    <a href="#" class="text-sm text-primary hover:text-blue-800 dark:text-blue-400">Forgot password?</a>
                </div>

                <button type="submit"
                    class="login-btn w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300">
                    Login
                </button>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">Or continue with</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" class="social-login-btn flex items-center justify-center space-x-2 py-2 px-4 rounded-lg">
                        <i class="fab fa-google text-red-600"></i>
                        <span>Google</span>
                    </button>
                    <button type="button" class="social-login-btn flex items-center justify-center space-x-2 py-2 px-4 rounded-lg">
                        <i class="fab fa-facebook text-blue-600"></i>
                        <span>Facebook</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <a href="register.php" class="text-primary hover:text-blue-800 dark:text-blue-400 font-medium">Sign up</a>
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

        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });

        // Add animation to form elements
        document.querySelectorAll('.input-group').forEach((group, index) => {
            group.style.animationDelay = `${index * 0.1}s`;
            group.classList.add('animate-fade-in-up');
        });
    </script>
</body>
</html> 