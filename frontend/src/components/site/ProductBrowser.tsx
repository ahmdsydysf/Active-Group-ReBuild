"use client";

import { useMemo, useState, useSyncExternalStore } from "react";
import { href, type Locale } from "@/lib/i18n";
import type { Category, Product } from "@/lib/types";
import type { Messages } from "@/messages/ar";
import Pager, { scrollToList } from "./Pager";
import ProductCard from "./ProductCard";
import { SearchIcon, WhatsAppIcon } from "./icons";

const PER_PAGE = 12;

type Card = Pick<Product, "id" | "slug" | "name" | "name_alt" | "active_ingredient" | "category" | "type" | "type_name" | "category_name" | "color" | "pack" | "crops" | "image">;

// The page is static; the starting category comes from `?c=` (links from the home page and footer).
const subscribe = (cb: () => void) => (addEventListener("popstate", cb), () => removeEventListener("popstate", cb));
const urlCategory = () => new URLSearchParams(location.search).get("c");

/** Arabic search ignores hamza forms, taa marbuta and diacritics, so "اكتي" finds "أكتي". */
const fold = (s: string) =>
  s.toLowerCase().normalize("NFKD").replace(/[ً-ٰٟ]/g, "").replace(/[أإآ]/g, "ا").replace(/ة/g, "ه").replace(/ى/g, "ي");

/** Products grid with category chips, search, type and crop filters, and paging. Everything runs in the browser. */
export default function ProductBrowser({ locale, products, categories, t, c, brand }: { locale: Locale; products: Card[]; categories: Category[]; t: Messages["products"]; c: Pick<Messages["common"], "all" | "pages" | "prev" | "next">; brand: string }) {
  const fromUrl = useSyncExternalStore(subscribe, urlCategory, () => null);
  const [picked, setPicked] = useState<string | null>(null);
  const [query, setQuery] = useState("");
  const [types, setTypes] = useState<string[]>([]);
  const [crops, setCrops] = useState<string[]>([]);
  const [page, setPage] = useState(1);
  const category = picked ?? (categories.some((x) => x.slug === fromUrl) ? fromUrl! : "all");

  const typeOptions = useMemo(() => [...new Map(products.map((p) => [p.type, p.type_name])).entries()], [products]);
  const cropOptions = useMemo(() => [...new Set(products.flatMap((p) => p.crops))].slice(0, 12), [products]);
  const index = useMemo(() => products.map((p) => fold([p.name, p.name_alt, p.active_ingredient].filter(Boolean).join(" "))), [products]);

  const q = fold(query.trim());
  const shown = products.filter(
    (p, i) =>
      (category === "all" || p.category === category) &&
      (!types.length || types.includes(p.type)) &&
      (!crops.length || p.crops.some((x) => crops.includes(x))) &&
      (!q || index[i].includes(q)),
  );
  const pages = Math.max(1, Math.ceil(shown.length / PER_PAGE));
  const current = Math.min(page, pages);
  const visible = shown.slice((current - 1) * PER_PAGE, current * PER_PAGE);

  // Every filter change starts again from page one.
  const reset = <T,>(set: (v: T) => void) => (v: T) => (set(v), setPage(1));
  const toggle = (list: string[], v: string) => (list.includes(v) ? list.filter((x) => x !== v) : [...list, v]);
  const go = (n: number) => {
    setPage(n);
    scrollToList("pgrid");
  };

  const chips = [{ slug: "all", name: c.all, color: "deep", count: products.length }, ...categories.map((x) => ({ slug: x.slug, name: x.name, color: x.color, count: 0 }))];

  return (
    <section className="py-14">
      <div className="wrap">
        <div className="flex flex-col lg:flex-row gap-4 lg:items-center justify-between mb-8">
          <div className="flex gap-2 overflow-x-auto pb-1" role="group" aria-label={t.byCategory}>
            {chips.map((x) => (
              <button key={x.slug} className="fchip" type="button" aria-pressed={category === x.slug} style={{ "--c": `var(--${x.color})` } as React.CSSProperties} onClick={() => reset(setPicked)(x.slug)}>
                <i />
                {x.name}
                {x.slug === "all" && <small>{x.count}</small>}
              </button>
            ))}
          </div>
          <form className="search-pill lg:w-[360px]" role="search" onSubmit={(e) => e.preventDefault()}>
            <label className="contents">
              <span className="sr-only">{t.search}</span>
              <SearchIcon className="w-[18px] flex-none" />
              <input type="search" placeholder={t.searchPlaceholder} value={query} onChange={(e) => reset(setQuery)(e.target.value)} />
            </label>
            <button className="btn btn-sun !py-2 !px-4 text-sm" type="submit">{t.searchButton}</button>
          </form>
        </div>
        <div className="grid lg:grid-cols-[260px_1fr] gap-8">
          <aside className="space-y-6" aria-label={t.advanced}>
            {[
              { title: t.type, options: typeOptions, value: types, set: setTypes },
              { title: t.crop, options: cropOptions.map((x) => [x, x] as [string, string]), value: crops, set: setCrops },
            ].map((g) =>
              g.options.length > 1 ? (
                <fieldset key={g.title} className="rounded-3xl border border-line p-5">
                  <legend className="sr-only">{g.title}</legend>
                  <h2 className="text-lg font-bold mb-2 text-deep" aria-hidden="true">{g.title}</h2>
                  {g.options.map(([value, label]) => (
                    <label key={value} className="flex items-center gap-3 py-1.5 cursor-pointer">
                      <input type="checkbox" className="w-[18px] h-[18px] accent-field" checked={g.value.includes(value)} onChange={() => reset(g.set)(toggle(g.value, value))} />
                      <span>{label}</span>
                    </label>
                  ))}
                </fieldset>
              ) : null,
            )}
            <div className="rounded-3xl bg-sun p-5 text-deep">
              <h2 className="text-lg font-bold">{t.helpTitle}</h2>
              <p className="text-sm mt-1">{t.helpText}</p>
              <a className="btn bg-deep text-white !py-2.5 mt-4 text-sm" href={href(locale, "contact/#quote")}>
                <WhatsAppIcon />
                {t.helpButton}
              </a>
            </div>
          </aside>
          <div id="pgrid" className="scroll-mt-28">
            <p className="text-soil-2 mb-4" aria-live="polite">{t.shown.replace("{n}", String(shown.length)).replace("{total}", String(products.length))}</p>
            {visible.length ? (
              <div className="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                {visible.map((p) => (
                  <ProductCard key={p.id} p={p} locale={locale} t={t} brand={brand} />
                ))}
              </div>
            ) : (
              <p className="rounded-3xl bg-sage p-8 text-center text-deep font-semibold">{t.none}</p>
            )}
            <Pager pages={pages} current={current} go={go} t={c} rtl={locale === "ar"} />
          </div>
        </div>
      </div>
    </section>
  );
}
