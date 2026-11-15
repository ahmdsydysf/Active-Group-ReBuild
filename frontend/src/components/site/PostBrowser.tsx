"use client";

import { useState } from "react";
import type { Locale } from "@/lib/i18n";
import type { Post } from "@/lib/types";
import type { Messages } from "@/messages/ar";
import Pager, { scrollToList } from "./Pager";
import PostCard from "./PostCard";

const PER_PAGE = 9;

/**
 * Article list filtered in the browser. "chips" puts the categories above the grid (advice);
 * "sidebar" lists them beside it with counts (blog).
 */
export default function PostBrowser({
  locale,
  posts,
  categories,
  variant,
  t,
  heading,
}: {
  locale: Locale;
  posts: (Post & { read: string })[];
  categories: { slug: string; name: string }[];
  variant: "chips" | "sidebar";
  t: Pick<Messages["common"], "all" | "empty" | "pages" | "prev" | "next">;
  heading: string;
}) {
  const [category, setCategory] = useState("all");
  const [page, setPage] = useState(1);
  const shown = category === "all" ? posts : posts.filter((p) => p.category === category);
  const pages = Math.max(1, Math.ceil(shown.length / PER_PAGE));
  const current = Math.min(page, pages);
  const visible = shown.slice((current - 1) * PER_PAGE, current * PER_PAGE);
  const pick = (slug: string) => (setCategory(slug), setPage(1));
  const go = (n: number) => (setPage(n), scrollToList("plist"));

  const grid = (
    <div id="plist" className="scroll-mt-28">
      <div className={`grid sm:grid-cols-2 ${variant === "chips" ? "lg:grid-cols-3" : ""} gap-6`}>
        {visible.map((p, i) => (
          <PostCard key={p.id} post={p} locale={locale} i={i} read={p.read} />
        ))}
      </div>
      {!visible.length && <p className="rounded-3xl bg-sage p-8 text-center text-deep font-semibold">{t.empty}</p>}
      <Pager pages={pages} current={current} go={go} t={t} rtl={locale === "ar"} />
    </div>
  );

  if (variant === "chips")
    return (
      <>
        {categories.length > 1 && (
          <div className="flex gap-2 overflow-x-auto pb-1 mb-8" role="group" aria-label={heading}>
            {[{ slug: "all", name: t.all }, ...categories].map((c) => (
              <button key={c.slug} className="fchip" type="button" aria-pressed={category === c.slug} style={{ "--c": "var(--field)" } as React.CSSProperties} onClick={() => pick(c.slug)}>
                {c.name}
              </button>
            ))}
          </div>
        )}
        {grid}
      </>
    );

  return (
    <div className="grid lg:grid-cols-[1fr_300px] gap-10">
      {grid}
      {categories.length > 0 && (
        <aside className="space-y-6">
          <div className="rounded-3xl border border-line p-5">
            <h2 className="text-lg font-bold text-deep mb-3">{heading}</h2>
            <ul className="space-y-2">
              {[{ slug: "all", name: t.all }, ...categories].map((c) => (
                <li key={c.slug}>
                  <button type="button" aria-pressed={category === c.slug} className={`flex w-full justify-between hover:text-field ${category === c.slug ? "text-field font-bold" : ""}`} onClick={() => pick(c.slug)}>
                    <span>{c.name}</span>
                    <span className="text-soil-2">{c.slug === "all" ? posts.length : posts.filter((p) => p.category === c.slug).length}</span>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        </aside>
      )}
    </div>
  );
}
