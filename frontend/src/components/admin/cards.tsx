import type { ReactNode } from "react";

type C = { drag: object; grip: ReactNode; toggle: ReactNode; actions: ReactNode };

/** The card most sections use: optional picture, title, a line under it, and the controls row. */
export function SimpleCard({ id, c, image, title, sub, badge, missingEn, round }: { id: number; c: C; image?: string | null; title: string; sub?: ReactNode; badge?: ReactNode; missingEn?: boolean; round?: boolean }) {
  return (
    <div key={id} className="tile flex flex-col gap-3" {...c.drag}>
      {image !== undefined && (
        <div className={`overflow-hidden bg-d-bg ${round ? "w-24 h-24 rounded-full mx-auto" : "aspect-[16/9] rounded-xl"}`}>
          {image && (
            // eslint-disable-next-line @next/next/no-img-element -- previews come straight from the API
            <img src={image} alt="" loading="lazy" className="w-full h-full object-cover" />
          )}
        </div>
      )}
      <div className={round ? "text-center" : ""}>
        <b className="block font-display text-[17px]">{title}</b>
        {sub && <small className="text-d-mute">{sub}</small>}
      </div>
      {(badge || missingEn) && (
        <div className="flex flex-wrap gap-1.5">
          {badge}
          {missingEn && <span className="pill" style={{ "--c": "var(--rust)" } as React.CSSProperties}>ناقص ترجمة</span>}
        </div>
      )}
      <div className="flex items-center justify-between gap-2 border-t border-d-line pt-3 mt-auto">
        <div className="flex items-center gap-2">
          {c.grip}
          {c.toggle}
        </div>
        {c.actions}
      </div>
    </div>
  );
}

/** Radio swatches for a colour choice (Testimonial, Feature, Category colours). */
export function ColorPicker({ legend, colors, value, onChange }: { legend: string; colors: Record<string, [string, string]>; value: string; onChange: (v: string) => void }) {
  return (
    <fieldset className="fld">
      <legend className="block font-semibold text-sm mb-2">{legend}</legend>
      <div className="flex flex-wrap gap-2">
        {Object.entries(colors).map(([key, [color, label]]) => (
          <label key={key} className="swatch" style={{ "--c": color } as React.CSSProperties}>
            <input type="radio" name={legend} value={key} checked={value === key} onChange={() => onChange(key)} className="sr-only" />
            <span aria-hidden="true" />
            {label}
          </label>
        ))}
      </div>
    </fieldset>
  );
}

export const PALETTE: Record<string, [string, string]> = {
  field: ["var(--field)", "أخضر"],
  deep: ["var(--deep)", "أخضر غامق"],
  sky: ["var(--sky)", "أزرق"],
  rust: ["var(--rust)", "طوبي"],
  plum: ["#7A2140", "نبيتي"],
  wheat: ["var(--wheat)", "برتقالي"],
  bloom: ["var(--bloom)", "وردي"],
};

export const pick = (keys: string[]) => Object.fromEntries(keys.map((k) => [k, PALETTE[k]]));
