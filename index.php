<?php
$page_title = "BDU Transit - Smart Campus Mobility";
require_once 'includes/header.php';

// Fetch active announcements
try {
    $stmt = $pdo->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY date_posted DESC LIMIT 3");
    $announcements = $stmt->fetchAll();
} catch (PDOException $e) {
    $announcements = [];
}
?>

    <!-- Announcements Banner -->
    <?php if (!empty($announcements)): ?>
        <div class="glass animate-fade"
            style="margin-top: 80px; padding: 0.85rem 5%; border-radius: 0; border-left: none; border-right: none; display: flex; align-items: center; gap: 1.5rem; z-index: 900; position: relative;">
            <div style="background: var(--primary-color); color: white; padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; white-space: nowrap; letter-spacing: 0.05em;">
                <i class="fas fa-bolt"></i> Live Updates
            </div>
            <marquee scrollamount="6" style="color: var(--text-main); font-weight: 500; font-size: 0.95rem;">
                <?php foreach ($announcements as $ann): ?>
                    <span style="margin-right: 4rem;">
                        <span style="color: var(--primary-color); font-weight: 700;"><?php echo htmlspecialchars($ann['title']); ?>:</span>
                        <?php echo htmlspecialchars($ann['message']); ?>
                    </span>
                <?php endforeach; ?>
            </marquee>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero" style="padding-top: <?php echo !empty($announcements) ? '4rem' : '8rem'; ?>;">
        <div class="hero-content animate-fade">
            <span style="background: var(--primary-light); color: var(--primary-color); padding: 0.5rem 1.25rem; border-radius: 100px; font-weight: 700; font-size: 0.85rem; margin-bottom: 2rem; display: inline-block; letter-spacing: 0.05em; text-transform: uppercase;">
                <i class="fas fa-check-circle"></i> The Official BDU Transit Guide
            </span>
            <h1 style="font-family: 'Outfit';">Campus Mobility <br> <span style="color: var(--primary-color); position: relative; display: inline-block;">
                Reimagined
                <svg style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 12px;" viewBox="0 0 200 12" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 10C50 2 150 2 200 10" stroke="var(--primary-color)" stroke-width="4" fill="transparent" stroke-linecap="round"/>
                </svg>
            </span></h1>
            <p style="font-size: 1.2rem; line-height: 1.8;">Experience the future of campus transportation. Get real-time schedules, live bus tracking, and instant alerts at your fingertips.</p>

            <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 2rem; margin-bottom: 4rem;">
                <a href="schedules.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem; box-shadow: var(--shadow-primary);">
                    <i class="far fa-calendar-alt"></i> View Schedules
                </a>
                <a href="tracking.php" class="btn btn-outline" style="padding: 1rem 2rem; font-size: 1rem; background: rgba(255,255,255,0.5);">
                    <i class="fas fa-location-arrow"></i> Live Tracking
                </a>
            </div>

            <div class="hero-cards">
                <div class="feature-card glass">
                    <div class="feature-icon"><i class="fas fa-clock"></i></div>
                    <h3 style="font-weight: 700; margin-bottom: 1rem;">Real-Time Schedules</h3>
                    <p style="font-size: 0.95rem;">Access frame-accurate departure and arrival times for all campus routes, updated dynamically.</p>
                </div>
                <div class="feature-card glass" style="border-top: 4px solid var(--primary-color);">
                    <div class="feature-icon" style="background: var(--primary-color); color: white;"><i class="fas fa-satellite"></i></div>
                    <h3 style="font-weight: 700; margin-bottom: 1rem;">Live Tracking</h3>
                    <p style="font-size: 0.95rem;">Never miss a bus again. Watch your ride move in real-time across Bahir Dar with our digital map.</p>
                </div>
                <div class="feature-card glass">
                    <div class="feature-icon" style="color: var(--accent-color); background: rgba(245, 158, 11, 0.1);"><i class="fas fa-bell"></i></div>
                    <h3 style="font-weight: 700; margin-bottom: 1rem;">Instant Alerts</h3>
                    <p style="font-size: 0.95rem;">Receive push notifications for delays, route changes, or emergency updates instantly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container" style="padding: 6rem 5% 2rem; text-align: center;">
        <div style="margin-bottom: 4rem;">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">Our Impact</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Dedicated to providing the best transportation experience for the BDU community.</p>
        </div>
        <div class="glass" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; padding: 4rem 2rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-xl);">
            <div>
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; font-family: 'Outfit';">05+</h2>
                <p style="color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em;">Campus Routes</p>
            </div>
            <div style="border-left: 1px solid rgba(0,0,0,0.05); border-right: 1px solid rgba(0,0,0,0.05);">
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; font-family: 'Outfit';">12</h2>
                <p style="color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em;">Active Buses</p>
            </div>
            <div>
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; font-family: 'Outfit';">2k+</h2>
                <p style="color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em;">Daily Commuters</p>
            </div>
            <div style="border-left: 1px solid rgba(0,0,0,0.05);">
                <h2 style="font-size: 3rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem; font-family: 'Outfit';">100%</h2>
                <p style="color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em;">Digital Signal</p>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
