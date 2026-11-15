"use client";

import { useEffect, useRef, useState } from "react";
import type { Messages } from "@/messages/ar";
import { BoxIcon, PhoneIcon, WhatsAppIcon } from "./icons";

type Props = { t: Messages["sprout"]; whatsapp: string | null; phone: string | null; quote: string };

/**
 * The "contact sprout": a potted seedling in the corner that opens one card with WhatsApp,
 * call and quote. Closes on outside click, Escape, or after scrolling 120px.
 */
export default function Sprout({ t, whatsapp, phone, quote }: Props) {
  const [open, setOpen] = useState(false);
  const [hint, setHint] = useState(false);
  const root = useRef<HTMLDivElement>(null);
  const button = useRef<HTMLButtonElement>(null);

  useEffect(() => {
    if (!open) return;
    const y0 = scrollY;
    const onClick = (e: MouseEvent) => !root.current?.contains(e.target as Node) && setOpen(false);
    const onScroll = () => Math.abs(scrollY - y0) > 120 && setOpen(false);
    const onKey = (e: KeyboardEvent) => {
      if (e.key !== "Escape") return;
      setOpen(false);
      button.current?.focus();
    };
    document.addEventListener("click", onClick);
    addEventListener("scroll", onScroll, { passive: true });
    addEventListener("keydown", onKey);
    return () => {
      document.removeEventListener("click", onClick);
      removeEventListener("scroll", onScroll);
      removeEventListener("keydown", onKey);
    };
  }, [open]);

  // A one-time greeting bubble per visit.
  useEffect(() => {
    let seen = false;
    try {
      seen = sessionStorage.getItem("ag-hi") === "1";
    } catch {}
    if (seen) return;
    let hide: ReturnType<typeof setTimeout>;
    const show = setTimeout(() => {
      setHint(true);
      try {
        sessionStorage.setItem("ag-hi", "1");
      } catch {}
      hide = setTimeout(() => setHint(false), 5000);
    }, 6000);
    return () => {
      clearTimeout(show);
      clearTimeout(hide);
    };
  }, []);

  return (
    <div className={`sprout${open ? " open" : ""}${hint && !open ? " hi" : ""}`} id="sprout" ref={root}>
      <div className="sprout-menu" id="sproutMenu">
        <b className="sprout-title">{t.title}</b>
        {whatsapp && (
          <a className="leafbtn" style={{ "--i": 0, "--c": "#1FA855" } as React.CSSProperties} href={whatsapp} target="_blank" rel="noopener noreferrer">
            <WhatsAppIcon />
            {t.whatsapp}
          </a>
        )}
        {phone && (
          <a className="leafbtn" style={{ "--i": 1, "--c": "var(--sky)" } as React.CSSProperties} href={phone}>
            <PhoneIcon />
            {t.call}
          </a>
        )}
        <a className="leafbtn" style={{ "--i": 2, "--c": "var(--wheat)" } as React.CSSProperties} href={quote}>
          <BoxIcon />
          {t.quote}
        </a>
      </div>
      <span className="sprout-hi" aria-hidden="true">
        {t.hint}
      </span>
      <button
        ref={button}
        className="sprout-pot"
        type="button"
        aria-expanded={open}
        aria-controls="sproutMenu"
        aria-label={t.title}
        onClick={(e) => {
          e.stopPropagation();
          setOpen((o) => !o);
          setHint(false);
        }}
      >
        <svg viewBox="0 0 80 96" aria-hidden="true">
          <g className="stemg">
            <path d="M40 66 C39 52 42 42 40 28" stroke="#3E9A27" strokeWidth="4" fill="none" strokeLinecap="round" />
            <path className="lf l1" d="M40 52 C30 44 22 46 18 50 C26 56 34 56 40 52Z" fill="#4CAF2E" />
            <path className="lf l2" d="M41 44 C50 36 58 38 62 42 C54 48 46 48 41 44Z" fill="#3E9A27" />
            <g className="head" transform="translate(40 24)">
              <ellipse className="bud" cx="0" cy="0" rx="6" ry="9" fill="#4CAF2E" />
              <g className="petals">
                {[0, 60, 120, 180, 240, 300].map((r) => (
                  <ellipse key={r} cx="0" cy="-11" rx="7" ry="11" fill="#E0607E" transform={`rotate(${r})`} />
                ))}
                <circle r="6.5" fill="#F2C230" stroke="#D08A2A" strokeWidth="1.5" />
              </g>
            </g>
          </g>
          <path d="M14 64 H66 L60 92 H20Z" fill="#C8642F" />
          <rect x="10" y="60" width="60" height="10" rx="4" fill="#A9501F" />
          <ellipse cx="40" cy="62" rx="26" ry="3" fill="#4A2E1A" />
          <path d="M26 78 h28" stroke="#E08A55" strokeWidth="2" strokeLinecap="round" opacity=".6" />
        </svg>
      </button>
    </div>
  );
}
