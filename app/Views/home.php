<?= $this->include('layout/header') ?>

<h1 class="mb-4 text-white">Trending Movies This Week</h1>

<div class="row" id="movieGrid">
    <?php if (empty($movies)): ?>
        <div class="col-12">
            <div class="alert alert-warning">Unable to load movies. Please check your API key.</div>
        </div>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="col-md-3 col-sm-6 mb-4 movie-card">
                <a href="<?= base_url('movie/detail/' . $movie['id']) ?>" class="text-decoration-none">
                    <div class="card h-100 bg-secondary border-0 shadow">
                        <?php if (!empty($movie['poster_path'])): ?>
                            <img src="https://image.tmdb.org/t/p/w500<?= $movie['poster_path'] ?>" 
                                 class="card-img-top" alt="<?= esc($movie['title']) ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 300px;">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= esc($movie['title']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= substr($movie['overview'] ?? 'No description available.', 0, 90) ?>...
                            </p>
                            <span class="badge bg-warning fs-6">
                                <?= number_format($movie['vote_average'] ?? 0, 1) ?> ★
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->include('layout/footer') ?>