<?= $this->include('layout/header') ?>

<!-- Recently Viewed (localStorage) -->
<div id="recentlyViewedSection" style="display:none;" class="mb-5">
    <h4 class="text-warning mb-3">🕐 Recently Viewed</h4>
    <div class="row" id="recentlyViewedGrid"></div>
    <hr class="border-secondary mt-2">
</div>

<h1 class="mb-4 text-white">🔥 Trending This Week</h1>

<div class="row" id="movieGrid">
    <?php if (empty($movies)): ?>
        <div class="col-12">
            <div class="alert alert-warning">Unable to load movies. Please check your API key.</div>
        </div>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="col-md-3 col-sm-6 mb-4 movie-card">
                <a href="<?= base_url('movie/detail/' . $movie['id']) ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow" style="background:#1e1e1e;">
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>"
                                 class="card-img-top" alt="<?= esc($movie['title']) ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-secondary"
                                 style="height:300px;">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-light"><?= esc($movie['title']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= substr($movie['overview'] ?? 'No description available.', 0, 90) ?>...
                            </p>
                            <span class="badge bg-warning text-dark fs-6">
                                <?= number_format($movie['vote_average'] ?? 0, 1) ?> ★
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// ── Recently Viewed (localStorage) ───────────────────────────────
const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');

function loadRecentlyViewed() {
    const recent = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    if (recent.length === 0) return;

    const section = document.getElementById('recentlyViewedSection');
    const grid    = document.getElementById('recentlyViewedGrid');
    section.style.display = 'block';
    grid.innerHTML = '';

    recent.slice(0, 4).forEach(movie => {
        const poster = movie.poster
            ? `https://image.tmdb.org/t/p/w500${movie.poster}`
            : 'https://via.placeholder.com/300x450?text=No+Poster';

        grid.innerHTML += `
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="${baseUrl}movie/detail/${movie.id}" class="text-decoration-none">
                    <div class="card border-0 h-100" style="background:#1e1e1e;">
                        <img src="${poster}" class="card-img-top" alt="${movie.title}">
                        <div class="card-body p-2">
                            <p class="text-light small mb-0 fw-bold">${movie.title}</p>
                            <span class="badge bg-warning text-dark">${movie.rating} ★</span>
                        </div>
                    </div>
                </a>
            </div>`;
    });
}

loadRecentlyViewed();
</script>

<?= $this->include('layout/footer') ?>
