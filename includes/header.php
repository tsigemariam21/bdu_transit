<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'BDU Transit - Smart Campus Mobility'; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <?php echo $extra_head ?? ''; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar glass">
        <a href="index.php" class="logo">
            <i class="fas fa-bus-alt"></i> BDU Transit
        </a>
        <div class="nav-links">
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="schedules.php" class="<?php echo $current_page == 'schedules.php' ? 'active' : ''; ?>">Schedules</a>
            <a href="tracking.php" class="<?php echo $current_page == 'tracking.php' ? 'active' : ''; ?>">Live Tracking</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu" style="display: flex; align-items: center; gap: 1rem; margin-left: 1rem;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin/dashboard.php" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.8rem; background: var(--danger-color);">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>">Login</a>
                <a href="register.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    Join Now
                </a>
            <?php endif; ?>
        </div>
    </nav>
