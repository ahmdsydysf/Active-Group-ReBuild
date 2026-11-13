"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import ContentManager from "./ContentManager";
import { BulkBar, Check, DPager, Filter, SearchBox, TableState } from "./ListParts";
import { SimpleCard } from "./cards";
import { EditIcon, PlusIcon, TrashIcon } from "./icons";
import { api } from "@/lib/admin/api";
import { fullDate } from "@/lib/admin/format";
import type { Row } from "@/lib/admin/types";
import { useDebounced, useList } from "@/lib/admin/useList";

type Post = Row & { title_ar: string; image_url: string | null; status: "draft" | "scheduled" | "published"; category_name: string | null; published_at: string | null };
type Category = Row & { name_ar: string; slug: string };

export const STATUS: Record<Post["status"], [string, string]> = {
  published: ["منشور", "var(--field)"],
  scheduled: ["مجدول", "var(--sky)"],
  draft: ["مسودة", "var(--d-mute)"],
};
const PER = 15;

const COPY = {
  advice: { title: "النصائح الفنية", intro: "مقالات الاستشارات الفنية في صفحة «نصائح وخبرات».", add: "نصيحة جديدة", noun: "نصيحة" },
  blog: { title: "المدونة", intro: "أخبار الشركة والمعارض والمقالات.", add: "مقال جديد", noun: "مقال" },
};

/** Articles of one site section (advice or blog), with their categories under the table. */
export default function PostsList({ section }: { section: "advice" | "blog" }) {
  const t = COPY[section];
  const [search, setSearch] = useState("");
  const [category, setCategory] = useState("");
  const [status, setStatus] = useState("");
  const [cats, setCats] = useState<[string, string][]>([]);
  const [version, setVersion] = useState(0);
  const list = useList<Post>("posts", { section, search: useDebounced(search), category, status }, PER);
  const counts = list.res?.meta.total;

  useEffect(() => {
    api<{ data: Category[] }>(`/post-categories?section=${section}`).then((r) => setCats(r.data.map((c) => [String(c.id), c.name_ar])), () => {});
  }, [section, version]);

  return (
    <>
      <div className="dhead">
        <div>
          <h1>{t.title}</h1>
          <p>{counts === undefined ? t.intro : `${counts} ${t.noun}. ${t.intro}`}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <Link className="dbtn pri" href={`/admin/posts/edit/?section=${section}`}>
            <PlusIcon />
            {t.add}
          </Link>
        </div>
      </div>
      <section className="tile">
        <div className="tool">
          <SearchBox label="ابحث بالعنوان" value={search} onChange={setSearch} />
          <Filter label="كل التصنيفات" value={category} options={cats} onChange={setCategory} />
          <Filter label="كل الحالات" value={status} options={Object.entries(STATUS).map(([k, [l]]) => [k, l])} onChange={setStatus} />
        </div>
        <BulkBar count={list.selected.length} onAction={list.bulk} />
        <div className="overflow-x-auto">
          <table className="ptable">
            <thead>
              <tr>
                <th className="w-8"><Check label="تحديد الكل" checked={list.allChecked} onChange={list.toggleAll} /></th>
                <th>العنوان</th>
                <th>الحالة</th>
                <th className="hide-s">تاريخ النشر</th>
                <th><span className="sr-only">إجراءات</span></th>
              </tr>
            </thead>
            <tbody>
              <TableState items={list.items} error={list.error} cols={5} empty="مفيش مقالات بالشروط دي." />
              {list.items?.map((p) => (
                <tr key={p.id}>
                  <td><Check label={`تحديد ${p.title_ar}`} checked={list.selected.includes(p.id)} onChange={() => list.toggleRow(p.id)} /></td>
                  <td>
                    <div className="nm">
                      {/* eslint-disable-next-line @next/next/no-img-element -- API image */}
                      {p.image_url ? <img className="thumb" src={p.image_url} alt="" loading="lazy" /> : <span className="thumb bg-d-line" />}
                      <div>
                        <b className="!whitespace-normal">{p.title_ar}</b>
                        <small className={p.missing_en ? "warn" : "text-d-mute"}>{[p.category_name, p.missing_en && "ناقص ترجمة"].filter(Boolean).join(" · ") || "بدون تصنيف"}</small>
                      </div>
                    </div>
                  </td>
                  <td><span className="stat" style={{ "--c": STATUS[p.status][1] } as React.CSSProperties}>{STATUS[p.status][0]}</span></td>
                  <td className="hide-s text-d-mute">{fullDate(p.published_at)}</td>
                  <td>
                    <div className="flex gap-1.5 justify-end">
                      <Link className="ebtn inline-flex items-center gap-1" href={`/admin/posts/edit/?id=${p.id}`}>
                        <EditIcon className="w-4 h-4" />
                        تعديل
                      </Link>
                      <button className="ebtn !px-2 text-rust" type="button" aria-label={`حذف ${p.title_ar}`} onClick={() => list.remove(p)}>
                        <TrashIcon className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <DPager meta={list.res?.meta} per={PER} onPage={list.setPage} />
      </section>

      <ContentManager<Category>
        sub
        resource="post-categories"
        query={`section=${section}`}
        title="التصنيفات"
        intro="أزرار التصفية فوق المقالات في الموقع."
        noun="التصنيف"
        addLabel="تصنيف جديد"
        listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
        defaults={{ section }}
        onChange={() => setVersion((n) => n + 1)}
        fields={[{ name: "name", label: "اسم التصنيف", labelEn: "Name", required: true, max: 120 }]}
        shared={[{ name: "slug", label: "الرابط", ltr: true, max: 120, hint: "حروف إنجليزي صغيرة وشرطة. سيبه فاضي ويتعمل لوحده." }]}
        card={(c, ctl) => <SimpleCard key={c.id} id={c.id} c={{ ...ctl, toggle: null }} title={c.name_ar} sub={c.slug} missingEn={c.missing_en} />}
      />
    </>
  );
}
