"use client";

import { useEffect, useState } from "react";
import ContentManager from "@/components/admin/ContentManager";
import { ColorPicker, PALETTE, pick, SimpleCard } from "@/components/admin/cards";
import { api } from "@/lib/admin/api";
import type { Row } from "@/lib/admin/types";

type Category = Row & { name_ar: string; description_ar: string | null; color: string; products_count: number; slug: string };
type ProductType = Row & { name_ar: string; product_category_id: number };

const COLORS = pick(["rust", "field", "wheat", "sky", "plum", "deep", "bloom"]);

/** Product categories (the coloured families) and the types inside each one. */
export default function CategoriesPage() {
  const [cats, setCats] = useState<[string, string][]>([]);
  const [version, setVersion] = useState(0);

  useEffect(() => {
    api<{ data: Category[] }>("/product-categories").then((r) => setCats(r.data.map((c) => [String(c.id), c.name_ar])), () => {});
  }, [version]);
  const catName = (id: number) => cats.find(([v]) => v === String(id))?.[1] ?? "";

  return (
    <>
      <ContentManager<Category>
        resource="product-categories"
        title="الأقسام والأنواع"
        intro="لون القسم هو لون كروت منتجاته في الموقع: الطوبي للمبيدات، الأخضر للأسمدة، البرتقالي لمنظمات النمو، الأزرق للعناصر الصغرى."
        noun="القسم"
        addLabel="قسم جديد"
        listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
        defaults={{ color: "field" }}
        onChange={() => setVersion((n) => n + 1)}
        fields={[
          { name: "name", label: "اسم القسم", labelEn: "Name", required: true, max: 120 },
          { name: "description", label: "وصف قصير", labelEn: "Description", type: "textarea", rows: 2, max: 500 },
        ]}
        shared={[{ name: "slug", label: "الرابط", ltr: true, max: 120, hint: "حروف إنجليزي صغيرة وشرطة. سيبه فاضي ويتعمل لوحده." }]}
        extra={(v, set) => <ColorPicker legend="اللون" colors={COLORS} value={v.color} onChange={(x) => set("color", x)} />}
        card={(cat, c) => (
          <SimpleCard key={cat.id} id={cat.id} c={c} title={cat.name_ar} sub={`${cat.products_count} منتج`} missingEn={cat.missing_en}
            badge={<span className="pill" style={{ "--c": PALETTE[cat.color]?.[0] } as React.CSSProperties}>{PALETTE[cat.color]?.[1]}</span>} />
        )}
      />
      {cats.length > 0 && (
        <ContentManager<ProductType>
          sub
          resource="product-types"
          title="الأنواع"
          intro="الأنواع جوه كل قسم (مبيد فطري، عناصر كبرى…). أول 3 في كل قسم بيظهروا في كارت القسم في الرئيسية."
          noun="النوع"
          addLabel="نوع جديد"
          listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
          defaults={{ product_category_id: cats[0][0] }}
          fields={[{ name: "name", label: "اسم النوع", labelEn: "Name", required: true, max: 120 }]}
          shared={[{ name: "product_category_id", label: "القسم", type: "select", required: true, options: cats }]}
          card={(t, c) => <SimpleCard key={t.id} id={t.id} c={c} title={t.name_ar} sub={catName(t.product_category_id)} missingEn={t.missing_en} />}
        />
      )}
    </>
  );
}
