/**
 * Kamera custom di dalam halaman (bukan kamera native HP) supaya foto bisa
 * distempel timestamp + koordinat GPS langsung ke pixel-nya sebelum diupload.
 * Butuh HTTPS (atau localhost) — getUserMedia & geolocation ditolak browser
 * di koneksi HTTP biasa.
 */
(function (window) {
    let activeStream = null;

    function pad(n) { return String(n).padStart(2, '0'); }

    function formatTimestamp(date) {
        return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
    }

    function getLocation(timeoutMs) {
        return new Promise((resolve) => {
            if (!('geolocation' in navigator)) {
                resolve(null);
                return;
            }
            let settled = false;
            const timer = setTimeout(() => {
                if (!settled) { settled = true; resolve(null); }
            }, timeoutMs);
            navigator.geolocation.getCurrentPosition(
                (pos) => { if (!settled) { settled = true; clearTimeout(timer); resolve(pos.coords); } },
                () => { if (!settled) { settled = true; clearTimeout(timer); resolve(null); } },
                { enableHighAccuracy: true, timeout: timeoutMs, maximumAge: 30000 }
            );
        });
    }

    function stopStream() {
        if (activeStream) {
            activeStream.getTracks().forEach(t => t.stop());
            activeStream = null;
        }
    }

    function stampCanvas(ctx, canvas, label) {
        let fontSize = Math.max(14, Math.round(canvas.width / 34));
        ctx.font = `600 ${fontSize}px sans-serif`;
        let textWidth = ctx.measureText(label).width;
        const maxTextWidth = canvas.width - fontSize * 1.6;
        if (textWidth > maxTextWidth && textWidth > 0) {
            fontSize = Math.max(10, Math.floor(fontSize * maxTextWidth / textWidth));
            ctx.font = `600 ${fontSize}px sans-serif`;
        }

        const paddingX = fontSize * 0.8;
        const barHeight = fontSize * 2.3;
        ctx.fillStyle = 'rgba(0,0,0,0.55)';
        ctx.fillRect(0, canvas.height - barHeight, canvas.width, barHeight);
        ctx.fillStyle = '#ffffff';
        ctx.textBaseline = 'middle';
        ctx.fillText(label, paddingX, canvas.height - barHeight / 2);
    }

    /**
     * Buka overlay kamera. Mengembalikan Promise<File|null> — null kalau user batal
     * atau kamera tidak bisa diakses.
     */
    function openGeoCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert('Kamera tidak bisa diakses di browser/koneksi ini (butuh HTTPS).');
            return Promise.resolve(null);
        }

        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:#000;z-index:9999;display:flex;flex-direction:column;';
            overlay.innerHTML = `
                <div style="position:relative;flex:1;overflow:hidden;background:#000;">
                    <video id="geocam-video" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>
                    <div id="geocam-status" style="position:absolute;top:.75rem;left:.75rem;right:.75rem;color:#fff;font-size:.75rem;background:rgba(0,0,0,.45);padding:.5rem .75rem;border-radius:.5rem;font-family:sans-serif;line-height:1.4;">Mengaktifkan kamera…</div>
                </div>
                <div style="display:flex;align-items:center;justify-content:center;gap:2rem;padding:1.25rem;background:#111;">
                    <button id="geocam-cancel" type="button" style="background:none;border:none;color:#fff;font-size:.875rem;font-weight:600;padding:.5rem 1rem;cursor:pointer;font-family:sans-serif;">Batal</button>
                    <button id="geocam-shutter" type="button" aria-label="Ambil foto" style="width:64px;height:64px;border-radius:50%;background:#fff;border:4px solid #6b7280;cursor:pointer;"></button>
                    <button id="geocam-switch" type="button" style="background:none;border:none;color:#fff;font-size:.875rem;font-weight:600;padding:.5rem 1rem;cursor:pointer;font-family:sans-serif;">Ganti Kamera</button>
                </div>
            `;
            document.body.appendChild(overlay);

            const video = overlay.querySelector('#geocam-video');
            const statusEl = overlay.querySelector('#geocam-status');
            const shutterBtn = overlay.querySelector('#geocam-shutter');
            const cancelBtn = overlay.querySelector('#geocam-cancel');
            const switchBtn = overlay.querySelector('#geocam-switch');

            let facingMode = 'environment';
            let closed = false;
            const locationPromise = getLocation(8000);

            locationPromise.then((coords) => {
                if (closed) return;
                statusEl.textContent = coords
                    ? `📍 ${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)} — siap memotret`
                    : '⚠ Lokasi tidak tersedia — siap memotret';
            });

            function cleanup(result) {
                if (closed) return;
                closed = true;
                stopStream();
                overlay.remove();
                resolve(result);
            }

            async function startStream() {
                stopStream();
                try {
                    activeStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: facingMode } },
                        audio: false,
                    });
                    video.srcObject = activeStream;
                } catch (err) {
                    statusEl.textContent = 'Gagal mengakses kamera: ' + err.message;
                }
            }

            cancelBtn.addEventListener('click', () => cleanup(null));
            switchBtn.addEventListener('click', () => {
                facingMode = facingMode === 'environment' ? 'user' : 'environment';
                startStream();
            });

            shutterBtn.addEventListener('click', async () => {
                if (!video.videoWidth) return;
                shutterBtn.disabled = true;
                statusEl.textContent = 'Memproses foto…';

                const coords = await locationPromise;
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const now = new Date();
                const label = coords
                    ? `${formatTimestamp(now)}  •  ${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`
                    : `${formatTimestamp(now)}  •  Lokasi tidak tersedia`;
                stampCanvas(ctx, canvas, label);

                canvas.toBlob((blob) => {
                    if (!blob) {
                        cleanup(null);
                        return;
                    }
                    const file = new File([blob], `geotag-${now.getTime()}.jpg`, { type: 'image/jpeg', lastModified: now.getTime() });
                    cleanup(file);
                }, 'image/jpeg', 0.92);
            });

            startStream();
        });
    }

    window.openGeoCamera = openGeoCamera;
})(window);
