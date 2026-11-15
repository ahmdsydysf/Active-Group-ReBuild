"use client";

import { useEffect, useRef } from "react";

const COLORS = [["#F2C230", "#7A4A0C"], ["#E0607E", "#F2C230"], ["#FFFFFF", "#F2C230"], ["#D08A2A", "#5A3A0E"], ["#2E8FB0", "#FFFFFF"], ["#F49AB0", "#B4442E"]];

type Flower = { x: number; h: number; t: number; pc: string; cc: string; n: number; r: number; ph: number; lean: number; leafSide: number; kind: "tulip" | "daisy" };
type Pollen = { x: number; y: number; v: number; s: number; o: number };

const ease = (p: number) => 1 - Math.pow(1 - Math.max(0, Math.min(1, p)), 3);

/**
 * The living garden behind the site hero and the dashboard login: flowers grow, sway away from the pointer, and a tap
 * plants a new one. Pauses when off screen; draws one still frame with reduced motion.
 */
export default function Garden() {
  const ref = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    const cv = ref.current;
    const ctx = cv?.getContext("2d");
    if (!cv || !ctx) return;
    const RM = matchMedia("(prefers-reduced-motion: reduce)").matches;
    let W = 0, H = 0, groundY = 0, flowers: Flower[] = [], pollen: Pollen[] = [];
    const mouse = { x: -999, y: -999 };

    const size = () => {
      const DPR = Math.min(2, devicePixelRatio || 1);
      const r = cv.getBoundingClientRect();
      W = r.width; H = r.height;
      cv.width = W * DPR; cv.height = H * DPR;
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
      groundY = H - 46;
    };
    const addFlower = (x: number, delay = 0, big = false) => {
      const [pc, cc] = COLORS[Math.floor(Math.random() * COLORS.length)];
      flowers.push({ x, h: (big ? 105 : 45) + Math.random() * (big ? 60 : 75), t: -delay, pc, cc, n: 5 + Math.floor(Math.random() * 4), r: 12 + Math.random() * 12, ph: Math.random() * 6.28, lean: (Math.random() - 0.5) * 30, leafSide: Math.random() > 0.5 ? 1 : -1, kind: Math.random() > 0.75 ? "tulip" : "daisy" });
      if (flowers.length > 46) flowers.shift();
    };
    const seed = () => {
      flowers = [];
      const n = Math.max(8, Math.floor(W / 70));
      for (let i = 0; i < n; i++) addFlower((i + 0.5) * (W / n) + (Math.random() - 0.5) * 30, 0.2 + i * 0.12 + Math.random() * 0.4);
      pollen = Array.from({ length: Math.floor(W / 30) }, () => ({ x: Math.random() * W, y: Math.random() * H, v: 0.2 + Math.random() * 0.5, s: 1 + Math.random() * 2.2, o: Math.random() * 6.28 }));
    };
    const drawFlower = (f: Flower, time: number) => {
      const g = ease(f.t / 1.6), b = ease((f.t - 1.5) / 1.2);
      if (g <= 0) return;
      const len = f.h * g;
      let sway = Math.sin(time * 0.9 + f.ph) * 6 + f.lean * g * 0.3;
      const dx = f.x - mouse.x, dy = groundY - len - mouse.y, d = Math.hypot(dx, dy);
      if (d < 160) sway += (dx / (d || 1)) * (160 - d) * 0.25;
      const tx = f.x + sway, ty = groundY - len;
      ctx.strokeStyle = "#4CAF2E"; ctx.lineWidth = 3; ctx.lineCap = "round";
      ctx.beginPath(); ctx.moveTo(f.x, groundY); ctx.quadraticCurveTo(f.x + sway * 0.2, groundY - len * 0.5, tx, ty); ctx.stroke();
      for (const [p, s] of [[0.35, f.leafSide], [0.6, -f.leafSide]]) {
        const lp = ease((g - p) / 0.3);
        if (lp <= 0) continue;
        ctx.save(); ctx.translate(f.x + sway * 0.2 * p * 2 * p, groundY - len * p); ctx.rotate(s * (0.9 + Math.sin(time + f.ph) * 0.08)); ctx.scale(lp, lp);
        ctx.fillStyle = s > 0 ? "#4CAF2E" : "#3E9A27";
        ctx.beginPath(); ctx.moveTo(0, 0); ctx.quadraticCurveTo(14, -12, 30, 0); ctx.quadraticCurveTo(14, 10, 0, 0); ctx.fill(); ctx.restore();
      }
      ctx.save(); ctx.translate(tx, ty);
      if (b <= 0) {
        ctx.fillStyle = "#3E9A27"; ctx.beginPath(); ctx.ellipse(0, -5, 5 * g, 9 * g, 0, 0, 6.29); ctx.fill(); ctx.restore();
        return;
      }
      ctx.rotate(Math.sin(time * 0.9 + f.ph) * 0.12);
      ctx.fillStyle = f.pc;
      if (f.kind === "tulip") {
        for (let i = -1; i <= 1; i++) { ctx.save(); ctx.rotate(i * 0.45 * b); ctx.beginPath(); ctx.ellipse(0, -f.r * 0.8, f.r * 0.45, f.r * (0.6 + 0.4 * b), 0, 0, 6.29); ctx.fill(); ctx.restore(); }
      } else {
        for (let i = 0; i < f.n; i++) { ctx.save(); ctx.rotate((i / f.n) * 6.283 + time * 0.05); ctx.beginPath(); ctx.ellipse(f.r * 0.55 * b, 0, f.r * 0.6 * b, f.r * 0.26, 0, 0, 6.29); ctx.fill(); ctx.restore(); }
        ctx.fillStyle = f.cc; ctx.beginPath(); ctx.arc(0, 0, f.r * 0.32 * (0.4 + 0.6 * b), 0, 6.29); ctx.fill();
      }
      ctx.restore();
    };

    let last = performance.now(), running = false, raf = 0;
    const frame = (now: number) => {
      const dt = Math.min(0.05, (now - last) / 1000); last = now;
      const time = now / 1000;
      ctx.clearRect(0, 0, W, H);
      for (const p of pollen) {
        p.y -= p.v; p.x += Math.sin(time + p.o) * 0.3;
        if (p.y < -10) { p.y = H; p.x = Math.random() * W; }
        ctx.fillStyle = `rgba(242,194,48,${0.25 + 0.25 * Math.sin(time * 2 + p.o)})`;
        ctx.beginPath(); ctx.arc(p.x, p.y, p.s, 0, 6.29); ctx.fill();
      }
      ctx.fillStyle = "#0B2A12"; ctx.beginPath(); ctx.moveTo(0, groundY);
      for (let x = 0; x <= W; x += 40) ctx.lineTo(x, groundY + Math.sin(x * 0.02) * 4);
      ctx.lineTo(W, H); ctx.lineTo(0, H); ctx.fill();
      ctx.strokeStyle = "rgba(76,175,46,.25)"; ctx.lineWidth = 1.5;
      for (let k = 1; k <= 3; k++) { ctx.beginPath(); for (let x = 0; x <= W; x += 30) ctx.lineTo(x, groundY + k * 11 + Math.sin(x * 0.02 + k) * 3); ctx.stroke(); }
      for (const f of flowers) { f.t += dt; drawFlower(f, time); }
      if (running) raf = requestAnimationFrame(frame);
    };

    const local = (e: PointerEvent) => { const r = cv.getBoundingClientRect(); return [e.clientX - r.left, e.clientY - r.top]; };
    const onMove = (e: PointerEvent) => { [mouse.x, mouse.y] = local(e); };
    const onLeave = () => { mouse.x = mouse.y = -999; };
    const onDown = (e: PointerEvent) => addFlower(local(e)[0], 0, true);
    const onResize = () => { size(); seed(); };
    cv.addEventListener("pointermove", onMove);
    cv.addEventListener("pointerleave", onLeave);
    cv.addEventListener("pointerdown", onDown);
    addEventListener("resize", onResize);
    const io = new IntersectionObserver(([e]) => {
      const was = running;
      running = e.isIntersecting && !RM;
      if (running && !was) { last = performance.now(); raf = requestAnimationFrame(frame); }
    });
    io.observe(cv);
    size(); seed();
    if (RM) { flowers.forEach((f) => (f.t = 5)); frame(performance.now()); }

    return () => {
      running = false; cancelAnimationFrame(raf); io.disconnect();
      cv.removeEventListener("pointermove", onMove); cv.removeEventListener("pointerleave", onLeave); cv.removeEventListener("pointerdown", onDown);
      removeEventListener("resize", onResize);
    };
  }, []);

  return <canvas id="garden" ref={ref} aria-hidden="true" />;
}
