"use client";

import { useEffect, useState } from "react";
import { CheckIcon } from "@/components/admin/icons";
import { useShell } from "@/components/admin/Shell";
import TextField, { type Field } from "@/components/admin/TextField";
import { api, ApiError, fieldErrors } from "@/lib/admin/api";
import type { Settings, StatInput } from "@/lib/admin/types";

/** Arabic and English side by side: `name` expands to `${name}_ar` and `${name}_en`. */
type Pair = Field & { pair?: true };

const TABS: { id: string; label: string; fields: Pair[] }[] = [
  {
    id: "general",
    label: "عام",
    fields: [
      { name: "site_name", label: "اسم الموقع", pair: true, required: true, max: 120 },
      { name: "footer_about", label: "نبذة الفوتر", type: "textarea", pair: true, max: 400 },
    ],
  },
  {
    id: "contact",
    label: "بيانات التواصل",
    fields: [
      { name: "phone1", label: "التليفون 1", type: "tel", ltr: true, max: 20 },
      { name: "phone2", label: "التليفون 2", type: "tel", ltr: true, max: 20 },
      { name: "whatsapp", label: "واتساب", type: "tel", ltr: true, max: 20, hint: "بكود الدولة أو من غيره: 01097003465 أو 201097003465" },
      { name: "email", label: "البريد الإلكتروني", type: "email", ltr: true, max: 120 },
      { name: "address", label: "العنوان", pair: true, max: 255 },
      { name: "hours", label: "مواعيد العمل", pair: true, max: 255 },
      { name: "map_url", label: "رابط الخريطة", type: "url", ltr: true, max: 500 },
      { name: "inbox_email", label: "إيميل استقبال الرسائل", type: "email", ltr: true, max: 120, hint: "رسائل فورم التواصل بتوصل على الإيميل ده، ومش بيظهر في الموقع." },
    ],
  },
  {
    id: "social",
    label: "السوشيال ميديا",
    fields: [
      { name: "facebook", label: "فيسبوك", type: "url", ltr: true, max: 500 },
      { name: "youtube", label: "يوتيوب", type: "url", ltr: true, max: 500 },
      { name: "linkedin", label: "لينكدإن", type: "url", ltr: true, max: 500 },
      { name: "instagram", label: "إنستجرام", type: "url", ltr: true, max: 500 },
    ],
  },
  { id: "numbers", label: "الأرقام", fields: [] },
  {
    id: "seo",
    label: "محركات البحث",
    fields: [
      { name: "seo_title", label: "عنوان الصفحة الرئيسية", pair: true, max: 70 },
      { name: "seo_description", label: "الوصف", type: "textarea", pair: true, max: 170, hint: "بيظهر تحت اسم الموقع في نتايج جوجل. أقل من 170 حرف." },
    ],
  },
];

const EMPTY_STAT: StatInput = { value: "", suffix: "", label_ar: "", label_en: "" };

export default function SettingsPage() {
  const { toast } = useShell();
  const [tab, setTab] = useState("general");
  const [values, setValues] = useState<Settings | null>(null);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    api<{ data: Settings }>("/settings").then((r) => setValues(r.data), () => {});
    const fromHash = () => {
      const id = location.hash.slice(1);
      if (TABS.some((t) => t.id === id)) setTab(id);
    };
    fromHash();
    addEventListener("hashchange", fromHash);
    return () => removeEventListener("hashchange", fromHash);
  }, []);

  if (!values) return <p className="empty" aria-busy="true">بنحمّل…</p>;

  const text = (k: string) => (values[k] as string | null) ?? "";
  const set = (k: string, v: string) => setValues((s) => s && { ...s, [k]: v });
  const stats = [0, 1, 2, 3].map((i) => values.stats[i] ?? EMPTY_STAT);
  const setStat = (i: number, k: keyof StatInput, v: string) => setValues((s) => s && { ...s, stats: stats.map((x, n) => (n === i ? { ...x, [k]: v } : x)) });

  const save = async () => {
    setSaving(true);
    setErrors({});
    try {
      const body = { ...values, stats: stats.filter((s) => String(s.value) !== "" || s.label_ar) };
      const r = await api<{ data: Settings }>("/settings", "PUT", body);
      setValues(r.data);
      toast("تم حفظ الإعدادات");
    } catch (e) {
      const errs = fieldErrors(e);
      setErrors(errs);
      const first = Object.keys(errs)[0]?.replace(/_(ar|en)$/, "").replace(/\..*/, "");
      const where = TABS.find((t) => t.fields.some((f) => f.name === first)) ?? (first === "stats" ? TABS[3] : null);
      if (where) setTab(where.id);
      toast(e instanceof ApiError && e.status === 422 ? "فيه حقول محتاجة تتصلّح" : e instanceof ApiError ? e.message : "حصلت مشكلة.");
    } finally {
      setSaving(false);
    }
  };

  const field = (f: Pair) =>
    f.pair ? (
      <div key={f.name} className="grid md:grid-cols-2 gap-x-6">
        <TextField field={{ ...f, name: `${f.name}_ar`, label: `${f.label} (عربي)` }} value={text(`${f.name}_ar`)} error={errors[`${f.name}_ar`]} onChange={set} />
        <TextField field={{ ...f, name: `${f.name}_en`, label: `${f.label} (إنجليزي)`, required: false, ltr: true }} value={text(`${f.name}_en`)} error={errors[`${f.name}_en`]} onChange={set} />
      </div>
    ) : (
      <TextField key={f.name} field={f} value={text(f.name)} error={errors[f.name]} onChange={set} />
    );

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        save();
      }}
      noValidate
    >
      <div className="dhead">
        <div>
          <h1>إعدادات الموقع</h1>
          <p>البيانات العامة اللي بتظهر في كل صفحات الموقع.</p>
        </div>
        <button className="dbtn pri" type="submit" disabled={saving} aria-busy={saving}>
          <CheckIcon />
          {saving ? "بنحفظ…" : "حفظ"}
        </button>
      </div>
      <section className="tile">
        <div className="vtabs" role="tablist">
          {TABS.map((t) => (
            <button key={t.id} type="button" role="tab" aria-selected={tab === t.id} aria-controls={`pane-${t.id}`} onClick={() => setTab(t.id)}>
              {t.label}
            </button>
          ))}
        </div>
        <div className="max-w-3xl">
          {TABS.map((t) => (
            <div key={t.id} id={`pane-${t.id}`} role="tabpanel" hidden={tab !== t.id}>
              {t.id === "numbers" ? (
                <>
                  <p className="hint-t !mt-0 mb-4">أربع أرقام في قسم «نجاحاتنا بالأرقام»، والرقم التالت بيظهر كمان على صور أعلى الرئيسية.</p>
                  {stats.map((s, i) => (
                    <fieldset key={i} className="stat-row">
                      <legend>الرقم {i + 1}</legend>
                      {(
                        [
                          ["value", "الرقم", "number"],
                          ["suffix", "قبله أو بعده", "text"],
                          ["label_ar", "الوصف (عربي)", "text"],
                          ["label_en", "الوصف (إنجليزي)", "text"],
                        ] as const
                      ).map(([k, label, type]) => {
                        const err = errors[`stats.${i}.${k}`];
                        return (
                          <div key={k} className="fld">
                            <label>
                              {label}
                              <input className="mt-1.5" type={type} value={String(s[k] ?? "")} dir={k === "label_ar" ? undefined : "ltr"} min={type === "number" ? 0 : undefined} maxLength={k === "suffix" ? 3 : 80} aria-invalid={!!err || undefined} onChange={(e) => setStat(i, k, e.target.value)} />
                            </label>
                            {err && <p className="ferr" role="alert">{err}</p>}
                          </div>
                        );
                      })}
                    </fieldset>
                  ))}
                </>
              ) : (
                t.fields.map(field)
              )}
            </div>
          ))}
        </div>
      </section>
    </form>
  );
}
