"use client";

import { useState } from "react";
import { UploadIcon } from "./icons";

const MAX_MB = 10;

/** One PDF (the product leaflet). Shows the current file with a link and a remove option. */
export default function FileField({ label, current, file, onFile, remove, onRemove, error }: {
  label: string; current: string | null; file: File | null; onFile: (f: File | null) => void; remove: boolean; onRemove: (on: boolean) => void; error?: string;
}) {
  const [problem, setProblem] = useState<string | null>(null);
  const take = (f: File | undefined) => {
    if (!f) return;
    if (f.type !== "application/pdf") return setProblem("الملف لازم يكون PDF.");
    if (f.size > MAX_MB * 1024 * 1024) return setProblem(`الملف أكبر من ${MAX_MB} ميجا.`);
    setProblem(null);
    onRemove(false);
    onFile(f);
  };
  const name = file?.name ?? (current && !remove ? decodeURIComponent(current.split("/").pop() ?? "") : null);
  return (
    <div className="fld">
      <label className="upl">
        <UploadIcon />
        <b className="block text-d-ink">{label}</b>
        <small>PDF لحد {MAX_MB} ميجا</small>
        <input type="file" accept="application/pdf" className="sr-only" onChange={(e) => { take(e.target.files?.[0]); e.target.value = ""; }} />
      </label>
      {name && (
        <p className="mt-2 flex items-center gap-2 text-sm">
          {file ? <span className="truncate">{name}</span> : <a className="underline truncate" href={current!} target="_blank" rel="noopener">{name}</a>}
          <button type="button" className="ebtn !px-2 text-rust" onClick={() => (file ? onFile(null) : onRemove(true))}>إزالة</button>
        </p>
      )}
      {(problem ?? error) && <p className="ferr" role="alert">{problem ?? error}</p>}
    </div>
  );
}
