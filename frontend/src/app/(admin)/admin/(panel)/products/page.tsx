"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { BulkBar, Check, DPager, Filter, SearchBox, TableState } from "@/components/admin/ListParts";
import Switch from "@/components/admin/Switch";
import { EditIcon, PlusIcon } from "@/components/admin/icons";
import ProductCell from "@/components/admin/ProductCell";
import { PACK_COLORS } from "@/components/site/Packs";
import { api } from "@/lib/admin/api";
import { ago } from "@/lib/admin/format";
import type { Row } from "@/lib/admin/types";
import { useDebounced, useList } from "@/lib/admin/useList";

export type ProductRow = Row & {
  name_ar: string; name_en: string | null; image_url: string | null; packs_ar: string | null; updated_at: string;
  category: { id: number; name_ar: string; color: string } | null; type_name: string | null; factory_name: string | null;
};

const PER = 20;
const STATUS: [string, string][] = [["active", "منشور"], ["hidden", "مخفي"], ["missing_en", "ناقص ترجمة"]];

export default function ProductsPage() {
  const [search, setSearch] = useState("");
  const [category, setCategory] = useState("");
  const [factory, setFactory] = useState("");
  const [status, setStatus] = useState("");
  const [cats, setCats] = useState<[string, string][]>([]);
  const [factories, setFactories] = useState<[string, string][]>([]);
  const term = useDebounced(search);
  const list = useList<ProductRow>("products", { search: term, category, factory, status }, PER);

  useEffect(() => {
    const opts = (r: { data: { id: number; name_ar: string }[] }) => r.data.map((x): [string, string] => [String(x.id), x.name_ar]);
    api<{ data: { id: number; name_ar: string }[] }>("/product-categories").then((r) => setCats(opts(r)), () => {});
    api<{ data: { id: number; name_ar: string }[] }>("/factories").then((r) => setFactories(opts(r)), () => {});
  }, []);

  const total = list.res?.meta.total;

  return (
    <>
      <div className="dhead">
        <div>
          <h1>المنتجات</h1>
          <p>{total === undefined ? "كل منتجات الكتالوج." : `${total} منتج${status || category || factory || term ? " في النتيجة" : ""}.`}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <Link className="dbtn pri" href="/admin/products/edit/">
            <PlusIcon />
            منتج جديد
          </Link>
        </div>
      </div>
      <section className="tile">
        <div className="tool">
          <SearchBox label="ابحث باسم المنتج أو المادة الفعالة" value={search} onChange={setSearch} />
          <Filter label="كل الأقسام" value={category} options={cats} onChange={setCategory} />
          <Filter label="كل الحالات" value={status} options={STATUS} onChange={setStatus} />
          <Filter label="كل المصانع" value={factory} options={factories} onChange={setFactory} />
        </div>
        <BulkBar count={list.selected.length} onAction={list.bulk} />
        <div className="overflow-x-auto">
          <table className="ptable">
            <thead>
              <tr>
                <th className="w-8"><Check label="تحديد الكل" checked={list.allChecked} onChange={list.toggleAll} /></th>
                <th>المنتج</th>
                <th className="hide-s">القسم والنوع</th>
                <th className="hide-s">المصنع</th>
                <th className="hide-s">آخر تعديل</th>
                <th>منشور</th>
                <th><span className="sr-only">إجراءات</span></th>
              </tr>
            </thead>
            <tbody>
              <TableState items={list.items} error={list.error} cols={7} empty="مفيش منتجات بالشروط دي." />
              {list.items?.map((p) => {
                const color = p.category?.color ?? "field";
                return (
                  <tr key={p.id} style={{ "--c": PACK_COLORS[color] } as React.CSSProperties}>
                    <td><Check label={`تحديد ${p.name_ar}`} checked={list.selected.includes(p.id)} onChange={() => list.toggleRow(p.id)} /></td>
                    <td>
                      <ProductCell name={p.name_ar} nameEn={p.name_en} image={p.image_url} color={color} packs={p.packs_ar} label={p.type_name ?? ""} />
                    </td>
                    <td className="hide-s">{p.category && <span className="pill">{p.category.name_ar}{p.type_name ? ` · ${p.type_name}` : ""}</span>}</td>
                    <td className="hide-s">{p.factory_name ?? "—"}</td>
                    <td className="hide-s text-d-mute">{ago(p.updated_at)}</td>
                    <td><Switch on={p.is_active} onChange={(on) => list.toggle(p, on)} /></td>
                    <td>
                      <Link className="ebtn inline-flex items-center gap-1" href={`/admin/products/edit/?id=${p.id}`}>
                        <EditIcon className="w-4 h-4" />
                        تعديل
                      </Link>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
        <DPager meta={list.res?.meta} per={PER} onPage={list.setPage} />
      </section>
    </>
  );
}
