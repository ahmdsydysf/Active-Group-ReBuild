"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import FileField from "@/components/admin/FileField";
import ImageField from "@/components/admin/ImageField";
import LangTabs from "@/components/admin/LangTabs";
import Repeater, { type Item } from "@/components/admin/Repeater";
import RichText from "@/components/admin/RichText";
import Switch from "@/components/admin/Switch";
import TextField, { type Field } from "@/components/admin/TextField";
import { api } from "@/lib/admin/api";
import { ago } from "@/lib/admin/format";
import { useRecord } from "@/lib/admin/useRecord";

type Category = { id: number; name_ar: string; types: { id: number; name_ar: string }[] };

const LANG_FIELDS = ["name", "active_ingredient", "formulation", "summary", "body", "packs", "safety", "seo_title", "seo_description", "doses", "faqs"];
const BLANK = {
  ...Object.fromEntries(LANG_FIELDS.flatMap((f) => [`${f}_ar`, `${f}_en`]).map((k) => [k, /doses|faqs/.test(k) ? [] : ""])),
  product_type_id: "", factory_id: "", slug: "", registration_no: "", is_active: true, is_featured: false, in_stock: true,
};

const DOSE_COLS = [
  { key: "crop", label: "المحصول" }, { key: "pest", label: "الآفة أو الغرض" }, { key: "rate", label: "المعدل" },
  { key: "interval", label: "الفترة بين الرشات" }, { key: "phi", label: "فترة الأمان" },
];
const DOSE_COLS_EN = [
  { key: "crop", label: "Crop" }, { key: "pest", label: "Pest or use" }, { key: "rate", label: "Rate" }, { key: "interval", label: "Interval" }, { key: "phi", label: "PHI" },
];
const FAQ_COLS = [{ key: "q", label: "السؤال" }, { key: "a", label: "الإجابة", wide: true, textarea: true }];
const FAQ_COLS_EN = [{ key: "q", label: "Question" }, { key: "a", label: "Answer", wide: true, textarea: true }];

export default function ProductFormPage() {
  const id = useSearchParams().get("id");
  const r = useRecord("products", id, BLANK, "/admin/products/");
  const [cats, setCats] = useState<Category[]>([]);
  const [factories, setFactories] = useState<[string, string][]>([]);
  const [category, setCategory] = useState("");
  const [image, setImage] = useState<File | null>(null);
  const [leaflet, setLeaflet] = useState<File | null>(null);
  const [removeLeaflet, setRemoveLeaflet] = useState(false);

  useEffect(() => {
    api<{ data: Category[] }>("/product-categories").then((x) => setCats(x.data), () => {});
    api<{ data: { id: number; name_ar: string }[] }>("/factories").then((x) => setFactories(x.data.map((f) => [String(f.id), f.name_ar])), () => {});
  }, []);

  const v = r.values;
  // The category select only narrows the type list; the record stores the type.
  const typeId = String(v?.product_type_id ?? "");
  const shownCategory = category || String(cats.find((c) => c.types.some((t) => String(t.id) === typeId))?.id ?? "");
  const types = cats.find((c) => String(c.id) === shownCategory)?.types ?? [];

  if (r.loadError) return <p className="empty" role="alert">{r.loadError}</p>;
  if (!v) return <p className="empty" aria-busy="true">بنحمّل…</p>;

  const str = (k: string) => String(v[k] ?? "");
  const field = (f: Field) => <TextField key={f.name} field={f} value={str(f.name)} error={r.errors[f.name]} onChange={r.set} />;
  const rowsError = (k: string) => Object.entries(r.errors).find(([e]) => e.startsWith(`${k}.`))?.[1];
  const lang = (l: "ar" | "en") => {
    const en = l === "en";
    return (
      <>
        {field({ name: `name_${l}`, label: en ? "Product name" : "اسم المنتج", required: !en, max: 160 })}
        {field({ name: `active_ingredient_${l}`, label: en ? "Active ingredient" : "المادة الفعالة", max: 255 })}
        {field({ name: `formulation_${l}`, label: en ? "Formulation" : "نوع التركيبة", max: 255, hint: en ? "e.g. WP, EC, SC" : "مثلًا: مسحوق قابل للبلل WP" })}
        {field({ name: `summary_${l}`, label: en ? "Short description" : "وصف مختصر", type: "textarea", rows: 3, max: 500 })}
        <RichText key={`${l}-${r.version}`} label={en ? "Full details" : "التفاصيل الكاملة"} value={str(`body_${l}`)} onChange={(h) => r.set(`body_${l}`, h)} dir={en ? "ltr" : undefined} />
        {field({ name: `packs_${l}`, label: en ? "Packs" : "العبوات", max: 255, hint: en ? "e.g. 250 g, 500 g, 1 kg" : "مثلًا: 250 جم، 500 جم، 1 كجم" })}
        {field({ name: `safety_${l}`, label: en ? "Safety and storage" : "الأمان والتخزين", type: "textarea", rows: 3, max: 2000 })}
      </>
    );
  };
  const enMissing = ["name", "summary"].some((f) => v[`${f}_ar`] && !v[`${f}_en`]);
  const errorLang = (["ar", "en"] as const).find((l) => Object.keys(r.errors).some((k) => k.includes(`_${l}`))) ?? null;
  const name = str("name_ar");

  return (
    <form onSubmit={(e) => { e.preventDefault(); r.save({ image, leaflet }, removeLeaflet ? { remove_leaflet: true } : {}).then((d) => d && (setImage(null), setLeaflet(null), setRemoveLeaflet(false))); }}>
      <div className="dhead">
        <div>
          <div className="dcrumb"><Link href="/admin/products/">المنتجات</Link> / {id ? "تعديل" : "جديد"}</div>
          <h1>{id ? `تعديل: ${name}` : "منتج جديد"}</h1>
          <p>{r.record ? `آخر تعديل ${ago(String(r.record.updated_at))}.` : "اكتب البيانات بالعربي، والإنجليزي لو متاح."}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          {id && <button className="dbtn text-rust" type="button" onClick={r.remove}>حذف</button>}
          {r.record && !!v.is_active && <a className="dbtn" href={`/ar/products/${str("slug")}/`} target="_blank" rel="noopener">معاينة</a>}
          <button className="dbtn pri" type="submit" disabled={r.saving}>{r.saving ? "بنحفظ…" : "حفظ"}</button>
        </div>
      </div>
      <div className="form-grid">
        <div>
          <section className="tile fsec">
            <h2>البيانات الأساسية</h2>
            <LangTabs key={r.version} ar={lang("ar")} en={lang("en")} enMissing={enMissing} invalid={errorLang} />
          </section>
          <section className="tile fsec">
            <h2>معدلات الاستخدام</h2>
            <p>الجرعات اللي هتظهر في جدول صفحة المنتج.</p>
            <LangTabs
              key={`d${r.version}`}
              ar={<Repeater cols={DOSE_COLS} rows={v.doses_ar as Item[]} onChange={(x) => r.set("doses_ar", x)} addLabel="إضافة صف" max={40} error={rowsError("doses_ar")} />}
              en={<Repeater cols={DOSE_COLS_EN} rows={v.doses_en as Item[]} onChange={(x) => r.set("doses_en", x)} addLabel="Add row" max={40} error={rowsError("doses_en")} />}
            />
          </section>
          <section className="tile fsec">
            <h2>أسئلة شائعة</h2>
            <p>بتظهر تحت جدول الجرعات في صفحة المنتج.</p>
            <LangTabs
              key={`f${r.version}`}
              ar={<Repeater cols={FAQ_COLS} rows={v.faqs_ar as Item[]} onChange={(x) => r.set("faqs_ar", x)} addLabel="إضافة سؤال" max={20} error={rowsError("faqs_ar")} />}
              en={<Repeater cols={FAQ_COLS_EN} rows={v.faqs_en as Item[]} onChange={(x) => r.set("faqs_en", x)} addLabel="Add question" max={20} error={rowsError("faqs_en")} />}
            />
          </section>
          <section className="tile fsec">
            <h2>محركات البحث</h2>
            {field({ name: "slug", label: "رابط الصفحة", ltr: true, max: 160, hint: `activegroup-eg.com/ar/products/${str("slug") || "…"} . سيبه فاضي ويتعمل من الاسم الإنجليزي.` })}
            <LangTabs
              key={`s${r.version}`}
              ar={<>{field({ name: "seo_title_ar", label: "عنوان SEO", max: 70 })}{field({ name: "seo_description_ar", label: "وصف SEO", type: "textarea", rows: 2, max: 170 })}</>}
              en={<>{field({ name: "seo_title_en", label: "SEO title", max: 70 })}{field({ name: "seo_description_en", label: "SEO description", type: "textarea", rows: 2, max: 170 })}</>}
            />
          </section>
        </div>
        <div>
          <section className="tile fsec">
            <h2>النشر</h2>
            {([["is_active", "منشور في الموقع"], ["is_featured", "منتج مميز في الرئيسية"], ["in_stock", "متوفر"]] as const).map(([k, label]) => (
              <div key={k} className="flex justify-between items-center mb-3">
                <span>{label}</span>
                <Switch on={!!v[k]} label={label} onChange={(on) => r.set(k, on)} />
              </div>
            ))}
          </section>
          <section className="tile fsec">
            <h2>التصنيف</h2>
            <div className="fld">
              <label>
                القسم
                <select className="mt-1.5" value={shownCategory} onChange={(e) => { setCategory(e.target.value); r.set("product_type_id", ""); }}>
                  <option value="">اختار القسم</option>
                  {cats.map((c) => <option key={c.id} value={c.id}>{c.name_ar}</option>)}
                </select>
              </label>
            </div>
            {field({ name: "product_type_id", label: "النوع", type: "select", required: true, options: types.map((t) => [String(t.id), t.name_ar]) })}
            {field({ name: "factory_id", label: "المصنع", type: "select", options: factories })}
            {field({ name: "registration_no", label: "رقم التسجيل", ltr: true, max: 60 })}
          </section>
          <section className="tile fsec">
            <h2>صورة المنتج</h2>
            <ImageField label="الصورة" hint="اختياري. لو مفيش صورة، الموقع بيرسم العبوة بلون القسم." contain current={r.record?.image_url as string | null} file={image} onFile={setImage} error={r.errors.image} />
          </section>
          <section className="tile fsec">
            <h2>النشرة الفنية</h2>
            <FileField label="ارفع النشرة الفنية" current={r.record?.leaflet_url as string | null} file={leaflet} onFile={setLeaflet} remove={removeLeaflet} onRemove={setRemoveLeaflet} error={r.errors.leaflet} />
          </section>
        </div>
      </div>
    </form>
  );
}
