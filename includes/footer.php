    <!-- Footer -->
    <footer style="margin-top: 6rem; padding: 5rem 5% 2rem; background: var(--bg-card); border-top: var(--glass-border); position: relative; overflow: hidden;">
        <!-- Subtle decorative sphere -->
        <div style="position: absolute; bottom: -50px; right: -50px; width: 200px; height: 200px; background: var(--primary-light); border-radius: 50%; filter: blur(60px); opacity: 0.5; z-index: 0;"></div>
        
        <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 4rem; padding: 0; position: relative; z-index: 1;">
            <div>
                <a href="index.php" class="logo" style="margin-bottom: 1.5rem; font-size: 1.5rem;">
                    <i class="fas fa-bus-alt"></i> BDU Transit
                </a>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7;">
                    Empowering Bahir Dar University with smart, real-time transportation solutions. Reducing wait times and improving campus accessibility for everyone.
                </p>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="#" style="color: var(--text-muted); font-size: 1.25rem;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: var(--text-muted); font-size: 1.25rem;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color: var(--text-muted); font-size: 1.25rem;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--text-main);">Quick Links</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="index.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9375rem; transition: var(--transition);">Home</a>
                    <a href="schedules.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9375rem; transition: var(--transition);">Schedules</a>
                    <a href="tracking.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9375rem; transition: var(--transition);">Live Tracking</a>
                    <a href="admin/login.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9375rem; transition: var(--transition);">Admin Portal</a>
                </div>
            </div>
            <div>
                <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--text-main);">Help & Support</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem; color: var(--text-muted); font-size: 0.9375rem;">
                    <span><i class="fas fa-envelope" style="width: 20px; color: var(--primary-color);"></i> transport@bdu.edu.et</span>
                    <span><i class="fas fa-phone" style="width: 20px; color: var(--primary-color);"></i> +251 58 220 0000</span>
                    <span><i class="fas fa-map-marker-alt" style="width: 20px; color: var(--primary-color);"></i> Bahir Dar, Ethiopia</span>
                </div>
            </div>
        </div>
        <div style="margin-top: 5rem; padding-top: 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); text-align: center; color: var(--text-muted); font-size: 0.875rem;">
            &copy; <?php echo date('Y'); ?> Bahir Dar University Transit. Crafted for Excellence.
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
                navbar.style.background = 'var(--glass-bg)';
                navbar.style.boxShadow = 'var(--shadow-md)';
            } else {
                navbar.classList.remove('scrolled');
                navbar.style.background = 'transparent';
                navbar.style.boxShadow = 'none';
            }
        });

        // Add stagger fade-in to cards if they exist
        const cards = document.querySelectorAll('.feature-card, .trip-card, .stat-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
            card.style.transitionDelay = `${index * 0.1}s`;
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
