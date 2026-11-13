"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import ImageField from "@/components/admin/ImageField";
import LangTabs from "@/components/admin/LangTabs";
import RichText from "@/components/admin/RichText";
import Switch from "@/components/admin/Switch";
import TextField, { type Field } from "@/components/admin/TextField";
import { api } from "@/lib/admin/api";
import { ago, toLocalInput } from "@/lib/admin/format";
import { useRecord } from "@/lib/admin/useRecord";

const SECTIONS = { advice: ["النصائح الفنية", "/admin/advice/", "experiences"], blog: ["المدونة", "/admin/blog/", "blog"] } as const;
type Section = keyof typeof SECTIONS;

const BLANK = {
  section: "advice", post_category_id: "", author_id: "", slug: "", title_ar: "", title_en: "", excerpt_ar: "", excerpt_en: "",
  body_ar: "", body_en: "", seo_description_ar: "", seo_description_en: "", is_active: false, is_featured: false, published_at: "",
};

export default function PostFormPage() {
  const params = useSearchParams();
  const id = params.get("id");
  const fromUrl = params.get("section") === "blog" ? "blog" : "advice";
  const r = useRecord("posts", id, { ...BLANK, section: fromUrl }, SECTIONS[fromUrl][1]);
  const [cats, setCats] = useState<[string, string][]>([]);
  const [authors, setAuthors] = useState<[string, string][]>([]);
  const [image, setImage] = useState<File | null>(null);
  const v = r.values;
  const section = (v?.section as Section) ?? fromUrl;

  useEffect(() => {
    api<{ data: { id: number; name_ar: string }[] }>(`/post-categories?section=${section}`).then((x) => setCats(x.data.map((c) => [String(c.id), c.name_ar])), () => {});
  }, [section]);
  useEffect(() => {
    api<{ data: { id: number; name_ar: string }[] }>("/team").then((x) => setAuthors(x.data.map((m) => [String(m.id), m.name_ar])), () => {});
  }, []);

  if (r.loadError) return <p className="empty" role="alert">{r.loadError}</p>;
  if (!v) return <p className="empty" aria-busy="true">بنحمّل…</p>;

  const [label, listPath, sitePath] = SECTIONS[section];
  const str = (k: string) => String(v[k] ?? "");
  const field = (f: Field) => <TextField key={f.name} field={f} value={str(f.name)} error={r.errors[f.name]} onChange={r.set} />;
  const lang = (l: "ar" | "en") => {
    const en = l === "en";
    return (
      <>
        {field({ name: `title_${l}`, label: en ? "Title" : "العنوان", required: !en, max: 200 })}
        {field({ name: `excerpt_${l}`, label: en ? "Excerpt" : "الملخص", type: "textarea", rows: 2, max: 500, hint: en ? undefined : "بيظهر في كارت المقال وتحت العنوان." })}
        <RichText key={`${l}-${r.version}`} label={en ? "Content" : "المحتوى"} value={str(`body_${l}`)} onChange={(h) => r.set(`body_${l}`, h)} dir={en ? "ltr" : undefined} />
        {field({ name: `seo_description_${l}`, label: en ? "SEO description" : "وصف SEO", type: "textarea", rows: 2, max: 170 })}
      </>
    );
  };
  const errorLang = (["ar", "en"] as const).find((l) => Object.keys(r.errors).some((k) => k.endsWith(`_${l}`))) ?? null;
  // The input shows local time; the API gets an ISO date with the offset.
  const submit = (publish?: boolean) => {
    const when = str("published_at") ? new Date(str("published_at")).toISOString() : "";
    return r.save({ image }, { published_at: when, ...(publish === undefined ? {} : { is_active: publish }) }).then((d) => d && setImage(null));
  };

  return (
    <form onSubmit={(e) => { e.preventDefault(); submit(); }}>
      <div className="dhead">
        <div>
          <div className="dcrumb"><Link href={listPath}>{label}</Link> / {id ? "تعديل" : "جديد"}</div>
          <h1>{id ? "تعديل مقال" : "مقال جديد"}</h1>
          <p>{r.record ? `آخر تعديل ${ago(String(r.record.updated_at))}.` : "اكتب بالعربي، والإنجليزي لو متاح."}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          {id && <button className="dbtn text-rust" type="button" onClick={r.remove}>حذف</button>}
          {r.record?.status === "published" && <a className="dbtn" href={`/ar/${sitePath}/${str("slug")}/`} target="_blank" rel="noopener">معاينة</a>}
          <button className="dbtn" type="button" disabled={r.saving} onClick={() => submit(false)}>حفظ كمسودة</button>
          <button className="dbtn pri" type="button" disabled={r.saving} onClick={() => submit(true)}>{r.saving ? "بنحفظ…" : "نشر"}</button>
        </div>
      </div>
      <div className="form-grid">
        <div>
          <section className="tile fsec">
            <LangTabs key={r.version} ar={lang("ar")} en={lang("en")} enMissing={!!v.title_ar && !v.title_en} invalid={errorLang} />
          </section>
        </div>
        <div>
          <section className="tile fsec">
            <h2>النشر</h2>
            <div className="flex justify-between items-center mb-3">
              <span>منشور</span>
              <Switch on={!!v.is_active} onChange={(on) => r.set("is_active", on)} />
            </div>
            <div className="flex justify-between items-center mb-3">
              <span>مميز في الرئيسية</span>
              <Switch on={!!v.is_featured} label="مميز" onChange={(on) => r.set("is_featured", on)} />
            </div>
            <TextField
              field={{ name: "published_at", label: "تاريخ النشر", type: "datetime-local", hint: "لو اخترت تاريخ جاي، المقال هيظهر لوحده في ميعاده. لو سبته فاضي، هيتنشر دلوقتي." }}
              value={toLocalInput(str("published_at") || null)}
              error={r.errors.published_at}
              onChange={r.set}
            />
            <TextField
              field={{ name: "section", label: "مكانه في الموقع", type: "select", required: true, options: [["advice", "نصائح وخبرات"], ["blog", "المدونة"]] }}
              value={section}
              onChange={(k, x) => { r.set(k, x); r.set("post_category_id", ""); }}
            />
            {field({ name: "post_category_id", label: "التصنيف", type: "select", options: cats })}
            {field({ name: "author_id", label: "الكاتب", type: "select", options: authors, hint: "من فريق العمل." })}
          </section>
          <section className="tile fsec">
            <h2>صورة الغلاف</h2>
            <ImageField label="الصورة" hint="مطلوبة. JPG أو PNG أو WEBP لحد 5 ميجا." current={r.record?.image_url as string | null} file={image} onFile={setImage} error={r.errors.image} />
          </section>
          <section className="tile fsec">
            <h2>رابط الصفحة</h2>
            {field({ name: "slug", label: "الرابط", ltr: true, max: 160, hint: "سيبه فاضي ويتعمل من العنوان." })}
          </section>
        </div>
      </div>
    </form>
  );
}
