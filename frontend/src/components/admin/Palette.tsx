"use client";

import { useRouter } from "next/navigation";
import { useEffect, useRef, useState, type ComponentType } from "react";

type Item = { href: string; label: string; color: string; Icon: ComponentType };

/** Ctrl+K: type to filter, arrows to move, Enter to go. */
export default function Palette({ open, onClose, items }: { open: boolean; onClose: () => void; items: readonly Item[] }) {
  const router = useRouter();
  const input = useRef<HTMLInputElement>(null);
  const [q, setQ] = useState("");
  const [sel, setSel] = useState(0);
  const [wasOpen, setWasOpen] = useState(open);
  if (open !== wasOpen) {
    // Start every opening with an empty search.
    setWasOpen(open);
    setQ("");
    setSel(0);
  }
  const shown = items.filter((i) => i.label.includes(q.trim()));

  useEffect(() => {
    if (!open) return;
    const t = setTimeout(() => input.current?.focus(), 50);
    return () => clearTimeout(t);
  }, [open]);

  const go = (i: Item | undefined) => {
    if (!i) return;
    onClose();
    router.push(i.href);
  };

  return (
    <>
      <div className={`scrim${open ? " open" : ""}`} onClick={onClose} />
      <div className={`palette${open ? " open" : ""}`} role="dialog" aria-label="البحث والأوامر" aria-hidden={!open}>
        <label className="sr-only" htmlFor="palInput">ابحث أو اكتب أمر</label>
        <input
          id="palInput"
          ref={input}
          value={q}
          placeholder="اكتب أمر أو اسم صفحة…"
          autoComplete="off"
          role="combobox"
          aria-expanded={open}
          aria-controls="palList"
          aria-activedescendant={shown[sel] ? `pal-${sel}` : undefined}
          tabIndex={open ? 0 : -1}
          onChange={(e) => {
            setQ(e.target.value);
            setSel(0);
          }}
          onKeyDown={(e) => {
            if (e.key === "ArrowDown" || e.key === "ArrowUp") {
              e.preventDefault();
              const n = shown.length || 1;
              setSel((s) => (s + (e.key === "ArrowDown" ? 1 : -1) + n) % n);
            }
            if (e.key === "Enter") go(shown[sel]);
          }}
        />
        <ul id="palList" role="listbox">
          {shown.map((i, n) => (
            <li key={i.href} id={`pal-${n}`} role="option" aria-selected={n === sel} className={n === sel ? "sel" : ""} style={{ "--c": i.color } as React.CSSProperties} onClick={() => go(i)} onMouseEnter={() => setSel(n)}>
              <i><i.Icon /></i>
              {i.label}
            </li>
          ))}
        </ul>
      </div>
    </>
  );
}
