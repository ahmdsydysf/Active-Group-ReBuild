"use client";

import ContentManager from "@/components/admin/ContentManager";
import { SimpleCard } from "@/components/admin/cards";
import type { Row } from "@/lib/admin/types";

type Service = Row & { title_ar: string; summary_ar: string | null; icon: string; image_url: string };

const SERVICE_ICONS: [string, string][] = [["lab", "معمل"], ["leaf", "ورقة نبات"], ["shield", "درع حماية"], ["visit", "مهندسين"], ["training", "كتاب"], ["truck", "عربية نقل"]];

export default function ServicesPage() {
  return (
    <ContentManager<Service>
      resource="services"
      title="الخدمات الفنية"
      intro="كروت صفحة الخدمات. كل كارت فيه زرار «اطلب الخدمة» بيودي لنموذج التواصل."
      noun="الخدمة"
      addLabel="خدمة جديدة"
      listClass="grid sm:grid-cols-2 xl:grid-cols-3 gap-4"
      defaults={{ icon: "leaf" }}
      image={{ name: "image", label: "صورة الخدمة", hint: "مطلوبة. صورة عرضية بتتقص من فوق وتحت." }}
      fields={[
        { name: "title", label: "اسم الخدمة", labelEn: "Title", required: true, max: 120 },
        { name: "summary", label: "وصف قصير", labelEn: "Summary", type: "textarea", rows: 3, max: 500 },
      ]}
      shared={[{ name: "icon", label: "الأيقونة", type: "select", required: true, options: SERVICE_ICONS }]}
      card={(s, c) => <SimpleCard key={s.id} id={s.id} c={c} image={s.image_url} title={s.title_ar} sub={s.summary_ar} missingEn={s.missing_en} />}
    />
  );
}
