<?= $this->include('layout/header') ?>

<?php
$poster = !empty($movie['poster_path'])
    ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
    : 'https://via.placeholder.com/300x450?text=No+Poster';

$backdrop = !empty($movie['backdrop_path'])
    ? 'https://image.tmdb.org/t/p/w1280' . $movie['backdrop_path']
    : null;

$trailer = null;
if (!empty($movie['videos']['results'])) {
    foreach ($movie['videos']['results'] as $video) {
        if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
            $trailer = $video['key'];
            break;
        }
    }
}

$cast      = array_slice($movie['credits']['cast'] ?? [], 0, 8);
$directors = array_filter($movie['credits']['crew'] ?? [], fn($c) => $c['job'] === 'Director');
?>

<!-- Backdrop Banner -->
<?php if ($backdrop): ?>
<div style="background: linear-gradient(to bottom, rgba(18,18,18,0) 0%, #121212 100%),
            url('<?= $backdrop ?>') center/cover no-repeat;
            height: 280px; margin: -1.5rem -12px 2rem; padding: 2rem;">
</div>
<?php endif; ?>

<!-- Movie Header -->
<div class="row mb-5">
    <div class="col-md-3 text-center mb-3">
        <img src="<?= $poster ?>" alt="<?= esc($movie['title']) ?>"
             class="img-fluid rounded shadow" style="max-height:400px;">
    </div>
    <div class="col-md-9">
        <h1 class="text-white fw-bold">
            <?= esc($movie['title']) ?>
            <span class="text-muted fs-4">(<?= substr($movie['release_date'] ?? '----', 0, 4) ?>)</span>
        </h1>

        <?php if (!empty($movie['tagline'])): ?>
            <p class="text-warning fst-italic mb-2">"<?= esc($movie['tagline']) ?>"</p>
        <?php endif; ?>

        <!-- Genres -->
        <div class="mb-3">
            <?php foreach ($movie['genres'] ?? [] as $genre): ?>
                <span class="badge bg-secondary me-1"><?= esc($genre['name']) ?></span>
            <?php endforeach; ?>
        </div>

        <!-- Stats -->
        <div class="d-flex gap-4 mb-3 flex-wrap">
            <div class="text-center">
                <div class="text-warning fw-bold fs-4"><?= number_format($movie['vote_average'] ?? 0, 1) ?> ★</div>
                <div class="text-muted small"><?= number_format($movie['vote_count'] ?? 0) ?> votes</div>
            </div>
            <div class="text-center">
                <div class="text-white fw-bold fs-4"><?= $movie['runtime'] ?? '?' ?> min</div>
                <div class="text-muted small">Runtime</div>
            </div>
            <?php if (!empty($movie['budget']) && $movie['budget'] > 0): ?>
            <div class="text-center">
                <div class="text-white fw-bold fs-5">$<?= number_format($movie['budget'] / 1000000, 1) ?>M</div>
                <div class="text-muted small">Budget</div>
            </div>
            <?php endif; ?>
            <?php if (!empty($movie['revenue']) && $movie['revenue'] > 0): ?>
            <div class="text-center">
                <div class="text-success fw-bold fs-5">$<?= number_format($movie['revenue'] / 1000000, 1) ?>M</div>
                <div class="text-muted small">Revenue</div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Director -->
        <?php if (!empty($directors)): ?>
        <p class="text-muted mb-2">
            <span class="text-light">Directed by:</span>
            <?= esc(implode(', ', array_column(array_values($directors), 'name'))) ?>
        </p>
        <?php endif; ?>

        <p class="text-light mt-2" style="line-height:1.7;"><?= esc($movie['overview'] ?? '') ?></p>

        <!-- Watchlist Button -->
        <?php if (session()->get('is_logged_in')): ?>
            <button id="watchlistBtn"
                    class="btn <?= $inWatchlist ? 'btn-success' : 'btn-outline-warning' ?> mt-2"
                    data-movie-id="<?= $movie['id'] ?>">
                <?= $inWatchlist ? '✅ In Watchlist' : '+ Add to Watchlist' ?>
            </button>
        <?php else: ?>
            <a href="<?= base_url('user/login') ?>" class="btn btn-outline-warning mt-2">
                Login to add to Watchlist
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Trailer -->
<?php if ($trailer): ?>
<div class="mb-5">
    <h3 class="text-white mb-3">🎬 Trailer</h3>
    <div class="ratio ratio-16x9" style="max-width:720px;">
        <iframe src="https://www.youtube.com/embed/<?= $trailer ?>"
                allowfullscreen title="Trailer"></iframe>
    </div>
</div>
<?php endif; ?>

<!-- Cast -->
<?php if (!empty($cast)): ?>
<div class="mb-5">
    <h3 class="text-white mb-3">🎭 Cast</h3>
    <div class="row g-3">
        <?php foreach ($cast as $actor): ?>
        <div class="col-6 col-md-3">
            <div class="card border-0 text-center" style="background:#1e1e1e;">
                <?php if (!empty($actor['profile_path'])): ?>
                    <img src="https://image.tmdb.org/t/p/w185<?= $actor['profile_path'] ?>"
                         class="card-img-top rounded-top" alt="<?= esc($actor['name']) ?>">
                <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded-top"
                         style="height:140px;">
                        <span class="text-muted">No Photo</span>
                    </div>
                <?php endif; ?>
                <div class="card-body p-2">
                    <p class="mb-0 text-light small fw-bold"><?= esc($actor['name']) ?></p>
                    <p class="mb-0 text-muted" style="font-size:0.75rem;"><?= esc($actor['character']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Reviews -->
<div class="mb-5">
    <h3 class="text-white mb-3">💬 Reviews</h3>

    <?php if (session()->get('is_logged_in')): ?>
    <div class="card border-0 mb-4 p-3" style="background:#1e1e1e;">
        <h5 class="text-warning mb-3">Write a Review</h5>
        <div id="reviewAlert"></div>
        <div class="mb-3">
            <label class="form-label text-light">Rating</label>
            <select id="reviewRating" class="form-select bg-dark text-light border-secondary">
                <option value="10">10 ⭐ — Masterpiece</option>
                <option value="9">9 ⭐ — Excellent</option>
                <option value="8">8 ⭐ — Great</option>
                <option value="7" selected>7 ⭐ — Good</option>
                <option value="6">6 ⭐ — Fine</option>
                <option value="5">5 ⭐ — Average</option>
                <option value="4">4 ⭐ — Below Average</option>
                <option value="3">3 ⭐ — Bad</option>
                <option value="2">2 ⭐ — Terrible</option>
                <option value="1">1 ⭐ — Unwatchable</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label text-light">Your Review</label>
            <textarea id="reviewComment" class="form-control bg-dark text-light border-secondary"
                      rows="3" placeholder="Share your thoughts..."></textarea>
        </div>
        <button id="submitReview" class="btn btn-warning fw-bold">Submit Review</button>
    </div>
    <?php else: ?>
    <div class="alert alert-secondary">
        <a href="<?= base_url('user/login') ?>" class="text-warning">Login</a> to write a review.
    </div>
    <?php endif; ?>

    <div id="reviewsList">
        <?php if (empty($reviews)): ?>
            <p class="text-muted">No reviews yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <div class="card border-0 mb-3 p-3" style="background:#1e1e1e;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-warning fw-bold"><?= esc($review['username'] ?? 'User') ?></span>
                    <div>
                        <span class="badge bg-warning text-dark"><?= $review['rating'] ?> ★</span>
                        <span class="text-muted small ms-2">
                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                        </span>
                    </div>
                </div>
                <p class="text-light mb-0"><?= esc($review['comment']) ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const movieId = <?= (int)$movie['id'] ?>;
const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

// ── Save to localStorage (recently viewed) ────────────────────
if (window.saveRecentlyViewed) {
    saveRecentlyViewed({
        id:     <?= (int)$movie['id'] ?>,
        title:  <?= json_encode($movie['title'] ?? '') ?>,
        poster: <?= json_encode($movie['poster_path'] ?? null) ?>,
        rating: <?= json_encode(number_format($movie['vote_average'] ?? 0, 1)) ?>
    });
}

// ── Watchlist toggle ──────────────────────────────────────────
const wBtn = document.getElementById('watchlistBtn');
if (wBtn) {
    wBtn.addEventListener('click', function () {
        fetch(baseUrl + 'movie/toggleWatchlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'movie_id=' + movieId + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    wBtn.textContent = '✅ In Watchlist';
                    wBtn.className = 'btn btn-success mt-2';
                } else {
                    wBtn.textContent = '+ Add to Watchlist';
                    wBtn.className = 'btn btn-outline-warning mt-2';
                }
            }
        });
    });
}

// ── Submit review ─────────────────────────────────────────────
const submitBtn = document.getElementById('submitReview');
if (submitBtn) {
    submitBtn.addEventListener('click', function () {
        const rating  = document.getElementById('reviewRating').value;
        const comment = document.getElementById('reviewComment').value.trim();
        const alertEl = document.getElementById('reviewAlert');

        if (!comment) {
            alertEl.innerHTML = '<div class="alert alert-danger">Please write a review before submitting.</div>';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        fetch(baseUrl + 'movie/addReview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'movie_id=' + movieId
                + '&rating=' + rating
                + '&comment=' + encodeURIComponent(comment)
                + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alertEl.innerHTML = '<div class="alert alert-success">Review posted!</div>';
                document.getElementById('reviewComment').value = '';

                const list = document.getElementById('reviewsList');
                const empty = list.querySelector('p.text-muted');
                if (empty) empty.remove();

                const div = document.createElement('div');
                div.className = 'card border-0 mb-3 p-3';
                div.style.background = '#1e1e1e';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-warning fw-bold"><?= esc(session()->get('username') ?? 'You') ?></span>
                        <div>
                            <span class="badge bg-warning text-dark">${rating} ★</span>
                            <span class="text-muted small ms-2">Just now</span>
                        </div>
                    </div>
                    <p class="text-light mb-0">${comment.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</p>`;
                list.insertBefore(div, list.firstChild);
            } else {
                alertEl.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to post review.'}</div>`;
            }
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Review';
        });
    });
}
</script>

<?= $this->include('layout/footer') ?>
