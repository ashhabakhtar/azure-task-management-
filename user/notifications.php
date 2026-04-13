<?php
require_once '../config.php';
require_once '../auth.php';

requireLogin();
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark all as read when visiting this page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->execute([$user_id]);
    header("Location: notifications.php");
    exit();
}

$notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$notifications->execute([$user_id]);
$notifs = $notifications->fetchAll();

// Unread counts for sidebar
$notifStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
$notifStmt->execute([$user_id]);
$unread_notifs = $notifStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - PAKKAdesK</title>
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
                <a href="my_tasks.php" class="nav-item"><i class="fa-solid fa-list-check"></i> My Tasks</a>
                <a href="notifications.php" class="nav-item active">
                    <i class="fa-solid fa-bell"></i> Notifications
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h1 class="page-title" style="margin-bottom: 0;">Notifications</h1>
                    <?php if ($unread_notifs > 0): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="mark_read" value="1">
                            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-check-double mr-2" style="margin-right: 8px;"></i> Mark all as read</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="stat-card animate-fade-in" style="padding: 0; max-width: 800px;">
                    <?php if (count($notifs) > 0): ?>
                        <div style="display: flex; flex-direction: column;">
                            <?php foreach($notifs as $index => $notif): ?>
                                <div style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.5rem; border-bottom: <?php echo $index === count($notifs)-1 ? 'none' : '1px solid var(--border)'; ?>; background: <?php echo $notif['is_read'] ? 'var(--surface)' : 'rgba(79, 70, 229, 0.03)'; ?>;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $notif['is_read'] ? 'var(--background)' : '#DBEAFE'; ?>; color: <?php echo $notif['is_read'] ? 'var(--text-muted)' : 'var(--primary)'; ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fa-solid fa-bell"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: <?php echo $notif['is_read'] ? '500' : '600'; ?>; color: var(--text-main); margin-bottom: 0.25rem;">
                                            <?php echo htmlspecialchars($notif['message']); ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?php echo date('M d, Y g:i A', strtotime($notif['created_at'])); ?>
                                        </div>
                                    </div>
                                    <?php if (!$notif['is_read']): ?>
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); margin-top: 0.5rem;"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-bell-slash" style="font-size: 2rem; margin-bottom: 1rem; color: var(--border);"></i>
                            <p>You don't have any notifications yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
