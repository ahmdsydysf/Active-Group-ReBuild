"use client";

import { useRef } from "react";

/** Product family card that tilts toward the pointer and follows it with a soft light. */
export default function FamilyCard({ className, href, children }: { className: string; href: string; children: React.ReactNode }) {
  const ref = useRef<HTMLAnchorElement>(null);

  const move = (e: React.PointerEvent) => {
    const card = ref.current;
    if (!card) return;
    const r = card.getBoundingClientRect();
    const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
    card.style.setProperty("--mx", `${x * 100}%`);
    card.style.setProperty("--my", `${y * 100}%`);
    if (!matchMedia("(prefers-reduced-motion: reduce)").matches) card.style.transform = `rotateY(${(x - 0.5) * 10}deg) rotateX(${(0.5 - y) * 8}deg)`;
  };

  return (
    <a ref={ref} className={className} href={href} onPointerMove={move} onPointerLeave={() => ref.current && (ref.current.style.transform = "")}>
      {children}
    </a>
  );
}
