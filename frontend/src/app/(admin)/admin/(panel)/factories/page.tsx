"use client";

import ContentManager from "@/components/admin/ContentManager";
import { SimpleCard } from "@/components/admin/cards";
import type { Row } from "@/lib/admin/types";

type Factory = Row & { name_ar: string; description_ar: string | null; address_ar: string | null; image_url: string | null };

export default function FactoriesPage() {
  return (
    <ContentManager<Factory>
      resource="factories"
      title="المصانع"
      intro="بتظهر في صفحة من نحن، وبتختارها في صفحة كل منتج."
      noun="المصنع"
      addLabel="مصنع جديد"
      listClass="grid sm:grid-cols-2 xl:grid-cols-3 gap-4"
      image={{ name: "image", label: "صورة المصنع", hint: "اختياري. JPG أو PNG أو WEBP لحد 5 ميجا." }}
      fields={[
        { name: "name", label: "اسم المصنع", labelEn: "Name", required: true, max: 120 },
        { name: "description", label: "الشغل اللي بيتعمل فيه", labelEn: "What it does", type: "textarea", rows: 2, max: 500 },
        { name: "address", label: "العنوان", labelEn: "Address", max: 255 },
      ]}
      card={(f, c) => <SimpleCard key={f.id} id={f.id} c={c} image={f.image_url} title={f.name_ar} sub={f.description_ar} missingEn={f.missing_en} />}
    />
  );
}
