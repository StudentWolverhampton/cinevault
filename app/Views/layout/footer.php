    </div> <!-- End container -->

    <footer style="background: var(--bg-navbar); color: var(--text-muted);" class="text-center py-4 mt-5">
        <p class="mb-1" style="color: var(--text-muted);">&copy; <?= date('Y') ?> CineVault - Advanced Web Technologies Coursework</p>
        <div class="d-flex justify-content-center gap-3 mt-2">
            <a href="<?= base_url() ?>" style="color:#ffc107; text-decoration:none;">🏠 Home</a>
            <a href="<?= base_url('movie/nearby') ?>" style="color:#ffc107; text-decoration:none;">📍 Nearby</a>
            <?php if (session()->get('is_logged_in')): ?>
                <a href="<?= base_url('movie/watchlist') ?>" style="color:#ffc107; text-decoration:none;">🎯 Watchlist</a>
            <?php endif; ?>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
