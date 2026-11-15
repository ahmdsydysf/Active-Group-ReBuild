"use client";

import { useEffect, useRef, useState } from "react";
import type { Testimonial } from "@/lib/types";
import { initial, VOICE_COLORS } from "@/lib/voices";
import { ChevronLeft, ChevronRight } from "./icons";

/** Scrolling testimonials with arrows, arrow keys and dots that follow the scroll position. */
export default function VoicesSlider({ items, rtl, heading, labels }: { items: Testimonial[]; rtl: boolean; heading: React.ReactNode; labels: { prev: string; next: string; region: string } }) {
  const track = useRef<HTMLDivElement>(null);
  const [state, setState] = useState({ at: 0, start: true, end: false });

  useEffect(() => {
    const tr = track.current;
    if (!tr) return;
    const sync = () => {
      const first = tr.children[0] as HTMLElement | undefined;
      const step = first ? first.getBoundingClientRect().width + (parseFloat(getComputedStyle(tr).columnGap) || 0) : tr.clientWidth;
      const x = Math.abs(tr.scrollLeft);
      const max = tr.scrollWidth - tr.clientWidth;
      setState({ at: Math.min(Math.round(x / step), items.length - 1), start: x < 4, end: x > max - 4 });
    };
    const first = requestAnimationFrame(sync);
    tr.addEventListener("scroll", sync, { passive: true });
    addEventListener("resize", sync);
    return () => (cancelAnimationFrame(first), tr.removeEventListener("scroll", sync), removeEventListener("resize", sync));
  }, [items.length]);

  const go = (n: number) => {
    const tr = track.current;
    const first = tr?.children[0] as HTMLElement | undefined;
    if (!tr || !first) return;
    const step = first.getBoundingClientRect().width + (parseFloat(getComputedStyle(tr).columnGap) || 0);
    tr.scrollBy({ left: n * step * (rtl ? -1 : 1), behavior: matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth" });
  };

  return (
    <>
      <div className="sec-head">
        {heading}
        <div className="sl-nav">
          <button type="button" aria-label={labels.prev} disabled={state.start} onClick={() => go(-1)}>
            {rtl ? <ChevronRight className="w-5 h-5" /> : <ChevronLeft className="w-5 h-5" />}
          </button>
          <button type="button" aria-label={labels.next} disabled={state.end} onClick={() => go(1)}>
            {rtl ? <ChevronLeft className="w-5 h-5" /> : <ChevronRight className="w-5 h-5" />}
          </button>
        </div>
      </div>
      <div
        className="sl-track"
        ref={track}
        tabIndex={0}
        role="region"
        aria-label={labels.region}
        onKeyDown={(e) => {
          if (e.key === "ArrowRight" || e.key === "ArrowLeft") {
            e.preventDefault();
            go((e.key === "ArrowRight") !== rtl ? 1 : -1);
          }
        }}
      >
        {items.map((v, i) => (
          <figure key={v.id} className="vslide" style={{ "--i": i, background: VOICE_COLORS[v.color] ?? VOICE_COLORS.field, margin: 0 } as React.CSSProperties}>
            <p>{rtl ? `«${v.quote}»` : `“${v.quote}”`}</p>
            <figcaption className="who">
              <i>{initial(v.name)}</i>
              <div>
                <b>{v.name}</b>
                {v.role && <small>{v.role}</small>}
              </div>
            </figcaption>
          </figure>
        ))}
      </div>
      <div className="sl-dots" aria-hidden="true">
        {items.map((v, i) => <i key={v.id} className={i === state.at ? "on" : undefined} />)}
      </div>
    </>
  );
}
