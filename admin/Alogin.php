<?php
session_start();
require_once __DIR__ . '/../database/database.php';
require_once '../includes/functions.php';

$error = '';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'All fields are required';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT id, password FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LegalEase</title>
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
        }

        .social-login-btn:hover {
            transform: translateY(-2px);
        }

        .social-login-btn.google:hover {
            background-color: #DB4437;
            color: white;
        }

        .social-login-btn.facebook:hover {
            background-color: #4267B2;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white/90 dark:bg-gray-800/90 shadow-lg fixed w-full z-50 backdrop-blur-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <?php include '../includes/logo.php'; ?>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="../index.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Back to Site</a>
                    <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200">
                        <span class="dark:hidden">🌙</span>
                        <span class="hidden dark:inline">☀️</span>
                    </button>
                    <a href="Aregister.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 pt-24 pb-8">
        <div class="max-w-md mx-auto login-container rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-2">Admin Login</h1>
                <p class="text-gray-600 dark:text-gray-400">Sign in to your admin account</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
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

                <button type="submit"
                    class="login-btn w-full bg-primary text-white py-3 px-4 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an admin account?
                    <a href="Aregister.php" class="text-primary hover:text-blue-800 dark:text-blue-400 font-medium">Register</a>
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

        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });
    </script>
</body>
</html> 