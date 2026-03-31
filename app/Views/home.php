<?= $this->include('layout/header') ?>

<!-- Recently Viewed (localStorage) -->
<div id="recentlyViewedSection" style="display:none;" class="mb-5">
    <h4 class="text-warning mb-3">🕐 Recently Viewed</h4>
    <div class="row" id="recentlyViewedGrid"></div>
    <hr class="border-secondary mt-2">
</div>

<!-- Genre Filter -->
<div class="mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <button class="btn btn-warning btn-sm genre-btn active-genre fw-bold"
                data-genre="trending">🔥 Trending</button>
        <?php foreach ($genres as $genre): ?>
            <button class="btn btn-sm genre-btn"
                    style="background:#1e1e1e; color:var(--text-main); border:1px solid #444;"
                    data-genre="<?= $genre['id'] ?>">
                <?= esc($genre['name']) ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Section title -->
<h1 class="mb-4" id="sectionTitle" style="color: var(--text-main);">🔥 Trending This Week</h1>

<!-- Loading spinner -->
<div id="loadingSpinner" style="display:none;" class="text-center py-5">
    <div class="spinner-border text-warning" role="status" style="width:3rem;height:3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-warning mt-3">Loading movies...</p>
</div>

<!-- Movie Grid -->
<div class="row" id="movieGrid">
    <?php if (empty($movies)): ?>
        <div class="col-12">
            <div class="alert alert-warning">Unable to load movies. Please check your API key.</div>
        </div>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <a href="<?= base_url('movie/detail/' . $movie['id']) ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow movie-card-hover">
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>"
                                 class="card-img-top" alt="<?= esc($movie['title']) ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-secondary"
                                 style="height:300px;">
                                <span style="color:var(--text-muted);">No Image</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1" style="color:var(--text-main);"><?= esc($movie['title']) ?></h5>
                            <p class="small flex-grow-1" style="color:var(--text-muted);">
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

<style>
    .genre-btn { transition: all 0.2s; }
    .genre-btn:hover { border-color: #ffc107 !important; color: #ffc107 !important; }
    .active-genre {
        background: #ffc107 !important;
        color: #000 !important;
        border-color: #ffc107 !important;
        font-weight: bold;
    }
</style>

<script>
const baseUrl     = document.querySelector('meta[name="base-url"]').getAttribute('content');
const movieGrid   = document.getElementById('movieGrid');
const spinner     = document.getElementById('loadingSpinner');
const sectionTitle = document.getElementById('sectionTitle');

// Genre names for the title
const genreNames = {};
<?php foreach ($genres as $genre): ?>
genreNames[<?= $genre['id'] ?>] = '<?= esc($genre['name']) ?>';
<?php endforeach; ?>

// ── Genre filter buttons ──────────────────────────────────────
document.querySelectorAll('.genre-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        // Update active state
        document.querySelectorAll('.genre-btn').forEach(b => {
            b.classList.remove('active-genre');
            b.style.background = '#1e1e1e';
            b.style.color = 'var(--text-main)';
            b.style.borderColor = '#444';
        });
        this.classList.add('active-genre');
        this.style.background = '';
        this.style.color = '';
        this.style.borderColor = '';

        const genreId = this.dataset.genre;

        // Update title
        if (genreId === 'trending') {
            sectionTitle.textContent = '🔥 Trending This Week';
        } else {
            sectionTitle.textContent = '🎬 ' + (genreNames[genreId] || 'Movies');
        }

        // Show spinner
        movieGrid.style.opacity = '0.3';
        spinner.style.display = 'block';

        fetch(`${baseUrl}movie/filterGenre?genre_id=${genreId}`)
            .then(r => r.json())
            .then(data => {
                spinner.style.display = 'none';
                movieGrid.style.opacity = '1';

                if (data.length === 0) {
                    movieGrid.innerHTML = '<div class="col-12"><p style="color:var(--text-muted);">No movies found.</p></div>';
                    return;
                }

                let html = '';
                data.forEach(movie => {
                    const poster = movie.poster_path
                        ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                        : 'https://via.placeholder.com/300x450?text=No+Poster';
                    const overview = (movie.overview || 'No description available.').substring(0, 90);

                    html += `
                        <div class="col-md-3 col-sm-6 mb-4">
                            <a href="${baseUrl}movie/detail/${movie.id}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow movie-card-hover">
                                    <img src="${poster}" class="card-img-top" alt="${movie.title}">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title mb-1" style="color:var(--text-main);">${movie.title}</h5>
                                        <p class="small flex-grow-1" style="color:var(--text-muted);">${overview}...</p>
                                        <span class="badge bg-warning text-dark fs-6">
                                            ${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'} ★
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>`;
                });
                movieGrid.innerHTML = html;
            })
            .catch(err => {
                spinner.style.display = 'none';
                movieGrid.style.opacity = '1';
                console.error('Genre filter error:', err);
            });
    });
});

// ── Recently Viewed (localStorage) ───────────────────────────────
function loadRecentlyViewed() {
    try {
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
                        <div class="card border-0 h-100 movie-card-hover">
                            <img src="${poster}" class="card-img-top" alt="${movie.title}">
                            <div class="card-body p-2">
                                <p class="small mb-1 fw-bold" style="color:var(--text-main);">${movie.title}</p>
                                <span class="badge bg-warning text-dark">${movie.rating} ★</span>
                            </div>
                        </div>
                    </a>
                </div>`;
        });
    } catch(e) {
        console.warn('localStorage error:', e);
    }
}

loadRecentlyViewed();
</script>

<?= $this->include('layout/footer') ?>
