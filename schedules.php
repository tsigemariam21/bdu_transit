<?php
$page_title = "Bus Schedules - BDU Transit";
require_once 'includes/header.php';

// Fetch Schedules with Route and Bus details
try {
    $sql = "SELECT s.*, b.bus_number, r.route_name, r.description as route_desc 
            FROM schedules s 
            JOIN buses b ON s.bus_id = b.bus_id 
            JOIN routes r ON s.route_id = r.route_id 
            ORDER BY r.route_name, s.departure_time";
    $stmt = $pdo->query($sql);
    $all_schedules = $stmt->fetchAll();

    // Group by Route Name
    $grouped_schedules = [];
    foreach ($all_schedules as $row) {
        $grouped_schedules[$row['route_name']]['desc'] = $row['route_desc'];
        $grouped_schedules[$row['route_name']]['trips'][] = $row;
    }

} catch (PDOException $e) {
    $error = "Error fetching schedules: " . $e->getMessage();
}
?>
    <style>
        .route-section {
            margin-bottom: 4rem;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .trip-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--radius-lg);
            border: var(--glass-border);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
        }

        .trip-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .time-display {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-family: 'Outfit';
        }

        .duration {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .bus-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 0.4rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 1.5rem;
        }
    </style>

    <div class="container" style="margin-top: 120px; min-height: 80vh;">
        <header style="text-align: center; margin-bottom: 4rem;">
            <span style="color: var(--primary-color); font-weight: 800; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.1em;">Timing Matters</span>
            <h1 style="font-size: 3rem; font-family: 'Outfit'; margin-top: 0.5rem;">Bus Schedules</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Precision timing for every campus route.</p>
        </header>

        <div class="search-container glass" style="margin-bottom: 4rem; padding: 1.5rem; border-radius: var(--radius-xl); display: flex; gap: 1rem; box-shadow: var(--shadow-lg);">
            <div style="position: relative; flex: 1;">
                <i class="fas fa-search" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"></i>
                <input type="text" id="scheduleSearch" placeholder="Search by route, bus number, or destination..." 
                    style="width: 100%; padding: 1rem 1.25rem 1rem 3.25rem; border-radius: var(--radius-md); border: 2px solid #e2e8f0; background: var(--bg-main); color: var(--text-main); font-size: 1rem; transition: var(--transition);">
            </div>
            <button class="btn btn-primary" style="padding: 0 2rem; font-size: 1rem;" onclick="filterSchedules()">Find Ride</button>
        </div>

        <?php if (isset($error)): ?>
            <div class="glass" style="background: rgba(239, 68, 68, 0.05); color: var(--danger-color); padding: 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; border-left: 4px solid var(--danger-color);">
                <i class="fas fa-exclamation-triangle"></i> &nbsp; <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($grouped_schedules as $route_name => $data): ?>
            <div class="route-section">
                <div style="display: flex; align-items: baseline; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1.5rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 800; font-family: 'Outfit'; color: var(--text-main);">
                        <i class="fas fa-route" style="color: var(--primary-color);"></i> <?php echo htmlspecialchars($route_name); ?>
                    </h2>
                    <p style="color: var(--text-muted); font-weight: 500; font-size: 1rem;"><?php echo htmlspecialchars($data['desc']); ?></p>
                </div>

                <div class="schedule-grid">
                    <?php foreach ($data['trips'] as $trip): ?>
                        <div class="trip-card">
                            <div class="time-display">
                                <?php echo date('H:i', strtotime($trip['departure_time'])); ?>
                                <i class="fas fa-arrow-right" style="font-size: 1rem; color: var(--primary-color); opacity: 0.5;"></i>
                                <?php echo $trip['arrival_time'] ? date('H:i', strtotime($trip['arrival_time'])) : '--:--'; ?>
                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400; margin-left: auto;">DAILY</span>
                            </div>
                            <div class="duration">
                                <i class="far fa-clock"></i> &nbsp;
                                <?php
                                if ($trip['arrival_time']) {
                                    $start = new DateTime($trip['departure_time']);
                                    $end = new DateTime($trip['arrival_time']);
                                    echo $start->diff($end)->format('%i min') . " transit time";
                                } else {
                                    echo "Estimated arrival";
                                }
                                ?>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div class="bus-badge">
                                    <i class="fas fa-bus"></i> <?php echo htmlspecialchars($trip['bus_number']); ?>
                                </div>
                                <div style="margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                                    <i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($trip['operating_days']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($grouped_schedules)): ?>
            <div class="glass" style="text-align: center; padding: 5rem; border-radius: var(--radius-xl);">
                <i class="fas fa-bus-alt" style="font-size: 4rem; color: var(--primary-light); margin-bottom: 2rem;"></i>
                <h3 style="color: var(--text-muted); font-family: 'Outfit';">No schedules found at the moment.</h3>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function filterSchedules() {
            const query = document.getElementById('scheduleSearch').value.toLowerCase();
            const routes = document.querySelectorAll('.route-section');
            
            routes.forEach(route => {
                const routeName = route.querySelector('h2').textContent.toLowerCase();
                const routeDesc = route.querySelector('p').textContent.toLowerCase();
                const trips = route.querySelectorAll('.trip-card');
                let hasVisibleTrip = false;

                trips.forEach(trip => {
                    const busNum = trip.querySelector('.bus-badge').textContent.toLowerCase();
                    const text = trip.textContent.toLowerCase();
                    
                    if (routeName.includes(query) || routeDesc.includes(query) || busNum.includes(query) || text.includes(query)) {
                        trip.style.display = 'block';
                        hasVisibleTrip = true;
                    } else {
                        trip.style.display = 'none';
                    }
                });

                if (hasVisibleTrip) {
                    route.style.display = 'block';
                } else {
                    route.style.display = 'none';
                }
            });
        }

        document.getElementById('scheduleSearch').addEventListener('keyup', filterSchedules);
    </script>

<?php require_once 'includes/footer.php'; ?>
