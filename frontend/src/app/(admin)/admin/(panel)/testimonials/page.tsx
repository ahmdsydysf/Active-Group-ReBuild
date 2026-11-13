"use client";

import ContentManager from "@/components/admin/ContentManager";
import { ColorPicker, PALETTE } from "@/components/admin/cards";
import { initial } from "@/lib/voices";
import type { Testimonial } from "@/lib/admin/types";

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
      extra={(v, set) => <ColorPicker legend="لون الكارت" colors={PALETTE} value={v.color} onChange={(c) => set("color", c)} />}
      card={(t, c) => {
        const color = PALETTE[t.color]?.[0] ?? PALETTE.field[0];
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
