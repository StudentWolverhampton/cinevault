<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVault - Discover Movies</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #121212; color: #ddd; }
        .card { background-color: #1e1e1e; }
        .btn-warning { background-color: #ffc107; color: black; font-weight: bold; }

        /* ── Navbar ── */
        .cv-navbar {
            background-color: #0a0a0a;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 12px rgba(0,0,0,0.5);
        }
        .cv-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffc107;
            text-decoration: none;
            letter-spacing: 1px;
        }
        .cv-brand:hover { color: #e0a800; }

        /* ── Search ── */
        .cv-search-wrap {
            position: relative;
            flex: 1;
            max-width: 420px;
            margin: 0 1.5rem;
        }
        .cv-search-wrap input {
            background: #1e1e1e;
            border: 1px solid #333;
            color: #ddd;
            border-radius: 20px;
            padding: 0.4rem 1rem;
            width: 100%;
            transition: border-color 0.2s;
        }
        .cv-search-wrap input:focus {
            outline: none;
            border-color: #ffc107;
            background: #252525;
            color: #fff;
        }
        .cv-search-wrap input::placeholder { color: #777; }

        /* ── Hamburger ── */
        .cv-hamburger {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 4px;
        }
        .cv-hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: #ddd;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .cv-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .cv-hamburger.open span:nth-child(2) { opacity: 0; }
        .cv-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* ── Side Drawer ── */
        .cv-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .cv-overlay.open {
            opacity: 1;
            pointer-events: all;
        }
        .cv-drawer {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            height: 100%;
            background: #0f0f0f;
            z-index: 3000;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            padding: 0;
            box-shadow: -4px 0 20px rgba(0,0,0,0.6);
        }
        .cv-drawer.open { right: 0; }

        /* Drawer header */
        .cv-drawer-header {
            background: #0a0a0a;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #222;
        }
        .cv-drawer-header span {
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffc107;
        }
        .cv-drawer-close {
            background: none;
            border: none;
            color: #aaa;
            font-size: 1.4rem;
            cursor: pointer;
            line-height: 1;
        }
        .cv-drawer-close:hover { color: #fff; }

        /* User badge in drawer */
        .cv-drawer-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #222;
            background: #161616;
        }
        .cv-drawer-user .username {
            color: #ffc107;
            font-weight: 700;
            font-size: 1rem;
        }
        .cv-drawer-user .status {
            color: #aaa;
            font-size: 0.8rem;
        }

        /* Nav links */
        .cv-drawer-nav {
            flex: 1;
            padding: 0.5rem 0;
            overflow-y: auto;
        }
        .cv-drawer-nav a {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.85rem 1.5rem;
            color: #ccc;
            text-decoration: none;
            font-size: 0.95rem;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .cv-drawer-nav a:hover {
            background: #1e1e1e;
            color: #fff;
            border-left-color: #ffc107;
        }
        .cv-drawer-nav a .nav-icon { font-size: 1.1rem; width: 24px; text-align: center; }
        .cv-drawer-nav .nav-divider {
            border-top: 1px solid #222;
            margin: 0.5rem 0;
        }

        /* Drawer footer */
        .cv-drawer-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #222;
            font-size: 0.75rem;
            color: #555;
            text-align: center;
        }

        /* Movie card hover */
        .movie-card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .movie-card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.6) !important;
        }

        /* Back to top */
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
        }
        #backToTop:hover { background: #e0a800; }

        /* Toast */
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

        /* Mobile search full width */
        @media (max-width: 576px) {
            .cv-search-wrap { max-width: none; margin: 0 0.75rem; }
        }
    </style>
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav class="cv-navbar">
    <a class="cv-brand" href="<?= base_url() ?>">🎬 CineVault</a>

    <div class="cv-search-wrap">
        <input type="search" id="searchInput" placeholder="Search movies..." autocomplete="off">
    </div>

    <button class="cv-hamburger" id="hamburgerBtn" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
</nav>

<!-- ── Overlay ─────────────────────────────────────────────── -->
<div class="cv-overlay" id="drawerOverlay"></div>

<!-- ── Side Drawer ─────────────────────────────────────────── -->
<div class="cv-drawer" id="sideDrawer">
    <div class="cv-drawer-header">
        <span>🎬 CineVault</span>
        <button class="cv-drawer-close" id="drawerClose">✕</button>
    </div>

    <!-- User section -->
    <?php if (session()->get('is_logged_in')): ?>
    <div class="cv-drawer-user">
        <div class="username">👤 <?= esc(session()->get('username')) ?></div>
        <div class="status">Logged in</div>
    </div>
    <?php else: ?>
    <div class="cv-drawer-user">
        <div class="username" style="color:#aaa;">👤 Guest</div>
        <div class="status">Not logged in</div>
    </div>
    <?php endif; ?>

    <!-- Nav links -->
    <nav class="cv-drawer-nav">
        <a href="<?= base_url() ?>">
            <span class="nav-icon">🏠</span> Home
        </a>
        <a href="<?= base_url('movie/nearby') ?>">
            <span class="nav-icon">📍</span> Nearby Cinemas
        </a>

        <?php if (session()->get('is_logged_in')): ?>
        <a href="<?= base_url('movie/watchlist') ?>">
            <span class="nav-icon">🎯</span> My Watchlist
        </a>
        <div class="nav-divider"></div>
        <a href="<?= base_url('user/logout') ?>" style="color:#e74c3c;">
            <span class="nav-icon">🚪</span> Logout
        </a>
        <?php else: ?>
        <div class="nav-divider"></div>
        <a href="<?= base_url('user/login') ?>" style="color:#ffc107;">
            <span class="nav-icon">🔑</span> Login
        </a>
        <a href="<?= base_url('user/register') ?>">
            <span class="nav-icon">📝</span> Register
        </a>
        <?php endif; ?>
    </nav>

    <div class="cv-drawer-footer">
        &copy; <?= date('Y') ?> CineVault
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer"></div>

<!-- Back to top -->
<button id="backToTop" title="Back to top">↑</button>

<div class="container mt-4">

<script>
    // ── Drawer ────────────────────────────────────────────────────
    const hamburger = document.getElementById('hamburgerBtn');
    const drawer    = document.getElementById('sideDrawer');
    const overlay   = document.getElementById('drawerOverlay');
    const closeBtn  = document.getElementById('drawerClose');

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        hamburger.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', () => {
        drawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    overlay.addEventListener('click', closeDrawer);
    closeBtn.addEventListener('click', closeDrawer);

    // Close on nav link click
    drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));

    // ── Back to top ───────────────────────────────────────────────
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // ── Toast ─────────────────────────────────────────────────────
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
