<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
    Scene,
    PerspectiveCamera,
    WebGLRenderer,
    Group,
    CircleGeometry,
    TorusGeometry,
    MeshStandardMaterial,
    MeshBasicMaterial,
    Mesh,
    AmbientLight,
    DirectionalLight,
    PointLight,
} from 'three';

const containerRef = ref<HTMLDivElement | null>(null);
const webglFailed = ref(false);

let renderer: WebGLRenderer | null = null;
let scene: Scene | null = null;
let camera: PerspectiveCamera | null = null;
let animationFrameId: number | null = null;
let resizeObserver: ResizeObserver | null = null;
let intersectionObserver: IntersectionObserver | null = null;

let rootGroup: Group | null = null;
let violetDisc: Mesh | null = null;
let hairlineOutline: Mesh | null = null;

let targetRotationX = 0;
let targetRotationY = 0;
let isVisible = true;
let isTabActive = true;
let prefersReducedMotion = false;
let isTouchDevice = false;

function onPointerMove(e: MouseEvent) {
    if (prefersReducedMotion || isTouchDevice || !containerRef.value) return;

    const rect = containerRef.value.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;

    const normX = (e.clientX - centerX) / (window.innerWidth / 2);
    const normY = (e.clientY - centerY) / (window.innerHeight / 2);

    // Subtle natural perspective shift (limited to ~2.5 degrees)
    targetRotationY = Math.max(-0.045, Math.min(0.045, normX * 0.045));
    targetRotationX = Math.max(-0.035, Math.min(0.035, normY * 0.035));
}

function initThree() {
    if (!containerRef.value) return;

    if (typeof window !== 'undefined') {
        if (window.matchMedia) {
            prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            isTouchDevice = window.matchMedia('(pointer: coarse)').matches;
        }
        if ('ontouchstart' in window) {
            isTouchDevice = true;
        }
    }

    const width = containerRef.value.clientWidth || 340;
    const height = containerRef.value.clientHeight || 340;

    scene = new Scene();

    // Camera calibrated for 1:1 aspect square stage
    camera = new PerspectiveCamera(36, width / height, 0.1, 100);
    camera.position.set(0, 0, 5.0);

    try {
        renderer = new WebGLRenderer({
            alpha: true,
            antialias: true,
            powerPreference: 'low-power',
        });
    } catch {
        webglFailed.value = true;
        return;
    }

    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setClearColor(0x000000, 0);

    const canvas = renderer.domElement;
    canvas.setAttribute('aria-hidden', 'true');
    canvas.className = 'w-full h-full pointer-events-none select-none';
    containerRef.value.appendChild(canvas);

    // Warm, natural studio lighting calibrated for refined violet (#7057D9)
    const ambientLight = new AmbientLight(0xffffff, 1.25);
    scene.add(ambientLight);

    // Directional light for soft, gentle shading gradient across the disc
    const dirLight = new DirectionalLight(0xffffff, 1.6);
    dirLight.position.set(3, 4, 3);
    scene.add(dirLight);

    // Subtle soft violet fill from bottom left
    const fillLight = new PointLight(0x7057d9, 1.1, 12);
    fillLight.position.set(-3, -2, 2);
    scene.add(fillLight);

    rootGroup = new Group();
    scene.add(rootGroup);

    // 1. Single Pure Circular Violet Disc (#7057D9)
    // Sized to comfortably fit 1:1 aspect without edge clipping
    const geoDisc = new CircleGeometry(1.56, 64);
    const matDisc = new MeshStandardMaterial({
        color: 0x7057d9,
        roughness: 0.42,
        metalness: 0.10,
    });
    violetDisc = new Mesh(geoDisc, matDisc);
    violetDisc.position.set(0, 0, 0);
    rootGroup.add(violetDisc);

    // 2. Single Hairline Outline with low opacity (#EAE6FB tone)
    const geoHairline = new TorusGeometry(1.58, 0.004, 16, 96);
    const matHairline = new MeshBasicMaterial({
        color: 0xede9fe,
        transparent: true,
        opacity: 0.35,
    });
    hairlineOutline = new Mesh(geoHairline, matHairline);
    hairlineOutline.position.set(0, 0, 0.01);
    rootGroup.add(hairlineOutline);

    if (prefersReducedMotion) {
        renderer.render(scene, camera);
        return;
    }

    startAnimationLoop();
}

function renderFrame() {
    if (!renderer || !scene || !camera || !rootGroup) return;

    if (!prefersReducedMotion) {
        // Smooth, subtle spring response to cursor
        rootGroup.rotation.x += (targetRotationX - rootGroup.rotation.x) * 0.035;
        rootGroup.rotation.y += (targetRotationY - rootGroup.rotation.y) * 0.035;
    }

    renderer.render(scene, camera);
}

function startAnimationLoop() {
    if (animationFrameId !== null) return;

    function loop() {
        if (isVisible && isTabActive) {
            renderFrame();
        }
        animationFrameId = requestAnimationFrame(loop);
    }

    animationFrameId = requestAnimationFrame(loop);
}

function stopAnimationLoop() {
    if (animationFrameId !== null) {
        cancelAnimationFrame(animationFrameId);
        animationFrameId = null;
    }
}

function handleResize() {
    if (!containerRef.value || !renderer || !camera) return;
    const width = containerRef.value.clientWidth;
    const height = containerRef.value.clientHeight;
    if (width === 0 || height === 0) return;

    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);

    if (prefersReducedMotion && scene) {
        renderer.render(scene, camera);
    }
}

function handleVisibilityChange() {
    isTabActive = !document.hidden;
}

onMounted(() => {
    initThree();

    if (containerRef.value && typeof ResizeObserver !== 'undefined') {
        resizeObserver = new ResizeObserver(() => {
            handleResize();
        });
        resizeObserver.observe(containerRef.value);
    }

    if (containerRef.value && typeof IntersectionObserver !== 'undefined') {
        intersectionObserver = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    isVisible = entry.isIntersecting;
                }
            },
            { threshold: 0.05 }
        );
        intersectionObserver.observe(containerRef.value);
    }

    window.addEventListener('mousemove', onPointerMove, { passive: true });
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

onBeforeUnmount(() => {
    stopAnimationLoop();

    window.removeEventListener('mousemove', onPointerMove);
    document.removeEventListener('visibilitychange', handleVisibilityChange);

    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }

    if (intersectionObserver) {
        intersectionObserver.disconnect();
        intersectionObserver = null;
    }

    if (rootGroup) {
        rootGroup.traverse((object) => {
            if ((object as Mesh).geometry) {
                (object as Mesh).geometry.dispose();
            }
            if ((object as Mesh).material) {
                const material = (object as Mesh).material;
                if (Array.isArray(material)) {
                    material.forEach((m) => m.dispose());
                } else {
                    material.dispose();
                }
            }
        });
        rootGroup.clear();
        rootGroup = null;
    }

    violetDisc = null;
    hairlineOutline = null;

    if (renderer) {
        renderer.dispose();
        if (renderer.domElement && renderer.domElement.parentNode) {
            renderer.domElement.parentNode.removeChild(renderer.domElement);
        }
        renderer = null;
    }

    scene = null;
    camera = null;
});
</script>

<template>
    <div
        ref="containerRef"
        class="w-full h-full pointer-events-none select-none flex items-center justify-center overflow-hidden z-0"
        aria-hidden="true"
    >
        <!-- Static Single Violet Circle Fallback if WebGL is unavailable -->
        <div
            v-if="webglFailed"
            class="w-full h-full rounded-full bg-[#7057D9] shadow-sm"
        />
    </div>
</template>
