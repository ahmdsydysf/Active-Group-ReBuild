"use client";

import ContentManager from "@/components/admin/ContentManager";
import type { Testimonial } from "@/lib/admin/types";

/** Card colours the site knows (Testimonial::COLORS on the API). */
const COLORS: Record<string, [string, string]> = {
  field: ["var(--field)", "أخضر"],
  deep: ["var(--deep)", "أخضر غامق"],
  sky: ["var(--sky)", "أزرق"],
  rust: ["var(--rust)", "طوبي"],
  plum: ["#7A2140", "نبيتي"],
  wheat: ["var(--wheat)", "برتقالي"],
  bloom: ["var(--bloom)", "وردي"],
};

const initial = (name: string) => Array.from(name.replace(/^(م|أ|د|الحاج)\.?\s+/u, ""))[0];

export default function TestimonialsPage() {
  return (
    <ContentManager<Testimonial>
      resource="testimonials"
      title="آراء العملاء"
      intro="الكروت اللي بتتراكب مع السكرول في الرئيسية. أول 5 منشورين بيظهروا هناك."
      noun="الرأي"
      addLabel="رأي جديد"
      listClass="grid md:grid-cols-2 gap-4"
      defaults={{ color: "field" }}
      fields={[
        { name: "quote", label: "الرأي", labelEn: "Quote", type: "textarea", rows: 4, required: true, max: 600 },
        { name: "name", label: "الاسم", labelEn: "Name", required: true, max: 120 },
        { name: "role", label: "الصفة", labelEn: "Role", max: 160 },
      ]}
      extra={(v, set) => (
        <fieldset className="fld">
          <legend className="block font-semibold text-sm mb-2">لون الكارت</legend>
          <div className="flex flex-wrap gap-2">
            {Object.entries(COLORS).map(([key, [color, label]]) => (
              <label key={key} className="swatch" style={{ "--c": color } as React.CSSProperties}>
                <input type="radio" name="color" value={key} checked={v.color === key} onChange={() => set("color", key)} className="sr-only" />
                <span aria-hidden="true" />
                {label}
              </label>
            ))}
          </div>
        </fieldset>
      )}
      card={(t, c) => {
        const color = COLORS[t.color]?.[0] ?? COLORS.field[0];
        return (
          <div key={t.id} className="tile flex flex-col gap-3" style={{ borderTop: `6px solid ${color}` }} {...c.drag}>
            <p className="font-display font-semibold text-[17px] leading-relaxed">«{t.quote_ar}»</p>
            <div className="flex items-center gap-3 mt-auto">
              <i className="w-10 h-10 rounded-full grid place-items-center not-italic text-white font-display font-bold" style={{ background: color }}>
                {initial(t.name_ar)}
              </i>
              <div className="flex-1">
                <b className="block">{t.name_ar}</b>
                {t.role_ar && <small className="text-d-mute">{t.role_ar}</small>}
              </div>
              {t.missing_en && <span className="pill" style={{ "--c": "var(--rust)" } as React.CSSProperties}>ناقص ترجمة</span>}
            </div>
            <div className="flex items-center justify-between gap-2 border-t border-d-line pt-3">
              <div className="flex items-center gap-2">
                {c.grip}
                {c.toggle}
              </div>
              {c.actions}
            </div>
          </div>
        );
      }}
    />
  );
}
