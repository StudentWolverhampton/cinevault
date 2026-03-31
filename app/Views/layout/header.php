<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVault - Discover Movies</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ── Theme variables ── */
        [data-theme="dark"] {
            --bg-main:    #121212;
            --bg-card:    #1e1e1e;
            --bg-navbar:  #0a0a0a;
            --bg-drawer:  #0f0f0f;
            --bg-drawer-header: #0a0a0a;
            --bg-drawer-user:   #161616;
            --bg-input:   #1e1e1e;
            --text-main:  #dddddd;
            --text-muted: #aaaaaa;
            --text-placeholder: #777;
            --border:     #333333;
            --divider:    #222222;
            --overlay:    rgba(0,0,0,0.6);
            --shadow:     rgba(0,0,0,0.5);
        }
        [data-theme="light"] {
            --bg-main:    #f0f0f0;
            --bg-card:    #ffffff;
            --bg-navbar:  #1a1a2e;
            --bg-drawer:  #ffffff;
            --bg-drawer-header: #1a1a2e;
            --bg-drawer-user:   #f8f8f8;
            --bg-input:   #ffffff;
            --text-main:  #222222;
            --text-muted: #555555;
            --text-placeholder: #999;
            --border:     #cccccc;
            --divider:    #eeeeee;
            --overlay:    rgba(0,0,0,0.4);
            --shadow:     rgba(0,0,0,0.2);
        }

        /* ── Base ── */
        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }
        .card {
            background-color: var(--bg-card) !important;
            color: var(--text-main);
            transition: background-color 0.3s;
        }
        .btn-warning { background-color: #ffc107; color: black; font-weight: bold; }

        /* ── Navbar ── */
        .cv-navbar {
            background-color: var(--bg-navbar);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 12px var(--shadow);
            transition: background-color 0.3s;
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
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 20px;
            padding: 0.4rem 1rem;
            width: 100%;
            transition: border-color 0.2s, background 0.3s;
        }
        .cv-search-wrap input:focus {
            outline: none;
            border-color: #ffc107;
            color: var(--text-main);
        }
        .cv-search-wrap input::placeholder { color: var(--text-placeholder); }

        /* ── Theme toggle button ── */
        .cv-theme-btn {
            background: none;
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 20px;
            padding: 0.3rem 0.7rem;
            cursor: pointer;
            font-size: 1rem;
            margin-right: 0.5rem;
            transition: all 0.2s;
        }
        .cv-theme-btn:hover {
            border-color: #ffc107;
            color: #ffc107;
        }

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
            background: var(--text-main);
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
            background: var(--overlay);
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .cv-overlay.open { opacity: 1; pointer-events: all; }

        .cv-drawer {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            height: 100%;
            background: var(--bg-drawer);
            z-index: 3000;
            transition: right 0.3s ease, background-color 0.3s;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 20px var(--shadow);
        }
        .cv-drawer.open { right: 0; }

        .cv-drawer-header {
            background: var(--bg-drawer-header);
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--divider);
        }
        .cv-drawer-header span { font-size: 1.2rem; font-weight: 800; color: #ffc107; }
        .cv-drawer-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.4rem;
            cursor: pointer;
        }
        .cv-drawer-close:hover { color: var(--text-main); }

        .cv-drawer-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--divider);
            background: var(--bg-drawer-user);
            transition: background 0.3s;
        }
        .cv-drawer-user .username { color: #ffc107; font-weight: 700; }
        .cv-drawer-user .status   { color: var(--text-muted); font-size: 0.8rem; }

        .cv-drawer-nav { flex: 1; padding: 0.5rem 0; overflow-y: auto; }
        .cv-drawer-nav a {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.85rem 1.5rem;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.95rem;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .cv-drawer-nav a:hover {
            background: var(--bg-card);
            color: #ffc107;
            border-left-color: #ffc107;
        }
        .cv-drawer-nav .nav-icon { font-size: 1.1rem; width: 24px; text-align: center; }
        .cv-drawer-nav .nav-divider { border-top: 1px solid var(--divider); margin: 0.5rem 0; }

        /* Theme toggle inside drawer */
        .cv-drawer-theme {
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--divider);
        }
        .cv-drawer-theme span { color: var(--text-muted); font-size: 0.9rem; }
        .theme-switch {
            position: relative;
            width: 48px;
            height: 26px;
        }
        .theme-switch input { opacity: 0; width: 0; height: 0; }
        .theme-slider {
            position: absolute;
            inset: 0;
            background: #444;
            border-radius: 26px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .theme-slider:before {
            content: '';
            position: absolute;
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }
        input:checked + .theme-slider { background: #ffc107; }
        input:checked + .theme-slider:before { transform: translateX(22px); }

        .cv-drawer-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--divider);
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* ── Movie card hover ── */
        .movie-card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .movie-card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px var(--shadow) !important;
        }

        /* ── Back to top ── */
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
            box-shadow: 0 4px 12px var(--shadow);
        }
        #backToTop:hover { background: #e0a800; }

        /* ── Toast ── */
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
            background: var(--bg-card);
            border-left: 4px solid #ffc107;
            color: var(--text-main);
            padding: 12px 18px;
            border-radius: 6px;
            box-shadow: 0 4px 16px var(--shadow);
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

        /* Light mode overrides for Bootstrap elements */
        [data-theme="light"] .badge.bg-secondary { background-color: #888 !important; }
        [data-theme="light"] .alert-secondary { background: #e9e9e9; color: #333; border-color: #ccc; }
        [data-theme="light"] .form-control,
        [data-theme="light"] .form-select {
            background: #fff !important;
            color: #222 !important;
            border-color: #ccc !important;
        }
        [data-theme="light"] .text-light { color: #222 !important; }
        [data-theme="light"] .text-white { color: #111 !important; }

        @media (max-width: 576px) {
            .cv-search-wrap { max-width: none; margin: 0 0.75rem; }
        }
    </style>

    <script>
        // Apply saved theme BEFORE page renders (prevents flash)
        (function() {
            const saved = localStorage.getItem('cvTheme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="cv-navbar">
    <a class="cv-brand" href="<?= base_url() ?>">🎬 CineVault</a>

    <div class="cv-search-wrap">
        <input type="search" id="searchInput" placeholder="Search movies..." autocomplete="off">
    </div>

    <div style="display:flex; align-items:center; gap:0.5rem;">
        <!-- Theme toggle button in navbar -->
        <button class="cv-theme-btn" id="themeToggleNav" title="Toggle light/dark mode">🌙</button>

        <button class="cv-hamburger" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- ── Overlay ── -->
<div class="cv-overlay" id="drawerOverlay"></div>

<!-- ── Side Drawer ── -->
<div class="cv-drawer" id="sideDrawer">
    <div class="cv-drawer-header">
        <span>🎬 CineVault</span>
        <button class="cv-drawer-close" id="drawerClose">✕</button>
    </div>

    <?php if (session()->get('is_logged_in')): ?>
    <div class="cv-drawer-user">
        <div class="username">👤 <?= esc(session()->get('username')) ?></div>
        <div class="status">Logged in</div>
    </div>
    <?php else: ?>
    <div class="cv-drawer-user">
        <div class="username" style="color:var(--text-muted);">👤 Guest</div>
        <div class="status">Not logged in</div>
    </div>
    <?php endif; ?>

    <nav class="cv-drawer-nav">
        <a href="<?= base_url() ?>"><span class="nav-icon">🏠</span> Home</a>
        <a href="<?= base_url('movie/nearby') ?>"><span class="nav-icon">📍</span> Nearby Cinemas</a>

        <?php if (session()->get('is_logged_in')): ?>
        <a href="<?= base_url('movie/watchlist') ?>"><span class="nav-icon">🎯</span> My Watchlist</a>
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

    <!-- Theme toggle inside drawer -->
    <div class="cv-drawer-theme">
        <span id="themeLabel">🌙 Dark Mode</span>
        <label class="theme-switch">
            <input type="checkbox" id="themeToggleDrawer">
            <span class="theme-slider"></span>
        </label>
    </div>

    <div class="cv-drawer-footer">&copy; <?= date('Y') ?> CineVault</div>
</div>

<!-- Toast container -->
<div id="toastContainer"></div>

<!-- Back to top -->
<button id="backToTop" title="Back to top">↑</button>

<div class="container mt-4">

<script>
    // ── Theme toggle (localStorage) ───────────────────────────────
    const root          = document.documentElement;
    const toggleDrawer  = document.getElementById('themeToggleDrawer');
    const toggleNav     = document.getElementById('themeToggleNav');
    const themeLabel    = document.getElementById('themeLabel');

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem('cvTheme', theme);
        const isDark = theme === 'dark';
        toggleDrawer.checked = !isDark;
        toggleNav.textContent = isDark ? '🌙' : '☀️';
        themeLabel.textContent = isDark ? '🌙 Dark Mode' : '☀️ Light Mode';
    }

    // Init from localStorage
    applyTheme(localStorage.getItem('cvTheme') || 'dark');

    toggleDrawer.addEventListener('change', function () {
        applyTheme(this.checked ? 'light' : 'dark');
        showToast(this.checked ? '☀️ Light mode on' : '🌙 Dark mode on');
    });

    toggleNav.addEventListener('click', function () {
        const current = root.getAttribute('data-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
        showToast(root.getAttribute('data-theme') === 'light' ? '☀️ Light mode on' : '🌙 Dark mode on');
    });

    // ── Drawer ────────────────────────────────────────────────────
    const hamburger = document.getElementById('hamburgerBtn');
    const drawer    = document.getElementById('sideDrawer');
    const overlay   = document.getElementById('drawerOverlay');
    const closeBtn  = document.getElementById('drawerClose');

    function openDrawer()  {
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

    hamburger.addEventListener('click', () => drawer.classList.contains('open') ? closeDrawer() : openDrawer());
    overlay.addEventListener('click', closeDrawer);
    closeBtn.addEventListener('click', closeDrawer);
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
