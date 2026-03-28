document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const movieGrid   = document.getElementById('movieGrid');

    // Only run search logic if these elements exist (not on login/register pages)
    if (!searchInput || !movieGrid) return;

    // Read base URL from a <meta> tag in the header (no PHP needed here)
    const baseMeta = document.querySelector('meta[name="base-url"]');
    const baseUrl  = baseMeta ? baseMeta.getAttribute('content') : '/';

    let timeout = null;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value.trim();

            if (query.length < 2) {
                location.reload();
                return;
            }

            fetch(`${baseUrl}movie/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    let html = '';

                    if (data.length === 0) {
                        html = `<div class="col-12"><p class="text-warning">No movies found for "${query}"</p></div>`;
                    } else {
                        data.forEach(movie => {
                            const poster = movie.poster_path
                                ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                                : 'https://via.placeholder.com/300x450?text=No+Poster';

                            html += `
                                <div class="col-md-3 col-sm-6 mb-4 movie-card">
                                    <a href="${baseUrl}movie/detail/${movie.id}" class="text-decoration-none">
                                        <div class="card h-100 bg-secondary border-0 shadow">
                                            <img src="${poster}" class="card-img-top" alt="${movie.title}">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title">${movie.title}</h5>
                                                <span class="badge bg-warning fs-6">${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'} ★</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>`;
                        });
                    }

                    movieGrid.innerHTML = html;
                })
                .catch(err => console.error('Search error:', err));
        }, 400);
    });
});
