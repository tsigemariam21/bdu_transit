<?php
$page_title = "Live Tracking - BDU Transit";
require_once 'includes/header.php';

// Fetch Routes
try {
    $routes_stmt = $pdo->query("SELECT * FROM routes");
    $routes = $routes_stmt->fetchAll();

    // Fetch Stops for all routes
    $stops_stmt = $pdo->query("SELECT * FROM pickup_points ORDER BY route_id, sequence_order");
    $all_stops = $stops_stmt->fetchAll();

    // Structure stops by route_id for JS
    $stops_by_route = [];
    foreach ($all_stops as $stop) {
        $stops_by_route[$stop['route_id']][] = $stop;
    }

} catch (PDOException $e) {
    $error = "Error loading map data.";
}
?>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #mapDisplay {
            height: 600px;
            width: 100%;
            border-radius: var(--radius-xl);
            position: relative;
            z-index: 10;
            border: var(--glass-border);
            box-shadow: var(--shadow-xl);
            background-color: #f8fafc;
        }

        .control-sidebar {
            background: var(--bg-card);
            padding: 2.5rem;
            border-radius: var(--radius-xl);
            border: var(--glass-border);
            box-shadow: var(--shadow-lg);
            height: 100%;
        }

        .custom-bus-icon {
            background: var(--primary-color);
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 10px 10px 20px rgba(0,0,0,0.2);
        }

        .pulse {
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0px rgba(14, 165, 233, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(14, 165, 233, 0); }
            100% { box-shadow: 0 0 0 0px rgba(14, 165, 233, 0); }
        }

        .route-step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-left: 2px dashed #e2e8f0;
            margin-left: 0.75rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .route-step::before {
            content: '';
            position: absolute;
            left: -0.45rem;
            top: 1.25rem;
            width: 0.75rem;
            height: 0.75rem;
            background: white;
            border: 2px solid var(--primary-color);
            border-radius: 50%;
        }

        .route-step.active::before {
            background: var(--primary-color);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .route-step:last-child {
            border-left: none;
        }
    </style>

    <div class="container" style="margin-top: 120px; min-height: 85vh;">
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
            <div class="animate-fade">
                <header style="margin-bottom: 2rem;">
                    <span style="color: var(--primary-color); font-weight: 800; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.1em;">Real-Time Signal</span>
                    <h1 style="font-family: 'Outfit'; font-size: 2.5rem;"><i class="fas fa-satellite-dish" style="color: var(--primary-color);"></i> Live Bus Tracking</h1>
                    <p style="color: var(--text-muted);">Navigate Bahir Dar University with live GPS positioning.</p>
                </header>

                <div id="mapDisplay" class="glass"></div>
            </div>

            <aside>
                <div class="control-sidebar glass">
                    <h3 style="font-family: 'Outfit'; margin-bottom: 1.5rem;">Trip Planner</h3>
                    
                    <div style="margin-bottom: 2rem;">
                        <label style="font-weight: 700; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Select Route</label>
                        <select id="routeSelect" onchange="renderRoute()" class="form-control" style="padding: 0.75rem; font-weight: 500;">
                            <option value="">Choose an active route</option>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['route_id']; ?>">
                                    <?php echo htmlspecialchars($route['route_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="trackingStatus" style="display:none;">
                        <div class="badge badge-success" style="margin-bottom: 2rem; width: 100%; justify-content: center; padding: 0.5rem;">
                            <i class="fas fa-wifi"></i> &nbsp; LIVE SIGNAL ACTIVE
                        </div>

                        <div style="background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.05);">
                            <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Estimated Arrival</span>
                            <div id="nextStopText" style="font-weight: 800; font-size: 1.1rem; color: var(--primary-color); margin-top: 0.25rem;">Waiting for route...</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;"><i class="fas fa-bus"></i> BDU-101 in transit</div>
                        </div>

                        <h4 style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem;">Route Progress</h4>
                        <div id="routeTimeline">
                            <!-- JS populated -->
                        </div>
                    </div>

                    <div id="emptyTracking" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                        <i class="fas fa-map-marked-alt" style="font-size: 3rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                        <p style="font-size: 0.9rem;">Select a route to begin live tracking</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        const routeData = <?php echo json_encode($stops_by_route); ?>;
        const fallbackCoords = {
            'Poly Campus Gate': [11.5992, 37.3965],
            'City Center': [11.5932, 37.3875],
            'Zenzelma Campus': [11.6360, 37.4580],
            'Kebele 10': [11.5850, 37.4100],
            'Peda Campus': [11.5721, 37.3911]
        };

        let map;
        let currentMarkers = [];
        let currentPolyline = null;
        let busMarker = null;
        let animationInterval;

        // Initialize map when content is loaded
        window.addEventListener('load', function() {
            initMap();
        });

        function initMap() {
            if (map) return;
            
            map = L.map('mapDisplay', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([11.5883, 37.3908], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a slight delay to ensure container is fully rendered
            setTimeout(() => {
                map.invalidateSize();
            }, 500);
        }

        function renderRoute() {
            const routeId = document.getElementById('routeSelect').value;
            const trackingStatus = document.getElementById('trackingStatus');
            const emptyTracking = document.getElementById('emptyTracking');
            const nextStopText = document.getElementById('nextStopText');
            const timeline = document.getElementById('routeTimeline');

            // Clear previous
            currentMarkers.forEach(m => map.removeLayer(m));
            currentMarkers = [];
            if (currentPolyline) map.removeLayer(currentPolyline);
            if (busMarker) map.removeLayer(busMarker);
            if (animationInterval) clearInterval(animationInterval);
            
            if (!routeId || !routeData[routeId]) {
                trackingStatus.style.display = 'none';
                emptyTracking.style.display = 'block';
                map.setView([11.5883, 37.3908], 14);
                return;
            }

            trackingStatus.style.display = 'block';
            emptyTracking.style.display = 'none';
            
            const stops = routeData[routeId];
            const points = [];
            const stopNames = [];
            
            timeline.innerHTML = '';

            stops.forEach((stop, index) => {
                let lat = parseFloat(stop.latitude);
                let lng = parseFloat(stop.longitude);
                stopNames.push(stop.location_name);

                if (!lat || !lng) {
                    const fallback = fallbackCoords[stop.location_name];
                    if (fallback) {
                        lat = fallback[0];
                        lng = fallback[1];
                    } else {
                        lat = 11.5883 + (Math.random() - 0.5) * 0.02;
                        lng = 37.3908 + (Math.random() - 0.5) * 0.02;
                    }
                }

                points.push([lat, lng]);

                // Create custom marker for stops
                const stopIcon = L.divIcon({
                    className: 'stop-marker',
                    html: `<div style="width:12px; height:12px; background:white; border:3px solid var(--primary-color); border-radius:50%;"></div>`,
                    iconSize: [12, 12]
                });

                const marker = L.marker([lat, lng], {icon: stopIcon}).addTo(map)
                    .bindPopup(`<b>${stop.location_name}</b><br>Stop #${stop.sequence_order}`);
                currentMarkers.push(marker);

                // Add to timeline
                const step = document.createElement('div');
                step.className = 'route-step';
                step.innerHTML = `<div><div style="font-weight:700; font-size:0.9rem;">${stop.location_name}</div><div style="font-size:0.75rem; color:var(--text-muted);">Stop #${stop.sequence_order}</div></div>`;
                timeline.appendChild(step);
            });

            currentPolyline = L.polyline(points, {color: 'var(--primary-color)', weight: 5, opacity: 0.5}).addTo(map);
            map.fitBounds(currentPolyline.getBounds(), {padding: [100, 100]});

            const busIcon = L.divIcon({
                className: 'custom-bus-icon pulse',
                html: '<i class="fas fa-bus"></i>',
                iconSize: [44, 44],
                iconAnchor: [22, 22]
            });

            busMarker = L.marker(points[0], {icon: busIcon}).addTo(map);

            let step = 0;
            const totalSteps = 150;
            let currentStop = 0;
            const timelineSteps = document.querySelectorAll('.route-step');

            animationInterval = setInterval(() => {
                step++;
                if (step > totalSteps) {
                    step = 0;
                    currentStop++;
                    if (currentStop >= points.length - 1) currentStop = 0;
                }

                const start = points[currentStop];
                const end = points[currentStop + 1];
                
                nextStopText.innerText = stopNames[currentStop + 1] || 'Main Campus';
                
                // Update timeline active state
                timelineSteps.forEach((s, idx) => {
                    if (idx === currentStop + 1) s.classList.add('active');
                    else s.classList.remove('active');
                });
                
                const lat = start[0] + (end[0] - start[0]) * (step / totalSteps);
                const lng = start[1] + (end[1] - start[1]) * (step / totalSteps);
                
                busMarker.setLatLng([lat, lng]);
            }, 50);
        }
    </script>
<?php require_once 'includes/footer.php'; ?>
