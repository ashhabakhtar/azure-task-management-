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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $hashed, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user_id]);
        }
        $message = 'Profile updated successfully.';
        $messageType = 'success';
    } catch(PDOException $e) {
        $message = 'Error updating profile. Email might already exist.';
        $messageType = 'danger';
    }
}

$user = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$user->execute([$user_id]);
$userData = $user->fetch();

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
    <title>My Profile - PAKKAdesK</title>
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
                <a href="notifications.php" class="nav-item">
                    <i class="fa-solid fa-bell"></i> Notifications
                    <?php if ($unread_notifs > 0): ?>
                        <span style="background: var(--danger); color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: auto;"><?php echo $unread_notifs; ?></span>
                    <?php endif; ?>
                </a>
                <a href="profile.php" class="nav-item active"><i class="fa-solid fa-user"></i> Profile</a>
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
                <h1 class="page-title">My Profile</h1>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> animate-fade-in"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="stat-card animate-fade-in" style="max-width: 500px;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 600;">
                            <?php echo strtoupper(substr($userData['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700;"><?php echo htmlspecialchars($userData['username']); ?></h2>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">User Account</p>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['username']); ?>" disabled title="Username cannot be changed">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password">
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Update Profile</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
