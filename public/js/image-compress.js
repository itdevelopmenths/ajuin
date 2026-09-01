/**
 * Kompres foto di sisi browser sebelum di-upload, supaya tidak kena limit
 * ukuran file di server (lampiran ticket dibatasi maks. 2 MB per file).
 * File non-gambar (mis. PDF) dan gambar yang sudah cukup kecil dibiarkan apa adanya.
 */
(function (window) {
    const DEFAULT_OPTIONS = {
        maxSizeBytes: 1.8 * 1024 * 1024, // target di bawah limit server (2 MB)
        maxDimension: 1920,
        mimeType: 'image/jpeg',
        initialQuality: 0.85,
        minQuality: 0.4,
    };

    function loadImageSource(file) {
        if ('createImageBitmap' in window) {
            return createImageBitmap(file, { imageOrientation: 'from-image' })
                .catch(() => createImageBitmap(file))
                .catch(() => loadViaImageElement(file));
        }
        return loadViaImageElement(file);
    }

    function loadViaImageElement(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => { URL.revokeObjectURL(url); resolve(img); };
            img.onerror = (err) => { URL.revokeObjectURL(url); reject(err); };
            img.src = url;
        });
    }

    function getDimensions(source) {
        return {
            width: source.width || source.naturalWidth,
            height: source.height || source.naturalHeight,
        };
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise((resolve) => canvas.toBlob(resolve, type, quality));
    }

    async function compressImage(file, userOptions) {
        const options = Object.assign({}, DEFAULT_OPTIONS, userOptions);

        if (!file || !file.type || !file.type.startsWith('image/') || file.type === 'image/gif') {
            return file;
        }
        if (file.size <= options.maxSizeBytes) {
            return file;
        }

        let source;
        try {
            source = await loadImageSource(file);
        } catch (err) {
            return file; // gagal decode — pakai file asli, biar validasi server yang menolak
        }

        let { width, height } = getDimensions(source);
        if (!width || !height) {
            if (source.close) source.close();
            return file;
        }

        if (width > options.maxDimension || height > options.maxDimension) {
            const scale = options.maxDimension / Math.max(width, height);
            width = Math.round(width * scale);
            height = Math.round(height * scale);
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(source, 0, 0, width, height);
        if (source.close) source.close();

        let quality = options.initialQuality;
        let blob = await canvasToBlob(canvas, options.mimeType, quality);

        while (blob && blob.size > options.maxSizeBytes && quality > options.minQuality) {
            quality -= 0.1;
            blob = await canvasToBlob(canvas, options.mimeType, quality);
        }

        if (!blob || blob.size >= file.size) {
            return file; // kompresi tidak membantu — pakai file asli
        }

        const newName = file.name.replace(/\.\w+$/, '') + '.jpg';
        return new File([blob], newName, { type: options.mimeType, lastModified: Date.now() });
    }

    window.compressImage = compressImage;
})(window);
