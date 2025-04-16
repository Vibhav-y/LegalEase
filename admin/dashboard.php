<?php
session_start();
require_once __DIR__ . '/../database/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Alogin.php');
    exit();
}

// Get pending users
$pendingUsers = getPendingUsers();

// Handle user approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        if (approveUser($userId)) {
            sendApprovalEmail($userId);
            $success = "User approved successfully.";
        } else {
            $error = "Failed to approve user.";
        }
    } elseif ($action === 'reject') {
        if (rejectUser($userId)) {
            sendRejectionEmail($userId);
            $success = "User rejected successfully.";
        } else {
            $error = "Failed to reject user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LegalEase</title>
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
    <!-- Navigation Bar -->
    <nav class="bg-white/90 dark:bg-gray-800/90 shadow-lg fixed w-full z-50 backdrop-blur-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <?php include '../includes/logo.php'; ?>
                </div>
                <div class="flex items-center space-x-6">
                    <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200">
                        <span class="dark:hidden">🌙</span>
                        <span class="hidden dark:inline">☀️</span>
                    </button>
                    <a href="logout.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 pt-24 pb-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary dark:text-gray-200 mb-2">Admin Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage user approvals and system settings</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Quick Actions Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <a href="lawyers.php" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg">
                            <i class="fas fa-gavel text-2xl text-primary dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-primary dark:text-gray-200">Manage Lawyers</h3>
                            <p class="text-gray-600 dark:text-gray-400">View and manage lawyer accounts</p>
                        </div>
                    </div>
                </a>

                <a href="users.php" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl text-primary dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-primary dark:text-gray-200">Manage Users</h3>
                            <p class="text-gray-600 dark:text-gray-400">View and manage user accounts</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Pending Users Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-primary dark:text-gray-200 mb-4">Pending User Approvals</h2>
                
                <?php if (empty($pendingUsers)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">No pending user approvals.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Age</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registration Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($pendingUsers as $user): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['age']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['gender']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <?php echo isset($user['is_approved']) && $user['is_approved'] ? 'Active' : 'Inactive'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <form method="POST" action="" class="flex space-x-2">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="action" value="approve" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md transition-colors duration-200">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button type="submit" name="action" value="reject" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition-colors duration-200">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Appointments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-primary dark:text-gray-200">Recent Appointments</h2>
                    <a href="appointments.php" class="text-primary hover:text-blue-800 dark:text-blue-400">View All</a>
                </div>
                <?php
                try {
                    $pdo = getDBConnection();
                    $stmt = $pdo->query("
                        SELECT 
                            a.id,
                            a.appointment_date,
                            a.duration,
                            a.status,
                            a.notes,
                            u.name as client_name,
                            l.name as lawyer_name
                        FROM appointments a
                        LEFT JOIN users u ON a.client_id = u.id
                        LEFT JOIN lawyers l ON a.lawyer_id = l.id
                        ORDER BY a.appointment_date DESC
                        LIMIT 5
                    ");
                    $recentAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
                ?>
                <?php if (empty($recentAppointments)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-check text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">No appointments found.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lawyer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($recentAppointments as $appointment): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <?php echo htmlspecialchars($appointment['client_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <?php echo htmlspecialchars($appointment['lawyer_name']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                            <?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php 
                                                switch($appointment['status']) {
                                                    case 'pending':
                                                        echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                                        break;
                                                    case 'approved':
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
    </script>
</body>
</html> 