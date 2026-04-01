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

<?php if ($backdrop): ?>
<div style="background: linear-gradient(to bottom, rgba(18,18,18,0) 0%, var(--bg-main) 100%),
            url('<?= $backdrop ?>') center/cover no-repeat;
            height: 280px; margin: -1.5rem -12px 2rem; padding: 2rem;">
</div>
<?php endif; ?>

<div class="row mb-5">
    <div class="col-md-3 text-center mb-3">
        <img src="<?= $poster ?>" alt="<?= esc($movie['title']) ?>"
             class="img-fluid rounded shadow" style="max-height:400px;">
    </div>
    <div class="col-md-9">
        <h1 class="fw-bold" style="color: var(--text-main);">
            <?= esc($movie['title']) ?>
            <span style="color: var(--text-muted);" class="fs-4">(<?= substr($movie['release_date'] ?? '----', 0, 4) ?>)</span>
        </h1>

        <?php if (!empty($movie['tagline'])): ?>
            <p class="text-warning fst-italic mb-2">"<?= esc($movie['tagline']) ?>"</p>
        <?php endif; ?>

        <div class="mb-3">
            <?php foreach ($movie['genres'] ?? [] as $genre): ?>
                <span class="badge bg-secondary me-1"><?= esc($genre['name']) ?></span>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-4 mb-3 flex-wrap">
            <div class="text-center">
                <div class="text-warning fw-bold fs-4"><?= number_format($movie['vote_average'] ?? 0, 1) ?> ★</div>
                <div style="color: var(--text-muted);" class="small"><?= number_format($movie['vote_count'] ?? 0) ?> votes</div>
            </div>
            <div class="text-center">
                <div class="fw-bold fs-4" style="color: var(--text-main);"><?= $movie['runtime'] ?? '?' ?> min</div>
                <div style="color: var(--text-muted);" class="small">Runtime</div>
            </div>
            <?php if (!empty($movie['budget']) && $movie['budget'] > 0): ?>
            <div class="text-center">
                <div class="fw-bold fs-5" style="color: var(--text-main);">$<?= number_format($movie['budget'] / 1000000, 1) ?>M</div>
                <div style="color: var(--text-muted);" class="small">Budget</div>
            </div>
            <?php endif; ?>
            <?php if (!empty($movie['revenue']) && $movie['revenue'] > 0): ?>
            <div class="text-center">
                <div class="text-success fw-bold fs-5">$<?= number_format($movie['revenue'] / 1000000, 1) ?>M</div>
                <div style="color: var(--text-muted);" class="small">Revenue</div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($directors)): ?>
        <p style="color: var(--text-muted);" class="mb-2">
            <span style="color: var(--text-main);">Directed by:</span>
            <?= esc(implode(', ', array_column(array_values($directors), 'name'))) ?>
        </p>
        <?php endif; ?>

        <p style="color: var(--text-main); line-height:1.7;" class="mt-2"><?= esc($movie['overview'] ?? '') ?></p>

        <?php if (session()->get('is_logged_in')): ?>
            <button id="watchlistBtn"
                    class="btn <?= $inWatchlist ? 'btn-success' : 'btn-outline-warning' ?> mt-2"
                    data-movie-id="<?= $movie['id'] ?>">
                <?= $inWatchlist ? '✅ In Watchlist' : '+ Add to Watchlist' ?>
            </button>
        <?php else: ?>
            <a href="<?= base_url('user/login') ?>" class="btn btn-outline-warning mt-2">Login to add to Watchlist</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($trailer): ?>
<div class="mb-5">
    <h3 class="mb-3" style="color: var(--text-main);">🎬 Trailer</h3>
    <div class="ratio ratio-16x9" style="max-width:720px;">
        <iframe src="https://www.youtube.com/embed/<?= $trailer ?>" allowfullscreen title="Trailer"></iframe>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($cast)): ?>
<div class="mb-5">
    <h3 class="mb-3" style="color: var(--text-main);">🎭 Cast</h3>
    <div class="row g-3">
        <?php foreach ($cast as $actor): ?>
        <div class="col-6 col-md-3">
            <div class="card border-0 text-center movie-card-hover">
                <?php if (!empty($actor['profile_path'])): ?>
                    <img src="https://image.tmdb.org/t/p/w185<?= $actor['profile_path'] ?>"
                         class="card-img-top rounded-top" alt="<?= esc($actor['name']) ?>">
                <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center rounded-top" style="height:140px;">
                        <span style="color: var(--text-muted);">No Photo</span>
                    </div>
                <?php endif; ?>
                <div class="card-body p-2">
                    <p class="mb-0 small fw-bold" style="color: var(--text-main);"><?= esc($actor['name']) ?></p>
                    <p class="mb-0" style="color: var(--text-muted); font-size:0.75rem;"><?= esc($actor['character']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="mb-5">
    <h3 class="mb-3" style="color: var(--text-main);">💬 Reviews</h3>

    <?php if (session()->get('is_logged_in')): ?>
    <div class="card border-0 mb-4 p-3">
        <h5 class="text-warning mb-3">Write a Review</h5>
        <div id="reviewAlert"></div>
        <div class="mb-3">
            <label class="form-label" style="color: var(--text-main);">Rating</label>
            <select id="reviewRating" class="form-select" style="background: var(--bg-input); color: var(--text-main); border-color: var(--border);">
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
            <label class="form-label" style="color: var(--text-main);">
                Your Review
                <span id="charCount" style="color: var(--text-muted); font-size:0.8rem; margin-left:8px;">0 / 500</span>
            </label>
            <textarea id="reviewComment" class="form-control" rows="3"
                      style="background: var(--bg-input); color: var(--text-main); border-color: var(--border);"
                      placeholder="Share your thoughts..." maxlength="500"></textarea>
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
            <p style="color: var(--text-muted);">No reviews yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <div class="card border-0 mb-3 p-3" id="review-<?= $review['id'] ?>">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-warning fw-bold"><?= esc($review['username'] ?? 'User') ?></span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark"><?= $review['rating'] ?> ★</span>
                        <span style="color: var(--text-muted);" class="small">
                            <?= date('M d, Y', strtotime($review['created_at'])) ?>
                        </span>
                        <?php if (session()->get('user_id') == $review['user_id']): ?>
                            <button class="btn btn-outline-danger btn-sm delete-review-btn"
                                    data-review-id="<?= $review['id'] ?>">🗑</button>
                        <?php endif; ?>
                    </div>
                </div>
                <p style="color: var(--text-main);" class="mb-0"><?= esc($review['comment']) ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const movieId = <?= (int)$movie['id'] ?>;
const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

if (window.saveRecentlyViewed) {
    saveRecentlyViewed({
        id:     <?= (int)$movie['id'] ?>,
        title:  <?= json_encode($movie['title'] ?? '') ?>,
        poster: <?= json_encode($movie['poster_path'] ?? null) ?>,
        rating: <?= json_encode(number_format($movie['vote_average'] ?? 0, 1)) ?>
    });
}

const commentBox = document.getElementById('reviewComment');
const charCount  = document.getElementById('charCount');
if (commentBox && charCount) {
    commentBox.addEventListener('input', function () {
        const len = this.value.length;
        charCount.textContent = `${len} / 500`;
        charCount.style.color = len > 450 ? '#dc3545' : 'var(--text-muted)';
    });
}

const wBtn = document.getElementById('watchlistBtn');
if (wBtn) {
    wBtn.addEventListener('click', function () {
        fetch(baseUrl + 'movie/toggleWatchlist', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'movie_id=' + movieId + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    wBtn.textContent = '✅ In Watchlist';
                    wBtn.className = 'btn btn-success mt-2';
                    if (window.showToast) showToast('Added to your watchlist!', 'success');
                } else {
                    wBtn.textContent = '+ Add to Watchlist';
                    wBtn.className = 'btn btn-outline-warning mt-2';
                    if (window.showToast) showToast('Removed from watchlist.', 'danger');
                }
            }
        });
    });
}

const submitBtn = document.getElementById('submitReview');
if (submitBtn) {
    submitBtn.addEventListener('click', function () {
        const rating  = document.getElementById('reviewRating').value;
        const comment = commentBox.value.trim();
        const alertEl = document.getElementById('reviewAlert');

        if (!comment) {
            alertEl.innerHTML = '<div class="alert alert-danger">Please write a review before submitting.</div>';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        fetch(baseUrl + 'movie/addReview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'movie_id=' + movieId + '&rating=' + rating + '&comment=' + encodeURIComponent(comment)
                + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alertEl.innerHTML = '';
                commentBox.value = '';
                charCount.textContent = '0 / 500';
                if (window.showToast) showToast('Review posted!', 'success');

                const list  = document.getElementById('reviewsList');
                const empty = list.querySelector('p');
                if (empty && empty.textContent.includes('No reviews')) empty.remove();

                const div = document.createElement('div');
                div.className = 'card border-0 mb-3 p-3';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-warning fw-bold"><?= esc(session()->get('username') ?? 'You') ?></span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark">${rating} ★</span>
                            <span style="color:var(--text-muted);" class="small">Just now</span>
                        </div>
                    </div>
                    <p style="color:var(--text-main);" class="mb-0">${comment.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</p>`;
                list.insertBefore(div, list.firstChild);
            } else {
                alertEl.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed.'}</div>`;
            }
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Review';
        });
    });
}

document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('delete-review-btn')) return;
    const reviewId = e.target.dataset.reviewId;
    if (!confirm('Delete this review?')) return;

    fetch(baseUrl + 'movie/deleteReview/' + reviewId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: '<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById('review-' + reviewId);
            card.style.transition = 'opacity 0.3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 300);
            if (window.showToast) showToast('Review deleted.', 'danger');
        }
    });
});
</script>

<?= $this->include('layout/footer') ?>
