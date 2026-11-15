"use client";

import type { ReactNode } from "react";
import { ChevronIcon, SearchIcon } from "./icons";
import type { Meta } from "@/lib/admin/useList";

export function SearchBox({ value, onChange, label }: { value: string; onChange: (v: string) => void; label: string }) {
  return (
    <label className="dsearch !cursor-text">
      <SearchIcon className="w-[18px] flex-none" />
      <span className="sr-only">{label}</span>
      <input type="search" className="bg-transparent outline-none flex-1 min-w-0 text-d-ink" placeholder={label} value={value} onChange={(e) => onChange(e.target.value)} />
    </label>
  );
}

export function Filter({ label, value, options, onChange }: { label: string; value: string; options: [string, string][]; onChange: (v: string) => void }) {
  return (
    <select className="dinput dsel !w-auto" aria-label={label} value={value} onChange={(e) => onChange(e.target.value)}>
      <option value="">{label}</option>
      {options.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
    </select>
  );
}

export function BulkBar({ count, onAction, publish = true }: { count: number; onAction: (a: "publish" | "hide" | "delete") => void; publish?: boolean }) {
  if (!count) return null;
  return (
    <div className="flex flex-wrap items-center gap-3 rounded-xl px-4 py-2.5 mb-3 bg-[color-mix(in_srgb,var(--leaf)_14%,transparent)]" role="region" aria-label="إجراءات على المحدد">
      <b>{count} محدد</b>
      {publish && (
        <>
          <button className="ebtn" type="button" onClick={() => onAction("publish")}>نشر</button>
          <button className="ebtn" type="button" onClick={() => onAction("hide")}>إخفاء</button>
        </>
      )}
      <button className="ebtn text-rust" type="button" onClick={() => onAction("delete")}>حذف</button>
    </div>
  );
}

/** Loading, error and empty states as one table row. */
export function TableState({ items, error, cols, empty }: { items: unknown[] | null; error: string | null; cols: number; empty: string }) {
  const text = error ?? (!items ? "بنحمّل…" : items.length ? null : empty);
  if (!text) return null;
  return (
    <tr>
      <td colSpan={cols} className="!py-10 text-center text-d-mute" role={error ? "alert" : undefined} aria-busy={!items && !error}>{text}</td>
    </tr>
  );
}

export function DPager({ meta, per, onPage }: { meta: Meta | undefined; per: number; onPage: (p: number) => void }) {
  if (!meta || !meta.total) return null;
  const { page, last_page: last, total } = meta;
  const from = (page - 1) * per + 1;
  const pages = Array.from({ length: last }, (_, i) => i + 1).filter((n) => n === 1 || n === last || Math.abs(n - page) <= 1);
  const btn = (n: number, label: ReactNode, aria?: string) => (
    <button key={aria ?? n} type="button" aria-label={aria} aria-current={!aria && n === page ? "page" : undefined} disabled={n < 1 || n > last} onClick={() => onPage(n)}>{label}</button>
  );
  return (
    <div className="dpager">
      <span>عرض {from} إلى {Math.min(page * per, total)} من {total}</span>
      {last > 1 && (
        <div>
          {btn(page - 1, <ChevronIcon className="w-4 h-4 -scale-x-100" />, "السابق")}
          {pages.map((n, i) => [i > 0 && n - pages[i - 1] > 1 && <span key={`g${n}`} className="self-center">…</span>, btn(n, n)])}
          {btn(page + 1, <ChevronIcon className="w-4 h-4" />, "التالي")}
        </div>
      )}
    </div>
  );
}

/** "Checked" box for a table header or row. */
export function Check({ checked, onChange, label }: { checked: boolean; onChange: () => void; label: string }) {
  return <input type="checkbox" className="chk" aria-label={label} checked={checked} onChange={onChange} />;
}
