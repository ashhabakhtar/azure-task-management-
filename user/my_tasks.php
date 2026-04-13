<?php
require_once '../config.php';
require_once '../auth.php';

requireLogin();
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['status'])) {
    $task_id = (int)$_POST['task_id'];
    $status = $_POST['status'];
    
    // Ensure task belongs to user
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$status, $task_id, $user_id]);
        $message = 'Task status updated successfully.';
        $messageType = 'success';
    } catch(PDOException $e) {
        $message = 'Error updating status.';
        $messageType = 'danger';
    }
}

$tasks = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY FIELD(status, 'pending', 'in_progress', 'completed'), due_date ASC");
$tasks->execute([$user_id]);
$my_tasks = $tasks->fetchAll();

// Unread notifications count for sidebar
$notifStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
$notifStmt->execute([$user_id]);
$unread_notifs = $notifStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - PAKKAdesK</title>
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
                <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                <a href="my_tasks.php" class="nav-item active"><i class="fa-solid fa-list-check"></i> My Tasks</a>
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
                <h1 class="page-title">My Tasks</h1>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> animate-fade-in"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="table-wrapper animate-fade-in">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Task Details</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th style="text-align: right;">Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($my_tasks) > 0): ?>
                                <?php foreach($my_tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem; <?php echo $task['status'] === 'completed' ? 'text-decoration: line-through; opacity: 0.6;' : ''; ?>">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 400px;">
                                                <?php echo htmlspecialchars($task['description']); ?>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-<?php echo $task['priority']; ?>"><?php echo ucfirst($task['priority']); ?></span></td>
                                        <td style="font-weight: 500; <?php echo (strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'color: var(--danger);' : ''; ?>">
                                            <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <select name="status" class="form-control" style="width: auto; padding: 0.4rem; font-size: 0.8rem; background: var(--surface);" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem;">You have no tasks assigned to you.</td>
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
