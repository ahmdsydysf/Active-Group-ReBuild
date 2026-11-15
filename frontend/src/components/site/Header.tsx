"use client";

import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { href, type Locale } from "@/lib/i18n";
import type { Messages } from "@/messages/ar";
import { CloseIcon, MenuIcon, WhatsAppIcon } from "./icons";

/** Sticky header that turns solid after scrolling, plus the full-screen mobile menu. */
export default function Header({ locale, t, brandHome, siteName }: { locale: Locale; t: Messages["nav"]; brandHome: string; siteName: string }) {
  const path = usePathname();
  const [solid, setSolid] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setSolid(window.scrollY > 40);
    onScroll();
    addEventListener("scroll", onScroll, { passive: true });
    return () => removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    if (!open) return;
    const onKey = (e: KeyboardEvent) => e.key === "Escape" && setOpen(false);
    addEventListener("keydown", onKey);
    return () => removeEventListener("keydown", onKey);
  }, [open]);

  const other: Locale = locale === "ar" ? "en" : "ar";
  const rest = path.replace(/^\/(ar|en)(\/|$)/, "");
  const current = (item: string) => rest === item;

  return (
    <>
      <header className={`hdr${solid ? " solid" : ""}`} id="hdr">
        <div className="wrap">
          <a className="logo" href={href(locale)} aria-label={brandHome}>
            {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
            <img src="/img/logo.webp" alt={siteName} width={150} height={46} />
          </a>
          <nav className="nav" aria-label={t.label}>
            {t.items.map((item) => (
              <a key={item.href} className={current(item.href) ? "on" : ""} href={href(locale, item.href)} aria-current={current(item.href) ? "page" : undefined}>
                {item.label}
              </a>
            ))}
          </nav>
          <a className="lang" href={href(other, rest)} lang={other} hrefLang={other}>
            {t.otherLang}
          </a>
          <a className="btn btn-sun" href={href(locale, "contact/#quote")}>
            <WhatsAppIcon />
            {t.quote}
          </a>
          <button className="burger" type="button" aria-label={t.open} aria-controls="mnav" aria-expanded={open} onClick={() => setOpen(true)}>
            <MenuIcon className="w-[22px]" />
          </button>
        </div>
      </header>
      <nav className={`mnav${open ? " open" : ""}`} id="mnav" aria-label={t.mobileLabel}>
        <button className="x" type="button" aria-label={t.close} onClick={() => setOpen(false)}>
          <CloseIcon />
        </button>
        {t.items.map((item) => (
          <a key={item.href} href={href(locale, item.href)} onClick={() => setOpen(false)}>
            {item.label}
          </a>
        ))}
        <a className="btn btn-sun mt-6 justify-center" href={href(locale, "contact/#quote")} onClick={() => setOpen(false)}>
          <WhatsAppIcon />
          {t.quote}
        </a>
      </nav>
    </>
  );
}
