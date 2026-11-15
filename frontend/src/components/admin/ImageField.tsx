"use client";

import { useEffect, useMemo, useState } from "react";
import { UploadIcon } from "./icons";

type Props = { label: string; hint: string; current?: string | null; file: File | null; onFile: (f: File | null) => void; error?: string; contain?: boolean };

const ACCEPT = "image/jpeg,image/png,image/webp";
const MAX = 5 * 1024 * 1024;

/** Drop or pick one image. The browser checks type and size first; the API checks again and re-encodes it. */
export default function ImageField({ label, hint, current, file, onFile, error, contain }: Props) {
  const [over, setOver] = useState(false);
  const [problem, setProblem] = useState<string | null>(null);

  const local = useMemo(() => (file ? URL.createObjectURL(file) : null), [file]);
  useEffect(() => () => void (local && URL.revokeObjectURL(local)), [local]);

  const take = (f: File | undefined) => {
    if (!f) return;
    if (!ACCEPT.split(",").includes(f.type)) return setProblem("الصيغة لازم تكون JPG أو PNG أو WEBP.");
    if (f.size > MAX) return setProblem("الصورة أكبر من 5 ميجا.");
    setProblem(null);
    onFile(f);
  };

  const preview = local ?? current;
  const message = problem ?? error;

  return (
    <div className="fld">
      <span className="block font-semibold text-sm mb-1.5">{label}</span>
      <label
        className={`upl${over ? " over" : ""}`}
        onDragEnter={(e) => { e.preventDefault(); setOver(true); }}
        onDragOver={(e) => { e.preventDefault(); setOver(true); }}
        onDragLeave={() => setOver(false)}
        onDrop={(e) => { e.preventDefault(); setOver(false); take(e.dataTransfer.files[0]); }}
      >
        <UploadIcon />
        <b className="block text-d-ink">اسحب صورة هنا أو اختر من جهازك</b>
        <small>{hint}</small>
        <input type="file" accept={ACCEPT} className="sr-only" aria-invalid={!!message} onChange={(e) => { take(e.target.files?.[0]); e.target.value = ""; }} />
        {preview && (
          <span className="block mt-3">
            {/* eslint-disable-next-line @next/next/no-img-element -- preview of a local file or an API image */}
            <img src={preview} alt="" className={`rounded-xl w-full h-36 ${contain ? "object-contain bg-white p-3" : "object-cover"}`} />
          </span>
        )}
      </label>
      {message && <p className="ferr" role="alert">{message}</p>}
    </div>
  );
}
