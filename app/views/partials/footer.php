</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> GAMY. All rights reserved.</p>
        <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>about">About</a></li>
            <li><a href="<?= BASE_URL ?>terms">Terms of Service</a></li>
            <li><a href="<?= BASE_URL ?>privacy">Privacy Policy</a></li>
        </ul>
    </div>
</footer>

<!-- Floating Admin Button -->
<div class="admin-floating">
    <a href="<?= ADMIN_URL ?>/dashboard">
        <i class="fas fa-lock"></i>
        <span>Admin</span>
    </a>
</div>

<!-- Mobile Menu Toggle Script -->
<script>
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
</body>
</html>