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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="<?= base_url() ?>">🎬 CineVault</a>

            <form class="d-flex mx-auto" style="width: 450px;">
                <input class="form-control me-2" type="search" id="searchInput"
                       placeholder="Search for movies..." aria-label="Search">
            </form>

            <div class="d-flex align-items-center gap-2">
                <?php if (session()->get('is_logged_in')): ?>
                    <a href="<?= base_url('movie/watchlist') ?>" class="btn btn-outline-warning">🎯 Watchlist</a>
                    <span class="text-light">Hello, <?= esc(session()->get('username')) ?></span>
                    <a href="<?= base_url('user/logout') ?>" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('user/register') ?>" class="btn btn-outline-light">Register</a>
                    <a href="<?= base_url('user/login') ?>" class="btn btn-warning">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
