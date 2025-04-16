<?php
session_start();
require_once __DIR__ . '/../database/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Alogin.php');
    exit();
}

// Get all lawyers
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM lawyers ORDER BY created_at DESC");
    $lawyers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Get lawyer appointments if viewing a specific lawyer
$appointments = [];
if (isset($_GET['id'])) {
    $appointments = getLawyerAppointments($_GET['id']);
}

// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointmentId = $_POST['appointment_id'];
    $status = $_POST['status'];
    $lawyerId = $_GET['id'];
    
    if (updateAppointmentStatus($appointmentId, $lawyerId, $status)) {
        $success = "Appointment status updated successfully.";
        // Refresh appointments
        $appointments = getLawyerAppointments($lawyerId);
    } else {
        $error = "Failed to update appointment status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lawyers - LegalEase</title>
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
                    <a href="dashboard.php" class="nav-link text-primary hover:text-blue-800 dark:text-blue-400">Dashboard</a>
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
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-primary to-blue-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Manage Lawyers</h1>
                        <p class="text-blue-100 mt-2">View and manage lawyer accounts</p>
                    </div>
                    <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-user-tie text-4xl"></i>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <?php if (empty($lawyers)): ?>
                    <div class="text-center py-12">
                        <div class="bg-gray-100 dark:bg-gray-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">No lawyers found.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Specialization</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Experience</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($lawyers as $lawyer): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white shadow-lg">
                                                        <span class="text-lg font-bold"><?php echo strtoupper(substr($lawyer['name'], 0, 1)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($lawyer['name']); ?></div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo $lawyer['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                <div class="text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($lawyer['email']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mr-2">
                                                    <i class="fas fa-gavel text-indigo-600 dark:text-indigo-300"></i>
                                                </div>
                                                <div class="text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($lawyer['specialization']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="relative w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mr-2">
                                                    <?php
                                                    $expYears = $lawyer['experience_years'] ?? 0;
                                                    $expPercentage = min(($expYears / 30) * 100, 100);
                                                    $expColor = $expYears < 5 ? 'bg-yellow-500' : ($expYears < 15 ? 'bg-green-500' : 'bg-blue-500');
                                                    ?>
                                                    <div class="absolute h-full <?php echo $expColor; ?> rounded-full" style="width: <?php echo $expPercentage; ?>%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                                    <?php echo $expYears; ?> yrs
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $lawyer['status'] === 'active' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; ?>">
                                                <i class="fas <?php echo $lawyer['status'] === 'active' ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                                <?php echo ucfirst($lawyer['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="delete_lawyer.php?id=<?php echo $lawyer['id']; ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-red-300 rounded-full text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 dark:border-red-700 transition-colors duration-200"
                                               onclick="return confirm('Are you sure you want to delete this lawyer?')">
                                                <i class="fas fa-trash mr-1"></i>
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['id']) && !empty($appointments)): ?>
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-primary dark:text-gray-200 mb-4">Appointments</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                <?php echo htmlspecialchars($appointment['client_name']); ?>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($appointment['client_email']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                <?php echo date('M d, Y h:i A', strtotime($appointment['appointment_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                <?php echo $appointment['duration']; ?> minutes
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
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                                <?php echo htmlspecialchars($appointment['notes']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form method="POST" class="flex space-x-2">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                    <select name="status" class="text-sm rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                                        <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="approved" <?php echo $appointment['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="text-primary hover:text-blue-800 dark:text-blue-400">
                                                        <i class="fas fa-save"></i> Update
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });

        // Set initial theme
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html> 