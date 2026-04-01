<?= $this->include('layout/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold" style="color: var(--text-main);">📍 Cinemas Near You</h1>
    <button id="locateBtn" class="btn btn-warning fw-bold">📡 Find My Location</button>
</div>

<div id="statusMsg" class="alert alert-secondary" style="display:none;"></div>

<div id="mapContainer" style="display:none;" class="mb-4">
    <iframe id="mapFrame"
            style="width:100%; height:400px; border:0; border-radius:8px;"
            loading="lazy" allowfullscreen></iframe>
</div>

<div id="resultsArea">
    <div class="text-center py-5">
        <p class="fs-5" style="color: var(--text-muted);">Click <strong>Find My Location</strong> to discover cinemas near you.</p>
        <p class="small" style="color: var(--text-muted);">Uses your device's GPS — no data is stored.</p>
    </div>
</div>

<script>
const locateBtn   = document.getElementById('locateBtn');
const statusMsg   = document.getElementById('statusMsg');
const resultsArea = document.getElementById('resultsArea');
const mapContainer = document.getElementById('mapContainer');
const mapFrame    = document.getElementById('mapFrame');

function showStatus(msg, type = 'info') {
    statusMsg.style.display = 'block';
    statusMsg.className = `alert alert-${type}`;
    statusMsg.innerHTML = msg;
}

locateBtn.addEventListener('click', function () {
    if (!navigator.geolocation) {
        showStatus('❌ Geolocation is not supported by your browser.', 'danger');
        return;
    }

    locateBtn.disabled = true;
    locateBtn.textContent = '📡 Locating...';
    showStatus('🔍 Getting your location...', 'info');

    navigator.geolocation.getCurrentPosition(
        position => {
            const lat      = position.coords.latitude;
            const lon      = position.coords.longitude;
            const accuracy = Math.round(position.coords.accuracy);

            showStatus(`✅ Location found! Accuracy: ~${accuracy}m. Searching for cinemas...`, 'success');

            mapContainer.style.display = 'block';
            mapFrame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${lon-0.05},${lat-0.05},${lon+0.05},${lat+0.05}&layer=mapnik&marker=${lat},${lon}`;

            const overpassUrl = `https://overpass-api.de/api/interpreter?data=[out:json];(node["amenity"="cinema"](around:10000,${lat},${lon});way["amenity"="cinema"](around:10000,${lat},${lon}););out center;`;

            fetch(overpassUrl)
                .then(r => r.json())
                .then(data => {
                    const cinemas = data.elements;
                    locateBtn.disabled = false;
                    locateBtn.textContent = '📡 Find My Location';

                    if (cinemas.length === 0) {
                        resultsArea.innerHTML = `
                            <div class="alert alert-warning">
                                No cinemas found within 10km.
                                Try <a href="https://www.google.com/maps/search/cinema" target="_blank" class="alert-link">Google Maps</a>.
                            </div>`;
                        return;
                    }

                    showStatus(`✅ Found ${cinemas.length} cinema(s) near you!`, 'success');

                    function getDistance(lat1, lon1, lat2, lon2) {
                        const R = 6371;
                        const dLat = (lat2 - lat1) * Math.PI / 180;
                        const dLon = (lon2 - lon1) * Math.PI / 180;
                        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                  Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
                                  Math.sin(dLon/2) * Math.sin(dLon/2);
                        return (R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))).toFixed(1);
                    }

                    cinemas.sort((a, b) => {
                        const aLat = a.lat || a.center?.lat;
                        const aLon = a.lon || a.center?.lon;
                        const bLat = b.lat || b.center?.lat;
                        const bLon = b.lon || b.center?.lon;
                        return getDistance(lat, lon, aLat, aLon) - getDistance(lat, lon, bLat, bLon);
                    });

                    let html = '<div class="row">';
                    cinemas.forEach(cinema => {
                        const cLat    = cinema.lat || cinema.center?.lat;
                        const cLon    = cinema.lon || cinema.center?.lon;
                        const name    = cinema.tags?.name || 'Unknown Cinema';
                        const address = cinema.tags?.['addr:street']
                            ? `${cinema.tags['addr:housenumber'] || ''} ${cinema.tags['addr:street']}`.trim()
                            : 'Address not available';
                        const phone   = cinema.tags?.phone || cinema.tags?.['contact:phone'] || null;
                        const website = cinema.tags?.website || cinema.tags?.['contact:website'] || null;
                        const dist    = getDistance(lat, lon, cLat, cLon);
                        const mapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${cLat},${cLon}`;

                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 h-100">
                                <div class="card-body">
                                    <h5 class="text-warning fw-bold">🎬 ${name}</h5>
                                    <p style="color:var(--text-muted);" class="small mb-1">📍 ${address}</p>
                                    <p class="mb-2"><span class="badge bg-warning text-dark">${dist} km away</span></p>
                                    ${phone ? `<p style="color:var(--text-muted);" class="small mb-1">📞 ${phone}</p>` : ''}
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="${mapsUrl}" target="_blank" class="btn btn-warning btn-sm fw-bold">🗺️ Directions</a>
                                        ${website ? `<a href="${website}" target="_blank" class="btn btn-outline-warning btn-sm">Website</a>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    resultsArea.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    locateBtn.disabled = false;
                    locateBtn.textContent = '📡 Find My Location';
                    showStatus('❌ Could not fetch cinema data. Please try again.', 'danger');
                });
        },
        error => {
            locateBtn.disabled = false;
            locateBtn.textContent = '📡 Find My Location';
            const messages = {
                1: 'Location access denied. Please allow location access in your browser.',
                2: 'Location unavailable. Please try again.',
                3: 'Location request timed out. Please try again.',
            };
            showStatus('❌ ' + (messages[error.code] || 'Unknown error.'), 'danger');
        },
        { timeout: 10000, enableHighAccuracy: true }
    );
});
</script>

<?= $this->include('layout/footer') ?>
