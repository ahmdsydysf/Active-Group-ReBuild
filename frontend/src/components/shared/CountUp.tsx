"use client";

import { useEffect, useRef } from "react";
import { formatNumber } from "@/lib/format";

/** A number that counts up once it scrolls into view. The final value is in the HTML for no-JS and crawlers. */
export default function CountUp({ value, suffix = "" }: { value: number; suffix?: string }) {
  const ref = useRef<HTMLElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const dur = matchMedia("(prefers-reduced-motion: reduce)").matches ? 1 : 1400;
    let raf = 0;
    const io = new IntersectionObserver(
      ([e]) => {
        if (!e.isIntersecting) return;
        io.disconnect();
        const t0 = performance.now();
        const step = (t: number) => {
          const p = Math.min(1, (t - t0) / dur);
          el.textContent = formatNumber(Math.round(value * (1 - Math.pow(1 - p, 3)))) + suffix;
          if (p < 1) raf = requestAnimationFrame(step);
        };
        raf = requestAnimationFrame(step);
      },
      { threshold: 0.4 },
    );
    io.observe(el);
    return () => {
      io.disconnect();
      cancelAnimationFrame(raf);
    };
  }, [value, suffix]);

  return (
    <b ref={ref}>
      {formatNumber(value)}
      {suffix}
    </b>
  );
}
