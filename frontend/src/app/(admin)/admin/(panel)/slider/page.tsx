"use client";

import ContentManager from "@/components/admin/ContentManager";
import type { Slide } from "@/lib/admin/types";

export default function SliderPage() {
  return (
    <ContentManager<Slide>
      resource="slides"
      title="السلايدر"
      intro="أول شريحة منشورة هي عنوان الرئيسية، وأول 3 صور بيظهروا جنبه. اسحب لإعادة الترتيب."
      noun="الشريحة"
      addLabel="شريحة جديدة"
      listClass=""
      image={{ name: "image", label: "الصورة", hint: "المقاس المناسب 1920×900، JPG أو PNG أو WEBP حتى 5 ميجا" }}
      fields={[
        { name: "title", label: "العنوان", labelEn: "Title", required: true, max: 255 },
        { name: "subtitle", label: "السطر اللي تحت العنوان", labelEn: "Subtitle", type: "textarea", rows: 2, max: 500 },
        { name: "button", label: "نص الزرار", labelEn: "Button text", max: 60 },
      ]}
      shared={[{ name: "button_url", label: "رابط الزرار", ltr: true, max: 500, hint: "صفحة في الموقع زي /products/ واللغة بتتضاف لوحدها، أو رابط كامل يبدأ بـ https://" }]}
      card={(s, c) => (
        <div key={s.id} className="slide-card" {...c.drag}>
          {c.grip}
          {/* eslint-disable-next-line @next/next/no-img-element -- API image */}
          <img src={s.image_url} alt="" />
          <div className="min-w-0">
            <b className="font-display block truncate">{s.title_ar}</b>
            <small className="text-d-mute">{s.button_ar ? `الزرار: ${s.button_ar}` : "من غير زرار"}</small>
            {s.missing_en && <span className="pill ms-2" style={{ "--c": "var(--rust)" } as React.CSSProperties}>ناقص ترجمة</span>}
          </div>
          <div className="flex items-center gap-2">
            {c.toggle}
            {c.actions}
          </div>
        </div>
      )}
    />
  );
}
