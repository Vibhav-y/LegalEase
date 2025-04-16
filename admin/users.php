<?php
session_start();
require_once __DIR__ . '/../database/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Alogin.php');
    exit();
}

// Initialize variables
$users = [];
$totalUsers = 0;
$activeUsers = 0;
$pendingUsers = 0;
$newUsersThisMonth = 0;
$growthRate = 0;
$error = null;

// Handle user approval
if (isset($_POST['approve_user'])) {
    $userId = $_POST['user_id'];
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$userId]);
        header("Location: users.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Error approving user: " . $e->getMessage();
    }
}

// Get all users
try {
    $db = getDBConnection();
    $stmt = $db->query("
        SELECT id, name, email, age, gender, created_at, is_approved 
        FROM users
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get user statistics
    $totalUsers = count($users);
    $activeUsers = count(array_filter($users, function($user) { return $user['is_approved'] == 1; }));
    $pendingUsers = count(array_filter($users, function($user) { return $user['is_approved'] == 0; }));
    
    // Get new users this month
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));
    
    $newUsersThisMonth = count(array_filter($users, function($user) use ($currentMonth) {
        return date('Y-m', strtotime($user['created_at'])) === $currentMonth;
    }));

    $lastMonthUsers = count(array_filter($users, function($user) use ($lastMonth) {
        return date('Y-m', strtotime($user['created_at'])) === $lastMonth;
    }));
    
    $growthRate = $lastMonthUsers > 0 ? (($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100 : 0;

    // Get gender distribution
    $genderStats = array_count_values(array_column($users, 'gender'));
    $genderData = json_encode([
        'labels' => array_keys($genderStats),
        'data' => array_values($genderStats),
        'colors' => ['#3B82F6', '#EC4899', '#10B981']
    ]);

    // Get age distribution
    $ageGroups = [
        'Under 18' => 0,
        '18-25' => 0,
        '26-35' => 0,
        '36-45' => 0,
        '46-60' => 0,
        'Over 60' => 0
    ];
    
    foreach ($users as $user) {
        if (!empty($user['age'])) {
            $age = intval($user['age']);
            if ($age < 18) $ageGroups['Under 18']++;
            elseif ($age <= 25) $ageGroups['18-25']++;
            elseif ($age <= 35) $ageGroups['26-35']++;
            elseif ($age <= 45) $ageGroups['36-45']++;
            elseif ($age <= 60) $ageGroups['46-60']++;
            else $ageGroups['Over 60']++;
        }
    }
    $ageData = json_encode([
        'labels' => array_keys($ageGroups),
        'data' => array_values($ageGroups)
    ]);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    // Initialize empty arrays for charts
    $genderData = json_encode(['labels' => [], 'data' => [], 'colors' => []]);
    $ageData = json_encode(['labels' => [], 'data' => []]);
}
?>

<!DOCTYPE html>
<html lang="en" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - LegalEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
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
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            height: 300px;
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
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-primary dark:text-gray-200">Manage Users</h1>
                    <p class="text-gray-600 dark:text-gray-400">View and manage user accounts</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 animate-fade-in" role="alert">
                    <span class="block sm:inline">User approved successfully!</span>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 dark:text-gray-400">Total Users</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $totalUsers; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <i class="fas fa-user-check text-green-600 dark:text-green-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 dark:text-gray-400">Active Users</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $activeUsers; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                            <i class="fas fa-user-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 dark:text-gray-400">Pending Users</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $pendingUsers; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 dark:text-gray-400">New Users (This Month)</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo $newUsersThisMonth; ?></h3>
                            <p class="text-sm <?php echo $growthRate >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                                <?php echo $growthRate >= 0 ? '↑' : '↓'; ?> <?php echo number_format(abs($growthRate), 1); ?>%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Activity Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent User Activity</h3>
                <div class="space-y-4">
                    <?php 
                    $recentUsers = array_slice($users, 0, 5);
                    foreach ($recentUsers as $user):
                    ?>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-white font-semibold"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Joined <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                        <div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $user['is_approved'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; ?>">
                                <?php echo $user['is_approved'] ? 'Active' : 'Pending'; ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gender Distribution</h3>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Age Distribution</h3>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <?php if (empty($users)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">No users found.</p>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registration Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo !empty($user['age']) ? htmlspecialchars($user['age']) : 'N/A'; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : 'N/A'; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $user['is_approved'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; ?>">
                                                <?php echo $user['is_approved'] ? 'Active' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex space-x-2">
                                                <a href="view_user.php?id=<?php echo $user['id']; ?>" class="text-primary hover:text-blue-800 dark:text-blue-400">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if (!$user['is_approved']): ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" name="approve_user" class="text-green-600 hover:text-green-800 dark:text-green-400">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
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
            updateCharts();
        });

        let genderChart = null;
        let ageChart = null;

        function updateCharts() {
            const isDarkMode = document.documentElement.classList.contains('dark');
            const textColor = isDarkMode ? '#fff' : '#000';
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

            // Update Gender Chart
            if (genderChart) {
                genderChart.destroy();
            }
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            const genderData = <?php echo $genderData; ?>;
            genderChart = new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: genderData.labels,
                    datasets: [{
                        data: genderData.data,
                        backgroundColor: genderData.colors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });

            // Update Age Chart
            if (ageChart) {
                ageChart.destroy();
            }
            const ageCtx = document.getElementById('ageChart').getContext('2d');
            const ageData = <?php echo $ageData; ?>;
            ageChart = new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: ageData.labels,
                    datasets: [{
                        label: 'Users',
                        data: ageData.data,
                        backgroundColor: '#3B82F6',
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Initialize charts when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateCharts();
        });
    </script>
</body>
</html> 