"use client";

import { PlusIcon, TrashIcon } from "./icons";

export type Col = { key: string; label: string; wide?: boolean; textarea?: boolean };
export type Item = Record<string, string>;

/**
 * Editable rows of a table, such as dose rates or questions and answers.
 * The grid follows the number of columns; the last cell is the delete button.
 */
export default function Repeater({ cols, rows, onChange, addLabel, max, error }: { cols: Col[]; rows: Item[]; onChange: (rows: Item[]) => void; addLabel: string; max: number; error?: string }) {
  const grid = { "--cols": `${cols.map((c) => (c.wide ? "minmax(0, 2fr)" : "minmax(0, 1fr)")).join(" ")} 40px` } as React.CSSProperties;
  const set = (i: number, k: string, v: string) => onChange(rows.map((r, j) => (j === i ? { ...r, [k]: v } : r)));
  return (
    <>
      {rows.length > 0 && (
        <div className="repeat-row text-d-mute text-sm font-semibold max-sm:!hidden" style={grid} aria-hidden="true">
          {cols.map((c) => <span key={c.key}>{c.label}</span>)}
          <span />
        </div>
      )}
      {rows.map((r, i) => (
        <div key={i} className="repeat-row" style={grid}>
          {cols.map((c) =>
            c.textarea ? (
              <textarea key={c.key} className="dinput" rows={2} aria-label={`${c.label} ${i + 1}`} placeholder={c.label} value={r[c.key] ?? ""} onChange={(e) => set(i, c.key, e.target.value)} />
            ) : (
              <input key={c.key} className="dinput" aria-label={`${c.label} ${i + 1}`} placeholder={c.label} value={r[c.key] ?? ""} onChange={(e) => set(i, c.key, e.target.value)} />
            ),
          )}
          <button className="iconbtn !w-10 !h-10 text-rust" type="button" aria-label={`حذف الصف ${i + 1}`} onClick={() => onChange(rows.filter((_, j) => j !== i))}>
            <TrashIcon />
          </button>
        </div>
      ))}
      {error && <p className="ferr" role="alert">{error}</p>}
      {rows.length < max && (
        <button className="dbtn mt-2" type="button" onClick={() => onChange([...rows, Object.fromEntries(cols.map((c) => [c.key, ""]))])}>
          <PlusIcon />
          {addLabel}
        </button>
      )}
    </>
  );
}
