<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVault - Discover Movies</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #121212; color: #ddd; }
        .card { background-color: #1e1e1e; }
        .navbar { background-color: #0a0a0a !important; }
        .btn-warning { background-color: #ffc107; color: black; font-weight: bold; }

        /* Movie card hover effect */
        .movie-card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .movie-card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.6) !important;
        }

        /* Back to top button */
        #backToTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffc107;
            color: #000;
            border: none;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            transition: opacity 0.3s;
        }
        #backToTop:hover { background: #e0a800; }

        /* Toast notifications */
        #toastContainer {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .cv-toast {
            background: #1e1e1e;
            border-left: 4px solid #ffc107;
            color: #fff;
            padding: 12px 18px;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
            min-width: 220px;
            animation: slideIn 0.3s ease;
            font-size: 0.9rem;
        }
        .cv-toast.success { border-color: #28a745; }
        .cv-toast.danger  { border-color: #dc3545; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .navbar-brand { font-size: 1.2rem !important; }
            .search-form { width: 100% !important; margin: 0.5rem 0 !important; }
            .nav-buttons { flex-wrap: wrap; gap: 0.25rem !important; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container flex-wrap gap-2">
            <a class="navbar-brand fw-bold fs-3" href="<?= base_url() ?>">🎬 CineVault</a>

            <form class="d-flex search-form mx-auto" style="width:400px; position:relative;">
                <input class="form-control me-2" type="search" id="searchInput"
                       placeholder="Search for movies..." aria-label="Search"
                       autocomplete="off">
            </form>

            <div class="d-flex align-items-center gap-2 nav-buttons">
                <a href="<?= base_url('movie/nearby') ?>" class="btn btn-outline-light btn-sm">📍 Nearby</a>
                <?php if (session()->get('is_logged_in')): ?>
                    <a href="<?= base_url('movie/watchlist') ?>" class="btn btn-outline-warning btn-sm">🎯 Watchlist</a>
                    <span class="text-light d-none d-md-inline">Hello, <?= esc(session()->get('username')) ?></span>
                    <a href="<?= base_url('user/logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('user/register') ?>" class="btn btn-outline-light btn-sm">Register</a>
                    <a href="<?= base_url('user/login') ?>" class="btn btn-warning btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Toast container -->
    <div id="toastContainer"></div>

    <!-- Back to top button -->
    <button id="backToTop" title="Back to top">↑</button>

    <div class="container mt-4">

<script>
    // ── Back to top ───────────────────────────────────────────────
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ── Toast helper ──────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `cv-toast ${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.4s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    window.showToast = showToast;
</script>
