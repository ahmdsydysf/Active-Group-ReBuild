"use client";

import { useEffect, useRef, type FormEvent, type ReactNode } from "react";
import { CloseIcon } from "./icons";

type Props = { open: boolean; title: string; saving: boolean; onClose: () => void; onSubmit: () => void; children: ReactNode };

/** Side panel form. Focus moves to the first field on open and back to the opener on close. */
export default function Drawer({ open, title, saving, onClose, onSubmit, children }: Props) {
  const ref = useRef<HTMLFormElement>(null);
  const opener = useRef<Element | null>(null);

  useEffect(() => {
    if (!open) return;
    opener.current = document.activeElement;
    const t = setTimeout(() => ref.current?.querySelector<HTMLElement>(".body :is(input:not([type=file]), textarea)")?.focus(), 300);
    const onKey = (e: KeyboardEvent) => e.key === "Escape" && onClose();
    addEventListener("keydown", onKey);
    return () => {
      clearTimeout(t);
      removeEventListener("keydown", onKey);
      (opener.current as HTMLElement | null)?.focus?.();
    };
  }, [open, onClose]);

  const submit = (e: FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  return (
    <>
      <div className={`scrim${open ? " open" : ""}`} onClick={onClose} />
      <form ref={ref} className={`drawer${open ? " open" : ""}`} aria-label={title} aria-hidden={!open} inert={!open} onSubmit={submit} noValidate>
        <header>
          <h3>{title}</h3>
          <button className="iconbtn" type="button" aria-label="إغلاق" onClick={onClose}>
            <CloseIcon />
          </button>
        </header>
        <div className="body">{children}</div>
        <footer>
          <button className="ghost" type="button" onClick={onClose}>إلغاء</button>
          <button className="save" type="submit" disabled={saving} aria-busy={saving}>
            {saving ? "بنحفظ…" : "حفظ"}
          </button>
        </footer>
      </form>
    </>
  );
}
