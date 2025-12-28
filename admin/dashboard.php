<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once '../config/db_connect.php';

// Fetch quick stats
$stats = [
    'buses' => $pdo->query("SELECT COUNT(*) FROM buses")->fetchColumn(),
    'routes' => $pdo->query("SELECT COUNT(*) FROM routes")->fetchColumn(),
    'schedules' => $pdo->query("SELECT COUNT(*) FROM schedules")->fetchColumn(),
    'active_alerts' => $pdo->query("SELECT COUNT(*) FROM announcements WHERE is_active = 1")->fetchColumn()
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BDU Transit</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--bg-card);
            padding: 2.5rem 1.5rem;
            border-right: var(--glass-border);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 4rem 5%;
            background: var(--bg-main);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem 1.25rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: var(--radius-md);
            margin-bottom: 0.75rem;
            transition: var(--transition);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .nav-item.active {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .stat-card {
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: var(--glass-border);
            display: flex;
            align-items: center;
            gap: 2rem;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon {
            width: 4.5rem;
            height: 4.5rem;
            border-radius: var(--radius-lg);
            background: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .action-card {
            background: white;
            padding: 3rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: var(--glass-border);
            margin-top: 4rem;
        }
    </style>
</head>

<body style="background: var(--bg-main);">

    <!-- Sidebar -->
    <aside class="sidebar glass">
        <div class="logo" style="padding-left: 1.25rem; margin-bottom: 4rem; font-size: 1.5rem; font-family: 'Outfit';">
            <i class="fas fa-shield-alt"></i> BDU Admin
        </div>
        <nav>
            <a href="dashboard.php" class="nav-item active"><i class="fas fa-chart-pie" style="width: 24px;"></i> Overview</a>
            <a href="buses.php" class="nav-item"><i class="fas fa-bus" style="width: 24px;"></i> Manage Buses</a>
            <a href="routes.php" class="nav-item"><i class="fas fa-route" style="width: 24px;"></i> Routes & Stops</a>
            <a href="schedules.php" class="nav-item"><i class="fas fa-calendar-alt" style="width: 24px;"></i> Timetables</a>
            <a href="announcements.php" class="nav-item"><i class="fas fa-bullhorn" style="width: 24px;"></i> Alerts Center</a>
            
            <div style="margin-top: 4rem; padding: 0 1.25rem;">
                <div style="height: 1px; background: rgba(0,0,0,0.05); margin-bottom: 2rem;"></div>
                <a href="logout.php" class="nav-item" style="color: var(--danger-color); padding-left: 0;">
                    <i class="fas fa-sign-out-alt" style="width: 24px;"></i> Secure Logout
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 4rem;">
            <div>
                <span style="color: var(--primary-color); font-weight: 800; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.1em;">Control Center</span>
                <h2 style="font-family: 'Outfit'; font-size: 2.5rem; margin-top: 0.5rem;">System Overview</h2>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: var(--text-main); font-size: 1.1rem;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Master Administrator</div>
            </div>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2.5rem;">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bus"></i></div>
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; font-family: 'Outfit';"><?php echo $stats['buses']; ?></div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Buses</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--accent-color); background: rgba(245, 158, 11, 0.1);"><i class="fas fa-route"></i></div>
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; font-family: 'Outfit';"><?php echo $stats['routes']; ?></div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Network Routes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--success-color); background: rgba(16, 185, 129, 0.1);"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; font-family: 'Outfit';"><?php echo $stats['schedules']; ?></div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Active Trips</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: var(--danger-color); background: rgba(239, 68, 68, 0.1);"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <div style="font-size: 2.5rem; font-weight: 800; font-family: 'Outfit';"><?php echo $stats['active_alerts']; ?></div>
                    <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Live Alerts</div>
                </div>
            </div>
        </div>

        <section class="action-card glass animate-fade">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h3 style="font-family: 'Outfit'; font-size: 1.5rem;">Quick Management</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Jump to frequently used administrative tools.</p>
                </div>
            </div>
            <div style="display: flex; gap: 1.5rem;">
                <a href="announcements.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1rem;">
                    <i class="fas fa-plus"></i> Post New Update
                </a>
                <a href="schedules.php" class="btn btn-outline" style="padding: 1rem 2.5rem; font-size: 1rem;">
                    <i class="fas fa-edit"></i> Modify Schedules
                </a>
                <a href="../index.php" class="btn btn-outline" style="padding: 1rem 2.5rem; font-size: 1rem;">
                    <i class="fas fa-external-link-alt"></i> View Public Site
                </a>
            </div>
        </section>

    </main>

    <script>
        // Simple animation trigger for cards
        document.querySelectorAll('.stat-card').forEach((card, idx) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (idx * 100));
        });
    </script>
</body>

</html>
