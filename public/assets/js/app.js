document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const movieGrid   = document.getElementById('movieGrid');
    const baseMeta    = document.querySelector('meta[name="base-url"]');
    const baseUrl     = baseMeta ? baseMeta.getAttribute('content') : '/';

    if (!searchInput) return;

    // ── Create live search dropdown ───────────────────────────────
    const dropdown = document.createElement('div');
    dropdown.id = 'searchDropdown';
    dropdown.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #1e1e1e;
        border: 1px solid #444;
        border-radius: 0 0 8px 8px;
        z-index: 9999;
        max-height: 420px;
        overflow-y: auto;
        display: none;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    `;

    const wrapper = searchInput.closest('form') || searchInput.parentNode;
    wrapper.style.position = 'relative';
    wrapper.appendChild(dropdown);

    // Hover style
    const style = document.createElement('style');
    style.textContent = `.dropdown-item-hover:hover { background: #2a2a2a !important; }`;
    document.head.appendChild(style);

    let timeout = null;
    let currentQuery = '';
    let originalGrid = movieGrid ? movieGrid.innerHTML : '';

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        currentQuery = query;
        clearTimeout(timeout);

        // If search is empty — hide dropdown and restore original grid
        if (query.length === 0) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            if (movieGrid && originalGrid) {
                movieGrid.innerHTML = originalGrid;
            }
            return;
        }

        // If less than 2 chars — just hide dropdown, do NOT reload
        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        // Show loading in dropdown
        dropdown.style.display = 'block';
        dropdown.innerHTML = `<div class="p-3 text-muted text-center">🔍 Searching...</div>`;

        timeout = setTimeout(() => {
            fetch(`${baseUrl}movie/search?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    if (query !== currentQuery) return; // discard stale response

                    if (data.length === 0) {
                        dropdown.innerHTML = `<div class="p-3 text-muted text-center">No results for "<strong>${query}</strong>"</div>`;
                        return;
                    }

                    // Update the main grid if it exists
                    if (movieGrid) {
                        let gridHtml = '';
                        data.forEach(movie => {
                            const poster = movie.poster_path
                                ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
                                : 'https://via.placeholder.com/300x450?text=No+Poster';
                            gridHtml += `
                                <div class="col-md-3 col-sm-6 mb-4 movie-card">
                                    <a href="${baseUrl}movie/detail/${movie.id}" class="text-decoration-none">
                                        <div class="card h-100 border-0 shadow" style="background:#1e1e1e;">
                                            <img src="${poster}" class="card-img-top" alt="${movie.title}">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title text-light">${movie.title}</h5>
                                                <span class="badge bg-warning text-dark fs-6">
                                                    ${movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A'} ★
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>`;
                        });
                        movieGrid.innerHTML = gridHtml;
                    }

                    // Build dropdown (top 6 results)
                    let html = '';
                    data.slice(0, 6).forEach(movie => {
                        const poster = movie.poster_path
                            ? `https://image.tmdb.org/t/p/w92${movie.poster_path}`
                            : null;
                        const year   = movie.release_date ? movie.release_date.substring(0, 4) : '';
                        const rating = movie.vote_average ? movie.vote_average.toFixed(1) : 'N/A';

                        html += `
                            <a href="${baseUrl}movie/detail/${movie.id}"
                               class="d-flex align-items-center gap-3 p-2 text-decoration-none dropdown-item-hover"
                               style="border-bottom:1px solid #333; color:#ddd;">
                                ${poster
                                    ? `<img src="${poster}" style="width:42px;height:63px;object-fit:cover;border-radius:4px;flex-shrink:0;" alt="">`
                                    : `<div style="width:42px;height:63px;background:#333;border-radius:4px;flex-shrink:0;"></div>`
                                }
                                <div style="overflow:hidden;">
                                    <div class="fw-bold text-truncate">${movie.title}</div>
                                    <div class="text-muted small">${year} &nbsp;·&nbsp; ⭐ ${rating}</div>
                                </div>
                            </a>`;
                    });

                    if (data.length > 6) {
                        html += `<div class="p-2 text-center text-muted small">+${data.length - 6} more results shown below</div>`;
                    }

                    dropdown.innerHTML = html;
                })
                .catch(err => {
                    console.error('Search error:', err);
                    dropdown.innerHTML = `<div class="p-3 text-danger">Search failed. Please try again.</div>`;
                });
        }, 400); // wait 400ms after user stops typing
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Re-open on focus if query exists
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2 && dropdown.innerHTML) {
            dropdown.style.display = 'block';
        }
    });
});

// ── localStorage: Recently Viewed ────────────────────────────────
function saveRecentlyViewed(movie) {
    try {
        let recent = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
        recent = recent.filter(m => m.id !== movie.id); // remove duplicate
        recent.unshift(movie);                           // add to front
        recent = recent.slice(0, 8);                    // keep max 8
        localStorage.setItem('recentlyViewed', JSON.stringify(recent));
    } catch(e) {
        console.warn('localStorage not available:', e);
    }
}

window.saveRecentlyViewed = saveRecentlyViewed;
