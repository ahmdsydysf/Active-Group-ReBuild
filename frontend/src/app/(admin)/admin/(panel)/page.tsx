"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import ContentGarden from "@/components/admin/ContentGarden";
import { PartnersIcon, SlidesIcon, StarIcon } from "@/components/admin/icons";
import { useShell } from "@/components/admin/Shell";
import CountUp from "@/components/shared/CountUp";
import { api } from "@/lib/admin/api";
import type { Overview } from "@/lib/admin/types";

const KPIS = [
  { key: "slides", label: "شرايح السلايدر", color: "var(--bloom)", line: "M0 46 L25 40 L50 44 L75 30 L100 34 L125 22 L150 26 L175 12 L200 16" },
  { key: "partners", label: "الشركاء", color: "var(--field)", line: "M0 40 L25 38 L50 36 L75 34 L100 30 L125 28 L150 24 L175 22 L200 18" },
  { key: "subscribers", label: "مشتركين القائمة البريدية", color: "var(--sky)", line: "M0 48 L25 44 L50 46 L75 36 L100 30 L125 32 L150 20 L175 18 L200 10" },
  { key: "testimonials", label: "آراء العملاء", color: "var(--sun)", line: "M0 30 L25 34 L50 26 L75 30 L100 22 L125 26 L150 20 L175 24 L200 18" },
] as const;

const ACTS = [
  { href: "/admin/slider/?new", label: "شريحة جديدة", Icon: SlidesIcon },
  { href: "/admin/partners/?new", label: "شريك جديد", Icon: PartnersIcon },
  { href: "/admin/testimonials/?new", label: "رأي جديد", Icon: StarIcon },
];

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
  const value = (key: (typeof KPIS)[number]["key"]) => (key === "subscribers" ? data?.subscribers.total : data?.sections.find((s) => s.key === key)?.total) ?? 0;
  const note = (key: (typeof KPIS)[number]["key"]) => {
    if (key === "subscribers") return `+${data?.subscribers.this_month ?? 0} الشهر ده`;
    const m = data?.sections.find((s) => s.key === key)?.missing_en ?? 0;
    return m ? `${m} ناقص ترجمة` : "مترجم بالكامل";
  };

  return (
    <>
      <div className={`sky${night ? " night" : ""}`}>
        <div className="orb" aria-hidden="true" />
        <h1>{`${night ? "مساء الخير" : "صباح الخير"} يا ${user.name.split(" ")[0]}`}</h1>
        <p>{!data ? " " : missing ? `عندك ${missing} عنصر محتاج ترجمة إنجليزي.` : "كل المحتوى مترجم عربي وإنجليزي."}</p>
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

      <div className="bento">
        {KPIS.map((k, i) => (
          <div key={k.key} className={`tile kpi${i === 3 ? " k4" : ""}`} style={{ "--c": k.color } as React.CSSProperties}>
            <span>{k.label}</span>
            {data ? <CountUp value={value(k.key)} /> : <b>–</b>}
            <small>{data ? note(k.key) : " "}</small>
            <svg viewBox="0 0 200 56" preserveAspectRatio="none" aria-hidden="true">
              <path className="l" d={k.line} fill="none" stroke={i === 3 ? "#123F1C" : "#fff"} strokeWidth="3" />
            </svg>
          </div>
        ))}
        {data && <ContentGarden sections={data.sections} />}
      </div>
    </>
  );
}
