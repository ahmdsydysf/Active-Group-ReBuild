import { href, type Locale } from "@/lib/i18n";
import type { Product } from "@/lib/types";
import Pack, { packShape } from "./Packs";
import { ForwardArrow } from "./icons";

/** Card colour class per category colour; pesticides (rust) use the base style. */
const CARD: Record<string, string> = { field: "c-fert", wheat: "c-reg", sky: "c-micro" };

type Card = Pick<Product, "slug" | "name" | "name_alt" | "category_name" | "type_name" | "color" | "pack" | "image">;

/** Label printed on the pack drawing: short names as they are, long ones cut to their first word. */
export const packLabel = (p: Pick<Product, "name" | "name_alt" | "type_name" | "color">, locale: Locale) => {
  if (p.color === "rust") return p.type_name;
  const latin = locale === "en" ? p.name : (p.name_alt ?? p.name);
  const text = latin.length <= 13 ? latin : p.name.split(/\s+/)[0];
  return text.length <= 13 ? text : text.slice(0, 12);
};

export function ProductArt({ p, locale, brand, style }: { p: Card; locale: Locale; brand: string; style?: React.CSSProperties }) {
  if (p.image)
    // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
    return <img src={p.image} alt="" loading="lazy" decoding="async" className="relative z-[1] h-[170px] w-auto object-contain" style={style} />;
  return <Pack shape={packShape(p.color, p.pack)} color={p.color} label={packLabel(p as Product, locale)} sub={brand} style={style} />;
}

export default function ProductCard({ p, locale, t, brand }: { p: Card; locale: Locale; t: { pack: string; details: string }; brand: string }) {
  return (
    <a className={`pcard ${CARD[p.color] ?? ""}`} href={href(locale, `products/${p.slug}/`)}>
      <div className="pimg">
        <ProductArt p={p} locale={locale} brand={brand} />
      </div>
      <div className="pbody">
        <span className="tagp">{`${p.category_name} · ${p.type_name}`}</span>
        <h3>
          <bdi>{p.name}</bdi>
        </h3>
        {p.name_alt && (
          <small className="text-soil-2" lang={locale === "ar" ? "en" : "ar"} dir={locale === "ar" ? "ltr" : "rtl"} style={{ textAlign: "end" }}>
            {p.name_alt}
          </small>
        )}
        <div className="meta">
          <span>{p.pack ? `${t.pack}: ${p.pack}` : ""}</span>
          <span className="inline-flex items-center gap-1 font-semibold text-field">
            {t.details} <ForwardArrow rtl={locale === "ar"} className="w-4 h-4" />
          </span>
        </div>
      </div>
    </a>
  );
}
