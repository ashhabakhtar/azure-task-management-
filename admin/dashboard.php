<?php
require_once '../config.php';
require_once '../auth.php';

requireAdmin();

// Fetch admin stats
$stats = [
    'total_users' => 0,
    'total_tasks' => 0,
    'pending_tasks' => 0,
    'completed_tasks' => 0
];

$stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$stats['total_tasks'] = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
$stats['pending_tasks'] = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'pending'")->fetchColumn();
$stats['completed_tasks'] = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'completed'")->fetchColumn();

// Fetch recent tasks
$recent_tasks = $pdo->query("
    SELECT t.id, t.title, t.status, t.priority, u.username as assigned_to 
    FROM tasks t 
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PAKKAdesK</title>
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
                <a href="users.php" class="nav-item"><i class="fa-solid fa-users"></i> Manage Users</a>
                <a href="create_task.php" class="nav-item"><i class="fa-solid fa-plus-circle"></i> Create Task</a>
                <a href="all_tasks.php" class="nav-item"><i class="fa-solid fa-list-check"></i> All Tasks</a>
                <a href="../logout.php" class="nav-item" style="margin-top: auto; color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="user-dropdown">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</span>
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                </div>
            </header>

            <div class="content-wrapper">
                <h1 class="page-title">Dashboard Overview</h1>

                <div class="stats-grid animate-fade-in">
                    <div class="stat-card" style="border-top: 4px solid var(--primary);">
                        <div class="stat-title">Total Users</div>
                        <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid var(--secondary);">
                        <div class="stat-title">Total Tasks</div>
                        <div class="stat-value"><?php echo $stats['total_tasks']; ?></div>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid var(--warning);">
                        <div class="stat-title">Pending Tasks</div>
                        <div class="stat-value"><?php echo $stats['pending_tasks']; ?></div>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid #10B981;">
                        <div class="stat-title">Completed Tasks</div>
                        <div class="stat-value"><?php echo $stats['completed_tasks']; ?></div>
                    </div>
                </div>

                <div class="table-wrapper animate-fade-in" style="animation-delay: 0.1s;">
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 1.125rem; font-weight: 600;">Recent Tasks</h2>
                        <a href="all_tasks.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">View All</a>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_tasks) > 0): ?>
                                <?php foreach($recent_tasks as $task): ?>
                                    <tr>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($task['title']); ?></td>
                                        <td><?php echo $task['assigned_to'] ? htmlspecialchars($task['assigned_to']) : '<span style="color:var(--text-muted)">Unassigned</span>'; ?></td>
                                        <td><span class="badge badge-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span></td>
                                        <td><span class="badge badge-<?php echo $task['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No tasks created yet.</td>
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
