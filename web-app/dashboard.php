<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total records
$total_sql = "SELECT COUNT(*) as total FROM data WHERE user_id = $user_id";
$total_result = $conn->query($total_sql);
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// Get records with pagination
$sql = "SELECT id, drowsiness_percentage, detection_time, detection_date 
        FROM data 
        WHERE user_id = $user_id 
        ORDER BY detection_date DESC, detection_time DESC 
        LIMIT $offset, $limit";
$result = $conn->query($sql);

// Get statistics
$stats_sql = "SELECT 
                COUNT(*) as total_count,
                AVG(drowsiness_percentage) as avg_percentage,
                MAX(drowsiness_percentage) as max_percentage
              FROM data 
              WHERE user_id = $user_id";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get current status (latest detection)
$status_sql = "SELECT drowsiness_percentage, detection_time, detection_date 
               FROM data 
               WHERE user_id = $user_id 
               ORDER BY detection_date DESC, detection_time DESC 
               LIMIT 1";
$status_result = $conn->query($status_sql);
$current_status = $status_result->fetch_assoc();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_sql = "DELETE FROM data WHERE id = $delete_id AND user_id = $user_id";
    if ($conn->query($delete_sql)) {
        header("Location: dashboard.php?page=$page&deleted=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Drowsiness Detection System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
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

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-900 to-purple-900 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">😴 Drowsiness Detection Logs</h1>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-200">Welcome, <span class="font-semibold text-white"><?php echo htmlspecialchars($user_name); ?></span></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 fade-in">
        <!-- Section 1: Current Status -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📊 Current Drowsiness Status</h2>
            <!-- In dashboard.php, update the status display section -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-8 
    <?php
    if ($current_status && $current_status['drowsiness_percentage'] > 70) echo 'border-red-500';
    elseif ($current_status && $current_status['drowsiness_percentage'] > 30) echo 'border-yellow-500';
    else echo 'border-green-500';
    ?>">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 mb-2">Latest Detection</p>
                        <p class="text-4xl font-bold 
                <?php
                if ($current_status && $current_status['drowsiness_percentage'] > 70) echo 'text-red-600';
                elseif ($current_status && $current_status['drowsiness_percentage'] > 30) echo 'text-yellow-600';
                else echo 'text-green-600';
                ?>">
                            <?php echo $current_status ? number_format($current_status['drowsiness_percentage'], 1) : '0.0'; ?>%
                        </p>
                        <p class="text-gray-500 mt-2">
                            <?php
                            if ($current_status) {
                                echo date('h:i A', strtotime($current_status['detection_time'])) . ' | ' . date('d-m-Y', strtotime($current_status['detection_date']));
                            } else {
                                echo 'No data yet';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-6xl">
                            <?php
                            if ($current_status && $current_status['drowsiness_percentage'] > 70) echo "⚠️🚗";
                            elseif ($current_status && $current_status['drowsiness_percentage'] > 30) echo "😴⚠️";
                            else echo "😊✅";
                            ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <?php
                            if ($current_status && $current_status['drowsiness_percentage'] > 70) echo "Critical! Take Break";
                            elseif ($current_status && $current_status['drowsiness_percentage'] > 30) echo "Mild Drowsiness";
                            else echo "Alert & Safe";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Statistics Cards -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm uppercase tracking-wide">Total Drowsiness Events</p>
                        <p class="text-4xl font-bold mt-2"><?php echo number_format($stats['total_count'] ?? 0); ?></p>
                    </div>
                    <div class="text-5xl">📊</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm uppercase tracking-wide">Average Drowsiness Percentage</p>
                        <p class="text-4xl font-bold mt-2"><?php echo number_format($stats['avg_percentage'] ?? 0, 1); ?>%</p>
                    </div>
                    <div class="text-5xl">📈</div>
                </div>
            </div>
        </div>

        <!-- Section 2: Records Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">📋 Drowsiness Detection Records</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drowsiness Detection</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $counter = $offset + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $counter++; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $percentage = $row['drowsiness_percentage'];
                                        $badge_color = $percentage > 70 ? 'red' : ($percentage > 40 ? 'yellow' : 'green');
                                        $badge_text = $percentage > 70 ? 'Critical Alert' : ($percentage > 40 ? 'Mild Drowsiness' : 'Low Risk');
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $badge_color; ?>-100 text-<?php echo $badge_color; ?>-800">
                                            <?php echo number_format($percentage, 1); ?>% - <?php echo $badge_text; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('h:i A', strtotime($row['detection_time'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('d-m-Y', strtotime($row['detection_date'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="?delete=<?php echo $row['id']; ?>&page=<?php echo $page; ?>"
                                            onclick="return confirm('Are you sure you want to delete this record?')"
                                            class="text-red-600 hover:text-red-900 transition duration-200">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No records found. Start monitoring to see data here!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to
                            <span class="font-medium"><?php echo min($offset + $limit, $total_records); ?></span> of
                            <span class="font-medium"><?php echo $total_records; ?></span> results
                        </p>
                        <div class="flex space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">Previous</a>
                            <?php endif; ?>
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50">Next</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>