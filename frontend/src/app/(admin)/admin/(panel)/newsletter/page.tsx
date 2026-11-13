"use client";

import { useCallback, useEffect, useState } from "react";
import { DPager, Filter, SearchBox, TableState } from "@/components/admin/ListParts";
import { useShell } from "@/components/admin/Shell";
import { DownloadIcon, TrashIcon } from "@/components/admin/icons";
import { api, ApiError, download } from "@/lib/admin/api";
import { ago } from "@/lib/admin/format";
import { useDebounced, type Meta } from "@/lib/admin/useList";

type Subscriber = { id: number; email: string; locale: string; source: string | null; unsubscribed_at: string | null; created_at: string };
type Res = { data: Subscriber[]; meta: Meta; stats: { active: number; this_month: number; unsubscribed: number } };

const PER = 50;
const STATUS: [string, string][] = [["active", "مشترك"], ["unsubscribed", "ألغى الاشتراك"]];

export default function NewsletterPage() {
  const { toast } = useShell();
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("");
  const [page, setPage] = useState(1);
  const [res, setRes] = useState<Res | null>(null);
  const [error, setError] = useState<string | null>(null);
  const term = useDebounced(search);
  const query = new URLSearchParams({ search: term, status }).toString();

  const load = useCallback(() => {
    api<Res>(`/subscribers?${query}&page=${page}`).then((r) => (setRes(r), setError(null)), (e) => setError(e instanceof ApiError ? e.message : "حصلت مشكلة."));
  }, [query, page]);
  useEffect(load, [load]);

  const fail = (e: unknown) => toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
  const setSubscribed = (s: Subscriber, on: boolean) =>
    api(`/subscribers/${s.id}`, "PUT", { subscribed: on }).then(() => (toast(on ? "رجع مشترك" : "اتلغى اشتراكه"), load()), fail);
  const remove = (s: Subscriber) =>
    confirm(`هتحذف ${s.email} نهائيًا. متأكد؟`) && api(`/subscribers/${s.id}`, "DELETE").then(() => (toast("تم الحذف"), load()), fail);

  const stats = res?.stats;
  const kpis = [
    ["مشترك نشط", stats?.active, "var(--sky)", "كل اللي لسه مشتركين"],
    ["اشتراك جديد", stats?.this_month, "var(--field)", "الشهر ده"],
    ["ألغوا الاشتراك", stats?.unsubscribed, "var(--bloom)", "من الأول"],
  ] as const;

  return (
    <>
      <div className="dhead">
        <div>
          <h1>القائمة البريدية</h1>
          <p>الإيميلات اللي اشتركت من فورم الموقع.</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <button className="dbtn pri" type="button" onClick={() => download(`/subscribers/export?status=${status}`, "subscribers.csv").catch(fail)}>
            <DownloadIcon />
            تصدير CSV
          </button>
        </div>
      </div>
      <div className="bento !mt-0 mb-4 [&>.kpi]:col-span-12 sm:[&>.kpi]:col-span-4">
        {kpis.map(([label, n, c, sub]) => (
          <div key={label} className="tile kpi" style={{ "--c": c } as React.CSSProperties}>
            <span>{label}</span>
            <b>{n ?? "…"}</b>
            <small>{sub}</small>
          </div>
        ))}
      </div>
      <section className="tile">
        <div className="tool">
          <SearchBox label="ابحث بالإيميل" value={search} onChange={(v) => (setSearch(v), setPage(1))} />
          <Filter label="كل الحالات" value={status} options={STATUS} onChange={(v) => (setStatus(v), setPage(1))} />
        </div>
        <div className="overflow-x-auto">
          <table className="ptable">
            <thead>
              <tr>
                <th>البريد الإلكتروني</th>
                <th className="hide-s">تاريخ الاشتراك</th>
                <th className="hide-s">اللغة</th>
                <th>الحالة</th>
                <th><span className="sr-only">إجراءات</span></th>
              </tr>
            </thead>
            <tbody>
              <TableState items={res?.data ?? null} error={error} cols={5} empty="مفيش مشتركين بالشروط دي." />
              {res?.data.map((s) => (
                <tr key={s.id}>
                  <td dir="ltr" className="text-end">{s.email}</td>
                  <td className="hide-s text-d-mute">{ago(s.created_at)}</td>
                  <td className="hide-s">{s.locale === "en" ? "English" : "العربية"}</td>
                  <td>
                    {s.unsubscribed_at ? <span className="stat" style={{ "--c": "var(--d-mute)" } as React.CSSProperties}>ألغى الاشتراك</span> : <span className="stat" style={{ "--c": "var(--field)" } as React.CSSProperties}>مشترك</span>}
                  </td>
                  <td>
                    <div className="flex gap-1.5 justify-end">
                      <button className="ebtn" type="button" onClick={() => setSubscribed(s, !!s.unsubscribed_at)}>{s.unsubscribed_at ? "رجّعه" : "إلغاء الاشتراك"}</button>
                      <button className="ebtn !px-2 text-rust" type="button" aria-label={`حذف ${s.email}`} onClick={() => remove(s)}>
                        <TrashIcon className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <DPager meta={res?.meta} per={PER} onPage={setPage} />
      </section>
    </>
  );
}
