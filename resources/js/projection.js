import { PPTXViewer } from 'pptxviewjs';

let viewer = null;
let loadedUrl = null;
let currentSlide = 1;

function getCanvas() {
    return document.getElementById('pptx-projection-canvas');
}

function ensureViewer() {
    const canvas = getCanvas();
    if (!canvas) return null;

    if (!viewer) {
        viewer = new PPTXViewer({
            canvas,
            autoExposeGlobals: true,
        });

        viewer.on('loadComplete', async () => {
            try {
                await viewer.renderSlide(Math.max(0, currentSlide - 1), canvas);
            } catch (e) {
                console.error('PPTX render error:', e);
            }
        });
    }

    return viewer;
}

async function load(url) {
    const v = ensureViewer();
    const canvas = getCanvas();
    if (!v || !canvas) return;

    if (loadedUrl === url) return;

    loadedUrl = url;
    try {
        await v.loadFromUrl(url);
    } catch (e) {
        console.error('PPTX loadFromUrl error:', e);
        const container = canvas.parentElement;
        if (container) {
            container.innerHTML = `<div style="width: 100%; height: 100%; display:grid; place-items:center; color:#fff; font-family: Inter, system-ui;">Erreur de chargement PPTX</div>`;
        }
    }
}

async function renderSlide(page) {
    const v = ensureViewer();
    const canvas = getCanvas();
    if (!v || !canvas) return;

    currentSlide = Math.max(1, Number(page || 1));

    try {
        await v.renderSlide(currentSlide - 1, canvas);
    } catch (e) {
        console.error('PPTX renderSlide error:', e);
    }
}

function reset() {
    viewer = null;
    loadedUrl = null;
    currentSlide = 1;
}

window.PptxProjection = {
    load,
    renderSlide,
    reset,
};
