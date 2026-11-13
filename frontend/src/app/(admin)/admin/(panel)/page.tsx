"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import ContentGarden from "@/components/admin/ContentGarden";
import { BookIcon, BoxIcon, EditIcon, ImageIcon } from "@/components/admin/icons";
import { useShell } from "@/components/admin/Shell";
import Pack, { PACK_COLORS, packShape } from "@/components/site/Packs";
import CountUp from "@/components/shared/CountUp";
import { api } from "@/lib/admin/api";
import { ago } from "@/lib/admin/format";
import { initial, messageType } from "@/lib/admin/messages";
import type { Overview } from "@/lib/admin/types";

const ACTS = [
  { href: "/admin/products/edit/", label: "منتج جديد", Icon: BoxIcon },
  { href: "/admin/posts/edit/?section=advice", label: "نصيحة فنية", Icon: BookIcon },
  { href: "/admin/gallery/?new", label: "صور للمعرض", Icon: ImageIcon },
];
const LINES = [
  "M0 46 L25 40 L50 44 L75 30 L100 34 L125 22 L150 26 L175 12 L200 16",
  "M0 40 L25 38 L50 36 L75 34 L100 30 L125 28 L150 24 L175 22 L200 18",
  "M0 48 L25 44 L50 46 L75 36 L100 30 L125 32 L150 20 L175 18 L200 10",
  "M0 30 L25 34 L50 26 L75 30 L100 22 L125 26 L150 20 L175 24 L200 18",
];
const MONTH = new Intl.DateTimeFormat("ar-EG", { month: "long" });

function kpis(k: Overview["kpis"]) {
  return [
    { label: "رسائل جديدة", value: k.messages_new, note: k.quotes_new ? `${k.quotes_new} منها طلب عرض سعر` : "مفيش طلبات أسعار جديدة", color: "var(--bloom)" },
    { label: "منتجات منشورة", value: k.products, note: `${k.products_this_month} اتضافوا الشهر ده`, color: "var(--field)" },
    { label: "مشتركين القائمة البريدية", value: k.subscribers, note: `+${k.subscribers_this_month} الشهر ده`, color: "var(--sky)" },
    { label: "مقالات ونصائح", value: k.posts, note: k.last_post_at ? `آخر نشر ${ago(k.last_post_at)}` : "لسه مفيش مقالات", color: "var(--sun)" },
  ];
}

/** Messages per month, newest on the left like the rest of the RTL dashboard. */
function MessagesChart({ values }: { values: number[] }) {
  const top = Math.max(4, Math.ceil(Math.max(...values) / 4) * 4);
  const x = (i: number) => 700 - (i * 680) / (values.length - 1);
  const y = (v: number) => 180 - (v / top) * 160;
  const line = values.map((v, i) => `${i ? "L" : "M"}${x(i).toFixed(1)} ${y(v).toFixed(1)}`).join(" ");
  const last = values.length - 1;
  const start = new Date();
  start.setDate(1);
  start.setMonth(start.getMonth() - last);
  const month = (i: number) => MONTH.format(new Date(start.getFullYear(), start.getMonth() + i, 1));
  return (
    <svg viewBox="0 0 720 220" role="img" aria-label={`الرسائل الواردة شهريًا، الشهر ده ${values[last]}`}>
      <defs>
        <linearGradient id="ag" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0" stopColor="#E0607E" stopOpacity=".35" />
          <stop offset="1" stopColor="#E0607E" stopOpacity="0" />
        </linearGradient>
      </defs>
      {[1, 2, 3, 4].map((n) => (
        <g key={n}>
          <line x1="20" x2="700" y1={y((top * n) / 4)} y2={y((top * n) / 4)} stroke="var(--d-line)" strokeDasharray="3 5" />
          <text x="718" y={y((top * n) / 4) + 4} textAnchor="end">{(top * n) / 4}</text>
        </g>
      ))}
      <path className="area" d={`${line} L${x(last)} 180 L700 180Z`} fill="url(#ag)" />
      <path className="ln" d={line} fill="none" stroke="#E0607E" strokeWidth="3" strokeLinejoin="round" />
      <circle cx={x(last)} cy={y(values[last])} r="6" fill="#E0607E" stroke="var(--d-panel)" strokeWidth="3" />
      <text x={x(last)} y={y(values[last]) - 14} textAnchor="middle" fontWeight="700" style={{ fill: "var(--d-ink)" }}>{values[last]}</text>
      {values.map((_, i) => (i % 2 === 0 ? <text key={i} x={x(i)} y="204" textAnchor="middle">{month(i)}</text> : null))}
    </svg>
  );
}

function CategoryDonut({ data }: { data: Overview["products_by_category"] }) {
  const total = data.reduce((n, c) => n + c.count, 0);
  const C = 2 * Math.PI * 70;
  const segments = data.reduce<{ c: (typeof data)[number]; len: number; offset: number }[]>((acc, c) => {
    const offset = acc.length ? acc[acc.length - 1].offset + acc[acc.length - 1].len : 0;
    return [...acc, { c, len: total ? (c.count / total) * C : 0, offset }];
  }, []);
  return (
    <>
      <svg viewBox="0 0 200 200" width="100%" style={{ maxHeight: 200 }} role="img" aria-label={`${total} منتج: ${data.map((c) => `${c.name} ${c.count}`).join("، ")}`}>
        <circle cx="100" cy="100" r="70" fill="none" stroke="var(--d-line)" strokeWidth="26" />
        {segments.map(({ c, len, offset }) => (
          <circle key={c.name} cx="100" cy="100" r="70" fill="none" stroke={PACK_COLORS[c.color] ?? PACK_COLORS.field} strokeWidth="26" strokeDasharray={`${len} ${C}`} strokeDashoffset={-offset} transform="rotate(-90 100 100)" />
        ))}
        <text x="100" y="98" textAnchor="middle" fontFamily="var(--display)" fontWeight="900" fontSize="34" fill="var(--d-ink)">{total}</text>
        <text x="100" y="122" textAnchor="middle" fontSize="13" fill="var(--d-mute)">منتج</text>
      </svg>
      <div className="legend justify-center">
        {data.map((c) => <span key={c.name}><i style={{ background: PACK_COLORS[c.color] ?? PACK_COLORS.field }} />{c.name} {c.count}</span>)}
      </div>
    </>
  );
}

export default function OverviewPage() {
  const { user } = useShell();
  const [data, setData] = useState<Overview | null>(null);
  // The shell renders pages only in the browser, so the local hour is safe to read here.
  const [night] = useState(() => {
    const h = new Date().getHours();
    return h >= 18 || h < 5;
  });

  useEffect(() => {
    api<Overview>("/overview").then(setData, () => {});
  }, []);

  const missing = data?.sections.reduce((n, s) => n + s.missing_en, 0) ?? 0;
  const fresh = data?.kpis.messages_new ?? 0;
  const summary = !data
    ? " "
    : [fresh && `عندك ${fresh} رسالة جديدة`, missing && `${missing} عنصر محتاج ترجمة إنجليزي`].filter(Boolean).join("، و") || "مفيش رسائل جديدة، وكل المحتوى مترجم.";

  return (
    <>
      <div className={`sky${night ? " night" : ""}`}>
        <div className="orb" aria-hidden="true" />
        <h1>{`${night ? "مساء الخير" : "صباح الخير"} يا ${user.name.split(" ")[0]}`}</h1>
        <p>{summary}{data && (fresh || missing) ? "." : ""}</p>
        <div className="acts">
          {ACTS.map(({ href, label, Icon }) => (
            <Link key={href} href={href} className="inline-flex gap-2 items-center rounded-xl px-4 py-2.5 font-display font-bold bg-white/90 text-deep">
              <Icon className="w-[18px] h-[18px]" />
              {label}
            </Link>
          ))}
        </div>
        <svg className="hills" viewBox="0 0 1000 70" preserveAspectRatio="none" aria-hidden="true">
          <path d="M0 50 Q 150 10 320 40 T 650 35 T 1000 30 V70 H0Z" fill="#4CAF2E" opacity=".55" />
          <path d="M0 60 Q 200 30 420 55 T 800 50 T 1000 45 V70 H0Z" fill="#1F6B2E" />
        </svg>
      </div>

      {!data ? (
        <p className="empty" aria-busy="true">بنحمّل…</p>
      ) : (
        <div className="bento">
          {kpis(data.kpis).map((k, i) => (
            <div key={k.label} className={`tile kpi${i === 3 ? " k4" : ""}`} style={{ "--c": k.color } as React.CSSProperties}>
              <span>{k.label}</span>
              <CountUp value={k.value} />
              <small>{k.note}</small>
              <svg viewBox="0 0 200 56" preserveAspectRatio="none" aria-hidden="true">
                <path className="l" d={LINES[i]} fill="none" stroke={i === 3 ? "#123F1C" : "#fff"} strokeWidth="3" />
              </svg>
            </div>
          ))}

          <ContentGarden sections={data.sections} />

          <section className="tile span5 inbox">
            <h3>أحدث الرسائل <Link href="/admin/messages/">كل الرسائل</Link></h3>
            {data.latest_messages.length ? (
              data.latest_messages.map((m) => (
                <Link key={m.id} className="m" href="/admin/messages/" style={{ "--c": messageType(m.type)[1] } as React.CSSProperties}>
                  <i aria-hidden="true">{initial(m.name)}</i>
                  <div className="min-w-0">
                    <b>{m.name}{m.status === "new" && <span className="new" aria-label="جديدة" />}</b>
                    <p><span className="pill">{messageType(m.type)[0]}</span>{m.body}</p>
                  </div>
                  <small>{ago(m.created_at)}</small>
                </Link>
              ))
            ) : (
              <p className="empty">لسه مفيش رسائل.</p>
            )}
          </section>

          <section className="tile span8 chart">
            <h3>الرسائل الواردة آخر 12 شهر</h3>
            <MessagesChart values={data.messages_by_month} />
          </section>

          <section className="tile span4">
            <h3>المنتجات حسب القسم</h3>
            <CategoryDonut data={data.products_by_category} />
          </section>

          <section className="tile span12">
            <h3>آخر المنتجات المعدلة <Link href="/admin/products/">كل المنتجات</Link></h3>
            <div className="overflow-x-auto">
              <table className="ptable">
                <thead>
                  <tr>
                    <th>المنتج</th>
                    <th className="hide-s">القسم والنوع</th>
                    <th className="hide-s">المصنع</th>
                    <th>الحالة</th>
                    <th><span className="sr-only">إجراءات</span></th>
                  </tr>
                </thead>
                <tbody>
                  {data.latest_products.map((p) => {
                    const color = p.color ?? "field";
                    return (
                      <tr key={p.id} style={{ "--c": PACK_COLORS[color] } as React.CSSProperties}>
                        <td>
                          <div className="nm">
                            <span className="t">
                              {p.image_url ? (
                                // eslint-disable-next-line @next/next/no-img-element -- API image
                                <img src={p.image_url} alt="" className="h-[34px] w-auto object-contain" />
                              ) : (
                                <Pack shape={packShape(color, p.packs_ar)} color={color} label={p.type ?? ""} style={{ height: 34, width: "auto" }} />
                              )}
                            </span>
                            <div>
                              <b><bdi>{p.name_ar}</bdi></b>
                              {p.name_en ? <small><bdi>{p.name_en}</bdi></small> : <small className="warn">ينقصه الاسم الإنجليزي</small>}
                            </div>
                          </div>
                        </td>
                        <td className="hide-s">{p.category && <span className="pill">{p.category}{p.type ? ` · ${p.type}` : ""}</span>}</td>
                        <td className="hide-s">{p.factory ?? "—"}</td>
                        <td><span className="stat" style={{ "--c": p.is_active ? "var(--field)" : "var(--d-mute)" } as React.CSSProperties}>{p.is_active ? "منشور" : "مخفي"}</span></td>
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
          </section>
        </div>
      )}
    </>
  );
}
