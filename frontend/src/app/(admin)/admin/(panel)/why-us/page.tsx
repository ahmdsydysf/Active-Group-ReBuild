"use client";

import ContentManager from "@/components/admin/ContentManager";
import { ColorPicker, PALETTE, pick, SimpleCard } from "@/components/admin/cards";
import type { Row } from "@/lib/admin/types";

type Feature = Row & { title_ar: string; description_ar: string | null; icon: string; color: string };

const COLORS = pick(["field", "bloom", "sky", "wheat", "rust", "deep"]);

export default function WhyUsPage() {
  return (
    <ContentManager<Feature>
      resource="features"
      title="ليه تختار أكتيف"
      intro="البلاطات الملونة في صفحة من نحن. الأفضل أربعة بالظبط."
      noun="الميزة"
      addLabel="ميزة جديدة"
      listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
      defaults={{ icon: "shield", color: "field" }}
      fields={[
        { name: "title", label: "العنوان", labelEn: "Title", required: true, max: 120 },
        { name: "description", label: "الوصف", labelEn: "Description", type: "textarea", rows: 3, max: 300 },
      ]}
      shared={[{ name: "icon", label: "الأيقونة", type: "select", required: true, options: [["shield", "درع"], ["people", "ناس"], ["truck", "عربية نقل"], ["lab", "معمل"]] }]}
      extra={(v, set) => <ColorPicker legend="اللون" colors={COLORS} value={v.color} onChange={(x) => set("color", x)} />}
      card={(f, c) => (
        <SimpleCard key={f.id} id={f.id} c={c} title={f.title_ar} sub={f.description_ar} missingEn={f.missing_en}
          badge={<span className="pill" style={{ "--c": PALETTE[f.color]?.[0] } as React.CSSProperties}>{PALETTE[f.color]?.[1]}</span>} />
      )}
    />
  );
}
