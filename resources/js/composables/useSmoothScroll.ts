import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { router } from '@inertiajs/vue3';

gsap.registerPlugin(ScrollTrigger);

let lenis: Lenis | null = null;
let updateScrollTrigger: (() => void) | null = null;
let tickerCallback: ((time: number) => void) | null = null;
let activeLayoutCount = 0;
let removeNavigateListener: (() => void) | null = null;

export function useSmoothScroll() {
    function init() {
        if (typeof window === 'undefined') return;

        activeLayoutCount++;

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) return;

        if (!lenis) {
            lenis = new Lenis({
                autoRaf: false,
                anchors: true,
                duration: 1.1,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                touchMultiplier: 1.2,
            });

            updateScrollTrigger = () => {
                ScrollTrigger.update();
            };

            tickerCallback = (time: number) => {
                lenis?.raf(time * 1000);
            };

            lenis.on('scroll', updateScrollTrigger);
            gsap.ticker.add(tickerCallback);
            gsap.ticker.lagSmoothing(0);

            (window as unknown as { __lenis?: Lenis }).__lenis = lenis;

            if (!removeNavigateListener) {
                removeNavigateListener = router.on('finish', () => {
                    setTimeout(() => {
                        ScrollTrigger.refresh();
                    }, 50);
                });
            }
        }
    }

    function destroy() {
        activeLayoutCount = Math.max(0, activeLayoutCount - 1);

        if (activeLayoutCount === 0 && lenis) {
            if (updateScrollTrigger) {
                lenis.off('scroll', updateScrollTrigger);
                updateScrollTrigger = null;
            }
            if (tickerCallback) {
                gsap.ticker.remove(tickerCallback);
                tickerCallback = null;
            }
            if (removeNavigateListener) {
                removeNavigateListener();
                removeNavigateListener = null;
            }

            lenis.destroy();
            lenis = null;
            delete (window as unknown as { __lenis?: Lenis }).__lenis;

            ScrollTrigger.getAll().forEach((st) => st.kill());
        }
    }

    function scrollTo(
        target: string | HTMLElement,
        options?: { offset?: number; immediate?: boolean; duration?: number }
    ) {
        if (lenis) {
            lenis.scrollTo(target, options);
        } else if (typeof target === 'string') {
            const el = document.querySelector(target);
            el?.scrollIntoView({ behavior: options?.immediate ? 'auto' : 'smooth' });
        } else if (target instanceof HTMLElement) {
            target.scrollIntoView({ behavior: options?.immediate ? 'auto' : 'smooth' });
        }
    }

    function refreshTriggers() {
        ScrollTrigger.refresh();
    }

    return {
        init,
        destroy,
        scrollTo,
        refreshTriggers,
        getLenis: () => lenis,
    };
}
