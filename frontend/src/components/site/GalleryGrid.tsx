"use client";

import { useEffect, useRef, useState } from "react";
import type { Photo } from "@/lib/types";
import type { Messages } from "@/messages/ar";
import { ChevronLeft, ChevronRight, CloseIcon } from "./icons";

/** Album chips, the masonry grid and a lightbox with arrows, arrow keys and Escape. */
export default function GalleryGrid({ photos, albums, t, all, rtl }: { photos: Photo[]; albums: { slug: string; name: string }[]; t: Messages["gallery"]; all: string; rtl: boolean }) {
  const [album, setAlbum] = useState("all");
  const [open, setOpen] = useState<number | null>(null);
  const closeRef = useRef<HTMLButtonElement>(null);
  const opener = useRef<HTMLElement | null>(null);
  const shown = album === "all" ? photos : photos.filter((p) => p.album === album);
  const current = open === null ? null : shown[open];

  const show = (i: number) => setOpen((i + shown.length) % shown.length);
  const close = () => {
    setOpen(null);
    opener.current?.focus();
  };

  useEffect(() => {
    if (open === null) return;
    closeRef.current?.focus();
    document.body.style.overflow = "hidden";
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") setOpen(null);
      if (e.key === "ArrowLeft") setOpen((i) => (i === null ? i : (i + (rtl ? 1 : -1) + shown.length) % shown.length));
      if (e.key === "ArrowRight") setOpen((i) => (i === null ? i : (i + (rtl ? -1 : 1) + shown.length) % shown.length));
    };
    addEventListener("keydown", onKey);
    return () => (removeEventListener("keydown", onKey), void (document.body.style.overflow = ""));
  }, [open, rtl, shown.length]);

  return (
    <>
      {albums.length > 1 && (
        <div className="flex gap-2 overflow-x-auto pb-1 mb-8" role="group" aria-label={t.albums}>
          {[{ slug: "all", name: all }, ...albums].map((a) => (
            <button key={a.slug} className="fchip" type="button" aria-pressed={album === a.slug} style={{ "--c": "var(--deep)" } as React.CSSProperties} onClick={() => setAlbum(a.slug)}>
              {a.name}
            </button>
          ))}
        </div>
      )}
      <div className="masonry">
        {shown.map((p, i) => (
          <figure key={p.id}>
            <button
              type="button"
              className="block w-full text-start"
              aria-label={p.caption ?? t.viewer}
              onClick={(e) => {
                opener.current = e.currentTarget;
                setOpen(i);
              }}
            >
              {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
              <img src={p.image_sm} alt={p.caption ?? ""} loading="lazy" decoding="async" />
            </button>
            {p.caption && <figcaption className="pointer-events-none" aria-hidden="true">{p.caption}</figcaption>}
          </figure>
        ))}
      </div>
      <div className={`lightbox${current ? " open" : ""}`} role="dialog" aria-modal="true" aria-label={t.viewer} inert={!current} onClick={(e) => e.target === e.currentTarget && close()}>
        <button ref={closeRef} type="button" className="top-5 end-5" aria-label={t.close} onClick={close}>
          <CloseIcon />
        </button>
        <button type="button" className="start-5 top-1/2 -translate-y-1/2" aria-label={t.prev} onClick={() => open !== null && show(open - 1)}>
          {rtl ? <ChevronRight /> : <ChevronLeft />}
        </button>
        {current && (
          // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
          <img src={current.image} alt={current.caption ?? ""} />
        )}
        <button type="button" className="end-5 top-1/2 -translate-y-1/2" aria-label={t.next} onClick={() => open !== null && show(open + 1)}>
          {rtl ? <ChevronLeft /> : <ChevronRight />}
        </button>
      </div>
    </>
  );
}
