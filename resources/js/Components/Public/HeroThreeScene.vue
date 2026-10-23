<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
    Scene,
    PerspectiveCamera,
    WebGLRenderer,
    Group,
    TorusGeometry,
    SphereGeometry,
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
let ringPrimary: Mesh | null = null;
let ringSecondary: Mesh | null = null;
let ringTertiary: Mesh | null = null;
let ringInner: Mesh | null = null;

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

    // Bounded tilt amplitude for calm, dignified response
    targetRotationY = Math.max(-0.14, Math.min(0.14, normX * 0.14));
    targetRotationX = Math.max(-0.10, Math.min(0.10, normY * 0.10));
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

    const width = containerRef.value.clientWidth || 360;
    const height = containerRef.value.clientHeight || 360;

    scene = new Scene();

    // 40 deg FOV with camera positioned to frame orbit boundaries with clean margins
    camera = new PerspectiveCamera(40, width / height, 0.1, 100);
    camera.position.set(0, 0, 7.2);

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

    // Studio Lighting calibrated for Obsidian dark substrate (#151B22)
    const ambientLight = new AmbientLight(0xffffff, 1.1);
    scene.add(ambientLight);

    // Primary key light (refined periwinkle purple: #818CF8)
    const dirLightPurple = new DirectionalLight(0x818cf8, 2.4);
    dirLightPurple.position.set(4, 5, 3.5);
    scene.add(dirLightPurple);

    // Fill rim light (indigo blue: #6366f1)
    const fillLight = new PointLight(0x6366f1, 1.8, 18);
    fillLight.position.set(-3.5, -3, 2.5);
    scene.add(fillLight);

    // Subtle rear edge light (soft lavender: #c7d2fe)
    const rearLight = new PointLight(0xc7d2fe, 1.0, 14);
    rearLight.position.set(0, 2.5, -3);
    scene.add(rearLight);

    rootGroup = new Group();
    scene.add(rootGroup);

    // -------------------------------------------------------------
    // 1. Primary Sculptural Orbit Ring (Curated Periwinkle: #818CF8)
    // -------------------------------------------------------------
    const geoPrimary = new TorusGeometry(2.35, 0.015, 24, 128);
    const matPrimary = new MeshStandardMaterial({
        color: 0x818cf8,
        roughness: 0.28,
        metalness: 0.52,
        transparent: true,
        opacity: 0.90,
    });
    ringPrimary = new Mesh(geoPrimary, matPrimary);
    ringPrimary.rotation.x = Math.PI / 3.4;
    ringPrimary.rotation.y = Math.PI / 6.0;
    rootGroup.add(ringPrimary);

    // Two subtle node markers along Primary Orbit (at 180° interval)
    const markerGeoPrimary = new SphereGeometry(0.038, 16, 16);
    const markerMatPrimary = new MeshBasicMaterial({ color: 0xc7d2fe });
    [0, Math.PI].forEach((theta) => {
        const marker = new Mesh(markerGeoPrimary, markerMatPrimary);
        marker.position.set(Math.cos(theta) * 2.35, Math.sin(theta) * 2.35, 0);
        ringPrimary?.add(marker);
    });

    // -------------------------------------------------------------
    // 2. Secondary Counter-Orbital Ring (Soft Periwinkle Slate: #A5B4FC)
    // -------------------------------------------------------------
    const geoSecondary = new TorusGeometry(1.82, 0.010, 20, 100);
    const matSecondary = new MeshStandardMaterial({
        color: 0xa5b4fc,
        roughness: 0.36,
        metalness: 0.38,
        transparent: true,
        opacity: 0.72,
    });
    ringSecondary = new Mesh(geoSecondary, matSecondary);
    ringSecondary.rotation.x = -Math.PI / 3.8;
    ringSecondary.rotation.z = Math.PI / 3.8;
    rootGroup.add(ringSecondary);

    // -------------------------------------------------------------
    // 3. Tertiary Outer Cadence Horizon (Deep Indigo: #6366F1)
    // -------------------------------------------------------------
    const geoTertiary = new TorusGeometry(2.68, 0.006, 16, 128);
    const matTertiary = new MeshStandardMaterial({
        color: 0x6366f1,
        roughness: 0.45,
        metalness: 0.30,
        transparent: true,
        opacity: 0.45,
    });
    ringTertiary = new Mesh(geoTertiary, matTertiary);
    ringTertiary.rotation.x = Math.PI / 2.25;
    ringTertiary.rotation.y = -Math.PI / 7.5;
    rootGroup.add(ringTertiary);

    // -------------------------------------------------------------
    // 4. Subtle Inner Framing Orbit (Refined Violet Accent: #818CF8)
    // -------------------------------------------------------------
    const geoInner = new TorusGeometry(1.48, 0.008, 16, 96);
    const matInner = new MeshStandardMaterial({
        color: 0x818cf8,
        roughness: 0.25,
        metalness: 0.45,
        transparent: true,
        opacity: 0.55,
    });
    ringInner = new Mesh(geoInner, matInner);
    ringInner.rotation.x = Math.PI / 4.2;
    ringInner.rotation.z = -Math.PI / 5.0;
    rootGroup.add(ringInner);

    if (prefersReducedMotion) {
        renderer.render(scene, camera);
        return;
    }

    startAnimationLoop();
}

function renderFrame() {
    if (!renderer || !scene || !camera || !rootGroup) return;

    if (!prefersReducedMotion) {
        // Slow, dignified angular velocities
        if (ringPrimary) ringPrimary.rotation.z += 0.00075;
        if (ringSecondary) ringSecondary.rotation.z -= 0.00095;
        if (ringTertiary) ringTertiary.rotation.z += 0.00045;
        if (ringInner) ringInner.rotation.z += 0.00110;

        // Smooth spring interpolation toward pointer orientation
        rootGroup.rotation.x += (targetRotationX - rootGroup.rotation.x) * 0.025;
        rootGroup.rotation.y += (targetRotationY - rootGroup.rotation.y) * 0.025;
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

    ringPrimary = null;
    ringSecondary = null;
    ringTertiary = null;
    ringInner = null;

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
        class="absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden z-0"
        aria-hidden="true"
    >
        <!-- Static SVG ring fallback if WebGL is unsupported -->
        <svg
            v-if="webglFailed"
            viewBox="0 0 400 400"
            class="w-full h-full opacity-60"
            fill="none"
            stroke="currentColor"
        >
            <ellipse cx="200" cy="200" rx="140" ry="60" stroke="#818CF8" stroke-width="1.5" transform="rotate(-25 200 200)" />
            <ellipse cx="200" cy="200" rx="110" ry="45" stroke="#A5B4FC" stroke-width="1.2" stroke-dasharray="6 4" transform="rotate(35 200 200)" />
            <ellipse cx="200" cy="200" rx="160" ry="70" stroke="#6366F1" stroke-width="0.8" opacity="0.4" transform="rotate(10 200 200)" />
        </svg>
    </div>
</template>
