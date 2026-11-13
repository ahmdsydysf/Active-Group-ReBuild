"use client";

import ContentManager from "@/components/admin/ContentManager";
import type { Partner } from "@/lib/admin/types";

export default function PartnersPage() {
  return (
    <ContentManager<Partner>
      resource="partners"
      title="الشركاء"
      intro="اللوجوهات اللي بتتحرك في الرئيسية. اسحب لإعادة الترتيب."
      noun="الشريك"
      addLabel="شريك جديد"
      listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
      image={{ name: "logo", label: "اللوجو", hint: "PNG أو WEBP بخلفية شفافة، حتى 5 ميجا", contain: true }}
      fields={[
        { name: "name", label: "الاسم", labelEn: "Name", required: true, max: 120 },
        { name: "field", label: "المجال", labelEn: "Field", max: 120, hint: "كلمة أو اتنين زي «مبيدات» أو «أسمدة»" },
        { name: "description", label: "نبذة", labelEn: "About", type: "textarea", rows: 3, max: 1000 },
      ]}
      shared={[{ name: "website", label: "الموقع الإلكتروني", type: "url", ltr: true, max: 500 }]}
      card={(p, c) => (
        <div key={p.id} className="tile flex flex-col gap-3" {...c.drag}>
          <div className="flex items-start justify-between gap-2">
            {c.grip}
            {c.toggle}
          </div>
          <div className="bg-white rounded-2xl h-24 grid place-items-center border border-d-line">
            {/* eslint-disable-next-line @next/next/no-img-element -- API image */}
            <img src={p.logo_url} alt={p.name_ar} className="h-10 w-auto max-w-[80%] object-contain" />
          </div>
          <div>
            <b className="font-display">{p.name_ar}</b>
            {p.missing_en && <span className="pill ms-2" style={{ "--c": "var(--rust)" } as React.CSSProperties}>ناقص ترجمة</span>}
            {p.field_ar && <small className="block text-d-mute">{p.field_ar}</small>}
          </div>
          {c.actions}
        </div>
      )}
    />
  );
}
