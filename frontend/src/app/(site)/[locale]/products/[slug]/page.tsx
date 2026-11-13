import { notFound } from "next/navigation";
import PageHero from "@/components/site/PageHero";
import ProductCard, { ProductArt } from "@/components/site/ProductCard";
import { DownloadIcon, ShieldIcon, WhatsAppIcon } from "@/components/site/icons";
import { getCatalog } from "@/lib/api";
import { href, locales, messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta, staticParams } from "@/lib/page";

const BADGE: Record<string, string> = { rust: "bg-rust", field: "bg-field", wheat: "bg-wheat", sky: "bg-sky", plum: "bg-[#7A2140]", deep: "bg-deep", bloom: "bg-bloom" };

export const dynamicParams = false;

export async function generateStaticParams() {
  const lists = await Promise.all(locales.map(async (locale) => (await getCatalog(locale)).products.map((p) => ({ locale, slug: p.slug }))));
  return staticParams(lists.flat(), locales[0]);
}

const find = async (locale: Awaited<ReturnType<typeof pageLocale>>, slug: string) => (await getCatalog(locale)).products.find((p) => p.slug === decodeURIComponent(slug));

export async function generateMetadata({ params }: PageProps<"/[locale]/products/[slug]">) {
  const locale = await pageLocale(params);
  const p = await find(locale, (await params).slug);
  if (!p) return {};
  return pageMeta(locale, `products/${p.slug}/`, p.seo.title ?? p.name, p.seo.description, p.image_lg);
}

export default async function ProductPage({ params }: PageProps<"/[locale]/products/[slug]">) {
  const locale = await pageLocale(params);
  const { slug } = await params;
  const m = messages(locale);
  const t = m.products;
  const catalog = await getCatalog(locale);
  const p = catalog.products.find((x) => x.slug === decodeURIComponent(slug));
  if (!p) notFound();
  const rtl = locale === "ar";
  // Same category first, then the rest, as in the design's "products you may need".
  const related = [...catalog.products.filter((x) => x.id !== p.id && x.category === p.category), ...catalog.products.filter((x) => x.category !== p.category)].slice(0, 4);
  const facts = [
    [t.ingredient, p.active_ingredient],
    [t.formulation, p.formulation],
    [t.packs, p.packs],
    [t.registration, p.registration_no],
  ].filter((f): f is [string, string] => Boolean(f[1]));
  const doseCols = (["crop", "pest", "rate", "interval", "phi"] as const).map((k, i) => [k, t.doseCols[i]] as const).filter(([k]) => p.doses.some((d) => d[k]));

  return (
    <main id="main">
      <PageHero
        color="deep"
        flowers
        wrapClass="!pb-28"
        crumbsLabel={m.common.crumbs}
        crumbs={[home(locale), { label: t.title, href: href(locale, "products/") }, { label: p.category_name, href: href(locale, `products/?c=${p.category}`) }, { label: p.name }]}
      >
        <div className="grid lg:grid-cols-[1fr_1.1fr] gap-10 items-center">
          <div className="relative grid place-items-center bg-white/90 rounded-[36px] p-10 min-h-[380px]">
            <ProductArt p={{ ...p, image: p.image_lg }} locale={locale} brand={m.families.packs.fertilizer} style={{ height: 300 }} />
            <span className="absolute top-5 start-5 stat" style={{ "--c": p.in_stock ? "var(--field)" : "var(--rust)" } as React.CSSProperties}>
              {p.in_stock ? t.inStock : t.outOfStock}
            </span>
          </div>
          <div>
            <span className={`tagp inline-block text-sm font-bold rounded-lg px-3 py-0.5 text-white ${BADGE[p.color] ?? "bg-field"}`}>{`${p.category_name} · ${p.type_name}`}</span>
            <h1 className="!text-[clamp(34px,4vw,54px)] mt-3 text-white">{p.name}</h1>
            {p.name_alt && (
              <p className="font-display text-lg text-[#B9CDB2]" lang={rtl ? "en" : "ar"} dir={rtl ? "ltr" : "rtl"} style={{ textAlign: "end" }}>
                {p.name_alt}
              </p>
            )}
            {p.summary && <p className="!text-lg mt-4 !opacity-100">{p.summary}</p>}
            {facts.length > 0 && (
              <dl className="grid grid-cols-2 gap-3 mt-6">
                {facts.map(([label, value]) => (
                  <div key={label} className="bg-white text-soil rounded-2xl p-4">
                    <dt className="text-sm text-soil-2">{label}</dt>
                    <dd className="font-display font-bold" dir={label === t.registration ? "ltr" : undefined} style={label === t.registration ? { textAlign: "end" } : undefined}>
                      {value}
                    </dd>
                  </div>
                ))}
              </dl>
            )}
            <div className="flex flex-wrap gap-3 mt-7">
              <a className="btn btn-sun" href={href(locale, "contact/#quote")}>
                <WhatsAppIcon />
                {t.quote}
              </a>
              {p.leaflet && (
                <a className="btn bg-white text-deep" href={p.leaflet} target="_blank" rel="noopener" download>
                  <DownloadIcon />
                  {t.leaflet}
                </a>
              )}
            </div>
          </div>
        </div>
      </PageHero>

      <section className="band !pt-16">
        <div className="wrap grid lg:grid-cols-[1.4fr_1fr] gap-10">
          <div>
            {p.body && <div className="prose-ag mb-10" dangerouslySetInnerHTML={{ __html: p.body }} />}
            {p.doses.length > 0 && (
              <>
                <h2 className="text-3xl font-extrabold text-deep mb-5">{t.doses}</h2>
                <div className="overflow-x-auto">
                  <table className="dose">
                    <thead>
                      <tr>{doseCols.map(([k, label]) => <th key={k}>{label}</th>)}</tr>
                    </thead>
                    <tbody>
                      {p.doses.map((d, i) => (
                        <tr key={i}>{doseCols.map(([k]) => <td key={k}>{d[k]}</td>)}</tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </>
            )}
            {p.faqs.length > 0 && (
              <div className="acc mt-10">
                <h2 className="text-3xl font-extrabold text-deep mb-5">{t.faqs}</h2>
                {p.faqs.map((f, i) => (
                  <details key={i} open={i === 0}>
                    <summary>{f.q}</summary>
                    <p>{f.a}</p>
                  </details>
                ))}
              </div>
            )}
          </div>
          <aside className="space-y-5">
            {p.safety.length > 0 && (
              <div className="rounded-3xl bg-[#FFF3D6] p-6">
                <h3 className="text-xl text-deep mb-3 flex items-center gap-2">
                  <ShieldIcon className="w-6 h-6 text-wheat" />
                  {t.safety}
                </h3>
                <ul className="space-y-2 text-[15.5px]">
                  {p.safety.map((s) => <li key={s}>{`• ${s}`}</li>)}
                </ul>
              </div>
            )}
            <div className="rounded-3xl bg-deep text-white p-6">
              <h3 className="text-xl mb-2">{t.programTitle}</h3>
              <p className="text-[#CFE3C8]">{t.programText}</p>
              <a className="btn btn-sun mt-4" href={href(locale, "contact/")}>{t.programButton}</a>
            </div>
          </aside>
        </div>
      </section>

      {related.length > 0 && (
        <section className="band bg-sage">
          <div className="wrap">
            <div className="sec-head">
              <div>
                <h2 className="text-deep">{t.related}</h2>
              </div>
              <a className="more text-field" href={href(locale, "products/")}>{t.all}</a>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {related.map((r) => <ProductCard key={r.id} p={r} locale={locale} t={t} brand={m.families.packs.fertilizer} />)}
            </div>
          </div>
        </section>
      )}
    </main>
  );
}
