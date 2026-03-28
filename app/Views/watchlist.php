<?= $this->include('layout/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-white fw-bold">🎯 My Watchlist</h1>
    <span class="badge bg-warning text-dark fs-6"><?= count($movies) ?> movie<?= count($movies) !== 1 ? 's' : '' ?></span>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (empty($movies)): ?>
    <div class="text-center py-5">
        <p class="text-muted fs-5">Your watchlist is empty.</p>
        <a href="<?= base_url() ?>" class="btn btn-warning mt-2">Browse Movies</a>
    </div>
<?php else: ?>
    <div class="row" id="watchlistGrid">
        <?php foreach ($movies as $movie): ?>
        <div class="col-md-3 col-sm-6 mb-4" id="movie-<?= $movie['movie_id'] ?>">
            <div class="card border-0 shadow h-100" style="background:#1e1e1e;">
                <?php if (!empty($movie['poster_path'])): ?>
                    <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>"
                         class="card-img-top" alt="<?= esc($movie['title']) ?>">
                <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center"
                         style="height:300px;">
                        <span class="text-muted">No Poster</span>
                    </div>
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-light"><?= esc($movie['title']) ?></h5>
                    <p class="text-muted small"><?= substr($movie['release_date'] ?? '', 0, 4) ?></p>
                    <span class="badge bg-warning text-dark mb-3"><?= number_format($movie['vote_average'] ?? 0, 1) ?> ★</span>
                    <div class="mt-auto d-flex gap-2">
                        <a href="<?= base_url('movie/detail/' . $movie['movie_id']) ?>"
                           class="btn btn-outline-light btn-sm flex-grow-1">View</a>
                        <button class="btn btn-outline-danger btn-sm remove-btn"
                                data-movie-id="<?= $movie['movie_id'] ?>">Remove</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const movieId = this.dataset.movieId;
        this.disabled = true;
        this.textContent = '...';

        fetch(baseUrl + 'movie/toggleWatchlist', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'movie_id=' + movieId + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.action === 'removed') {
                const card = document.getElementById('movie-' + movieId);
                card.style.transition = 'opacity 0.3s';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    // Update count badge
                    const remaining = document.querySelectorAll('[id^="movie-"]').length;
                    document.querySelector('.badge.bg-warning').textContent =
                        remaining + ' movie' + (remaining !== 1 ? 's' : '');
                    if (remaining === 0) {
                        document.getElementById('watchlistGrid').innerHTML =
                            '<div class="col-12 text-center py-5"><p class="text-muted fs-5">Your watchlist is empty.</p>' +
                            '<a href="' + baseUrl + '" class="btn btn-warning mt-2">Browse Movies</a></div>';
                    }
                }, 300);
            }
        });
    });
});
</script>

<?= $this->include('layout/footer') ?>
