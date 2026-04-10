import { PPTXViewer } from 'pptxviewjs';

let previewViewer = null;
let previewLoadedUrl = null;
let previewCurrentSlide = 1;

function ensurePreviewViewer() {
    const canvas = document.getElementById('pptx-preview-canvas');
    if (!canvas) return null;

    if (!previewViewer) {
        previewViewer = new PPTXViewer({
            canvas,
            autoExposeGlobals: true,
        });

        previewViewer.on('loadComplete', async () => {
            try {
                await previewViewer.renderSlide(Math.max(0, previewCurrentSlide - 1), canvas);
            } catch (e) {
                console.error('PPTX preview render error:', e);
            }
        });
    }

    return previewViewer;
}

async function loadPreview(url) {
    const v = ensurePreviewViewer();
    const canvas = document.getElementById('pptx-preview-canvas');
    if (!v || !canvas) return;

    if (previewLoadedUrl === url) return;

    previewLoadedUrl = url;
    try {
        await v.loadFromUrl(url);
    } catch (e) {
        console.error('PPTX preview loadFromUrl error:', e);
        const container = canvas.parentElement;
        if (container) {
            container.innerHTML = `<div style="width: 100%; height: 100%; display:grid; place-items:center; color:#94a3b8; font-family: Inter, system-ui; font-size:0.875rem;">Erreur de chargement PPTX</div>`;
        }
    }
}

async function renderPreviewSlide(page) {
    const v = ensurePreviewViewer();
    const canvas = document.getElementById('pptx-preview-canvas');
    if (!v || !canvas) return;

    previewCurrentSlide = Math.max(1, Number(page || 1));

    try {
        await v.renderSlide(previewCurrentSlide - 1, canvas);
    } catch (e) {
        console.error('PPTX preview renderSlide error:', e);
    }
}

function resetPreview() {
    previewViewer = null;
    previewLoadedUrl = null;
    previewCurrentSlide = 1;
}

window.PptxPreview = {
    load: loadPreview,
    renderSlide: renderPreviewSlide,
    reset: resetPreview,
};
