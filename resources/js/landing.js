/**
 * MyCash Landing Page — Refined Scrollytelling Engine
 * 300-Frame Hardware Canvas Sequence + Lenis Smooth Scroll + GSAP ScrollTrigger
 * Clean Code Architecture (SOLID, No side-effects, Zero console errors)
 */

import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// ─────────────────────────────────────────────────────────────────────────────
// 1. SEQUENCE BUFFER (300 Frames with Progressive Loading & Fallback)
// ─────────────────────────────────────────────────────────────────────────────
class SequenceBuffer {
    constructor(totalFrames, onProgress) {
        this.totalFrames = totalFrames;
        this.onProgress = onProgress;
        this.images = new Map();
        this.loadedCount = 0;
        this.firstFrameLoaded = false;
        this.isFullyLoaded = false;
    }

    getFrameUrl(index) {
        const padded = String(index).padStart(3, '0');
        return `/assets/sequence/ezgif-frame-${padded}.jpg`;
    }

    preloadFirstFrame(callback) {
        const img = new Image();
        img.src = this.getFrameUrl(1);
        img.onload = () => {
            this.images.set(1, img);
            this.loadedCount++;
            this.firstFrameLoaded = true;
            if (callback) callback(img);
            this.startBatchLoading();
        };
        img.onerror = () => {
            if (callback) callback(null);
            this.startBatchLoading();
        };
    }

    startBatchLoading() {
        // Priority 1: frames 2 to 40 (immediate response when scrolling starts)
        this.loadRange(2, Math.min(40, this.totalFrames), () => {
            // Priority 2: frames 41 to 140
            this.loadRange(41, Math.min(140, this.totalFrames), () => {
                // Priority 3: frames 141 to 300
                this.loadRange(141, this.totalFrames, () => {
                    this.isFullyLoaded = true;
                });
            });
        });
    }

    loadRange(start, end, onComplete) {
        const countToLoad = end - start + 1;
        if (countToLoad <= 0) {
            if (onComplete) onComplete();
            return;
        }

        let completed = 0;
        for (let i = start; i <= end; i++) {
            const img = new Image();
            img.src = this.getFrameUrl(i);
            const done = () => {
                completed++;
                this.loadedCount++;
                if (this.onProgress) {
                    this.onProgress(this.loadedCount, this.totalFrames);
                }
                if (completed === countToLoad && onComplete) {
                    onComplete();
                }
            };
            img.onload = () => {
                this.images.set(i, img);
                done();
            };
            img.onerror = () => {
                done();
            };
        }
    }

    getFrame(index) {
        if (this.images.has(index)) {
            return this.images.get(index);
        }
        // Fallback to nearest loaded frame to ensure zero flickering
        let lower = index - 1;
        let upper = index + 1;
        while (lower >= 1 || upper <= this.totalFrames) {
            if (lower >= 1 && this.images.has(lower)) return this.images.get(lower);
            if (upper <= this.totalFrames && this.images.has(upper)) return this.images.get(upper);
            lower--;
            upper++;
        }
        return this.images.get(1) || null;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 2. CANVAS HIGH-DPI RENDERER
// ─────────────────────────────────────────────────────────────────────────────
class SequenceCanvasRenderer {
    constructor(canvas, sequenceBuffer) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d', { alpha: false });
        this.buffer = sequenceBuffer;
        this.currentFrame = 1;
        this.targetFrame = 1;
        this.dpr = Math.min(window.devicePixelRatio || 1, 2);

        this.initResize();
    }

    initResize() {
        this.handleResize = this.handleResize.bind(this);
        window.addEventListener('resize', this.handleResize, { passive: true });
        this.handleResize();
    }

    handleResize() {
        const rect = this.canvas.getBoundingClientRect();
        this.dpr = Math.min(window.devicePixelRatio || 1, 2);
        this.canvas.width = Math.round(rect.width * this.dpr);
        this.canvas.height = Math.round(rect.height * this.dpr);
        this.renderImmediate();
    }

    setTargetFrame(frameIndex) {
        this.targetFrame = Math.max(1, Math.min(frameIndex, this.buffer.totalFrames));
    }

    renderImmediate() {
        const frameImg = this.buffer.getFrame(Math.round(this.currentFrame));
        if (!frameImg) return;

        const w = this.canvas.width;
        const h = this.canvas.height;
        const ctx = this.ctx;

        const frameW = 1920;
        const frameH = 1080;
        const frameRatio = frameW / frameH;
        const canvasRatio = w / h;

        let drawW, drawH;
        if (canvasRatio > frameRatio) {
            drawH = h;
            drawW = h * frameRatio;
        } else {
            drawW = w;
            drawH = w / frameRatio;
        }

        const drawX = Math.round((w - drawW) / 2);
        const drawY = Math.round((h - drawH) / 2);

        ctx.fillStyle = '#060D17';
        ctx.fillRect(0, 0, w, h);
        ctx.drawImage(frameImg, drawX, drawY, drawW, drawH);
    }

    tick(lerpFactor = 0.18) {
        const diff = this.targetFrame - this.currentFrame;
        if (Math.abs(diff) > 0.05) {
            this.currentFrame += diff * lerpFactor;
            this.renderImmediate();
            return true;
        } else if (Math.round(this.currentFrame) !== this.targetFrame) {
            this.currentFrame = this.targetFrame;
            this.renderImmediate();
            return false;
        }
        return false;
    }

    destroy() {
        window.removeEventListener('resize', this.handleResize);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. MAIN ORCHESTRATOR
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lenis Smooth Scroll
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        orientation: 'vertical',
        smoothWheel: true,
        wheelMultiplier: 1.0,
        touchMultiplier: 1.3,
    });

    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    // Responsive Canvas & Buffer Orchestration
    // Only initialize the 300-frame sequence & pinning on desktop (>= 1024px)
    // On mobile (< 1024px), the lightweight #hero-mobile with Hero.png is active instead.
    const mm = gsap.matchMedia();

    mm.add('(min-width: 1024px)', () => {
        const canvas = document.getElementById('sequence-canvas');
        const sequenceSection = document.getElementById('sequence-track');
        if (!canvas || !sequenceSection) return;

        const totalFrames = 300;
        const bufferBar = document.getElementById('buffer-progress-bar');
        const bufferText = document.getElementById('buffer-progress-text');
        const frameCounterEl = document.getElementById('hud-frame-counter');
        const heroTextBlock = document.getElementById('hero-text-block');
        const heroHeadlineBlock = document.getElementById('hero-headline-block');

        const buffer = new SequenceBuffer(totalFrames, (loaded, total) => {
            const pct = Math.round((loaded / total) * 100);
            if (bufferBar) bufferBar.style.width = `${pct}%`;
            if (bufferText) bufferText.textContent = `${pct}% BUFFERED`;
            if (pct >= 100) {
                setTimeout(() => {
                    const loaderContainer = document.getElementById('sequence-loader-pill');
                    if (loaderContainer) {
                        loaderContainer.classList.add('opacity-0', 'pointer-events-none');
                    }
                }, 600);
            }
        });

        const renderer = new SequenceCanvasRenderer(canvas, buffer);

        // Preload frame 1 and paint immediately
        buffer.preloadFirstFrame(() => {
            renderer.renderImmediate();
        });

        // RAF Loop
        let animFrameId;
        let lastRenderedFrame = 1;
        function renderLoop() {
            renderer.tick(0.18);
            const currentFrameInt = Math.round(renderer.currentFrame);
            if (currentFrameInt !== lastRenderedFrame) {
                lastRenderedFrame = currentFrameInt;
                if (frameCounterEl) {
                    frameCounterEl.textContent = `FRM ${String(currentFrameInt).padStart(3, '0')} / 300`;
                }
            }
            animFrameId = requestAnimationFrame(renderLoop);
        }
        animFrameId = requestAnimationFrame(renderLoop);

        // Pinning and Scroll Orchestration
        const st = ScrollTrigger.create({
            trigger: sequenceSection,
            start: 'top top',
            end: '+=100%',
            pin: true,
            scrub: 0.3,
            anticipatePin: 1,
            onUpdate: (self) => {
                const progress = self.progress;
                const targetFrame = Math.min(300, Math.max(1, Math.round(progress * 299) + 1));
                renderer.setTargetFrame(targetFrame);

                // Smooth Hero Text & Headline Fade Out as sequence activates
                // 0.0 -> 0.04: Opacity 1.0
                // 0.04 -> 0.22: Linearly fade out
                // > 0.22: Opacity 0.0
                let textOpacity = 1;
                if (progress > 0.04 && progress <= 0.22) {
                    textOpacity = 1 - (progress - 0.04) / 0.18;
                } else if (progress > 0.22) {
                    textOpacity = 0;
                }

                if (heroTextBlock) {
                    heroTextBlock.style.opacity = textOpacity.toFixed(2);
                    heroTextBlock.style.transform = `translateY(${((1 - textOpacity) * -20).toFixed(1)}px)`;
                    heroTextBlock.style.pointerEvents = textOpacity > 0.5 ? 'auto' : 'none';
                }

                if (heroHeadlineBlock) {
                    heroHeadlineBlock.style.opacity = textOpacity.toFixed(2);
                    heroHeadlineBlock.style.transform = `translateY(${((1 - textOpacity) * 20).toFixed(1)}px)`;
                }
            },
        });

        // Ensure ScrollTrigger measures accurately
        ScrollTrigger.refresh();

        return () => {
            if (animFrameId) cancelAnimationFrame(animFrameId);
            renderer.destroy();
            st.kill();
        };
    });

    // Anchor Smooth Scrolling via Lenis
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            let targetId = anchor.getAttribute('href');
            if (targetId === '#' || targetId === '') return;
            if (targetId === '#sequence-track' && window.innerWidth < 1024) {
                targetId = '#hero-mobile';
            }
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                e.preventDefault();
                lenis.scrollTo(targetEl, { offset: -40, duration: 1.2 });
            }
        });
    });
});
