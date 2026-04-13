<?php
require_once '../config.php';
require_once '../auth.php';

requireLogin();
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Stats
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'completed' => 0
];

$stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM tasks WHERE user_id = ? GROUP BY status");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

foreach ($results as $row) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

// Recent tasks
$recentStmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC LIMIT 5");
$recentStmt->execute([$user_id]);
$recent_tasks = $recentStmt->fetchAll();

// Unread notifications count
$notifStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
$notifStmt->execute([$user_id]);
$unread_notifs = $notifStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - PAKKAdesK</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-layer-group"></i> PAKKAdesK
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="my_tasks.php" class="nav-item"><i class="fa-solid fa-list-check"></i> My Tasks</a>
                <a href="notifications.php" class="nav-item">
                    <i class="fa-solid fa-bell"></i> Notifications
                    <?php if ($unread_notifs > 0): ?>
                        <span style="background: var(--danger); color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: auto;"><?php echo $unread_notifs; ?></span>
                    <?php endif; ?>
                </a>
                <a href="profile.php" class="nav-item"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="../logout.php" class="nav-item" style="margin-top: auto; color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="user-dropdown">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                </div>
            </header>

            <div class="content-wrapper">
                <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

                <div class="stats-grid animate-fade-in">
                    <div class="stat-card" style="border-left: 4px solid var(--primary);">
                        <div class="stat-title">My Total Tasks</div>
                        <div class="stat-value"><?php echo $stats['total']; ?></div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid var(--warning);">
                        <div class="stat-title">Pending</div>
                        <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #3B82F6;">
                        <div class="stat-title">In Progress</div>
                        <div class="stat-value"><?php echo $stats['in_progress']; ?></div>
                    </div>
                    <div class="stat-card" style="border-left: 4px solid #10B981;">
                        <div class="stat-title">Completed</div>
                        <div class="stat-value"><?php echo $stats['completed']; ?></div>
                    </div>
                </div>

                <div class="table-wrapper animate-fade-in" style="animation-delay: 0.1s;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.125rem; font-weight: 600;">Upcoming Deadlines</h2>
                        <a href="my_tasks.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">View All</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task Details</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_tasks) > 0): ?>
                                <?php foreach($recent_tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($task['title']); ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($task['description']); ?>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span></td>
                                        <td><span class="badge badge-<?php echo $task['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?></span></td>
                                        <td style="font-weight: 500; <?php echo (strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'color: var(--danger);' : ''; ?>">
                                            <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No upcoming tasks. You're all caught up!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
