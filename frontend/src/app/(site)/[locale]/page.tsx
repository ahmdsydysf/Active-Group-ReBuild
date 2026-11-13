import type { Metadata } from "next";
import { Fragment } from "react";
import BloomCta from "@/components/site/BloomCta";
import CountUp from "@/components/shared/CountUp";
import FamilyCard from "@/components/site/FamilyCard";
import Garden from "@/components/shared/Garden";
import Journey from "@/components/site/Journey";
import Pack, { type PackKind } from "@/components/site/Packs";
import { ForwardArrow } from "@/components/site/icons";
import { getHome, getSite } from "@/lib/api";
import { whatsappHref } from "@/lib/contact";
import { href, isLocale, type Locale, messages } from "@/lib/i18n";
import { formatNumber } from "@/lib/format";
import type { Category } from "@/lib/types";

// Category colour (set in the dashboard) -> card style and pack drawing from the design.
const FAMILY: Record<string, [string, PackKind]> = { rust: ["f1", "pesticide"], field: ["f2", "fertilizer"], wheat: ["f3", "regulator"] };
// Advice card colours, in display order.
const ADVICE = [["#D9EBD1", "#123F1C"], ["#DCEEF5", "#0E3A4A"], ["var(--bloom)", "#fff"], ["var(--sun)", "var(--deep)"], ["#F7DCD4", "#5A1E12"]];
// Testimonial card colours offered in the dashboard.
const VOICE: Record<string, string> = { field: "var(--field)", rust: "var(--rust)", sky: "var(--sky)", plum: "#7A2140", deep: "var(--deep)", wheat: "var(--wheat)", bloom: "var(--bloom)" };
// The marquee needs at least this many logos to fill a wide screen before it loops.
const MARQUEE_MIN = 20;

export async function generateMetadata({ params }: PageProps<"/[locale]">): Promise<Metadata> {
  const { locale } = await params;
  const site = await getSite(locale as Locale);
  return {
    title: { absolute: site.seo.title ?? site.site_name },
    alternates: { canonical: `/${locale}/`, languages: { ar: "/ar/", en: "/en/" } },
    openGraph: { title: site.seo.title ?? site.site_name, description: site.seo.description ?? undefined, locale, type: "website" },
  };
}

export default async function HomePage({ params }: PageProps<"/[locale]">) {
  const { locale: raw } = await params;
  const locale = (isLocale(raw) ? raw : "ar") as Locale;
  const t = messages(locale);
  const rtl = locale === "ar";
  const [home, site] = await Promise.all([getHome(locale), getSite(locale)]);
  const [hero, ...rest] = home.slides;
  const collage = [hero, ...rest].filter(Boolean).slice(0, 3);
  const badge = site.stats[2]; // the third number doubles as the hero photo badge
  const logos = home.partners.length ? Array.from({ length: Math.ceil(MARQUEE_MIN / home.partners.length) * 2 }, () => home.partners).flat() : [];

  return (
    <>
      <main id="main">
        <section className="hero" aria-label={t.hero.label}>
          <Garden />
          <div className="wrap">
            <div>
              {hero && (
                <h1>
                  {hero.title.split(/\s+/).map((w, i) => (
                    <Fragment key={i}>
                      {i > 0 && " "}
                      <span className="w">
                        <span style={{ animationDelay: `${0.15 + i * 0.06}s` }}>{w}</span>
                      </span>
                    </Fragment>
                  ))}
                </h1>
              )}
              {hero?.subtitle && <p className="lead">{hero.subtitle}</p>}
              <div className="cta">
                {hero?.button && (
                  <a className="btn btn-sun" href={hero.button_url?.startsWith("/") ? `/${locale}${hero.button_url}` : (hero.button_url ?? href(locale, "products/"))}>
                    {hero.button}
                  </a>
                )}
                <a className="btn btn-line" href={href(locale, "contact/")}>
                  {t.hero.secondary}
                </a>
              </div>
              <div className="hint">
                <i>+</i>
                {t.hero.hint}
              </div>
            </div>
            <div className="hero-media" aria-hidden="true">
              {collage.map((s, i) => (
                <figure key={s.id} className={`hm hm-${"abc"[i]}`}>
                  {/* eslint-disable-next-line @next/next/no-img-element -- hidden under 1080px, so it never loads on phones */}
                  <img src={s.image_sm} alt="" />
                </figure>
              ))}
              {badge && (
                <div className="hm-badge">
                  <b>{rtl ? `${badge.suffix}${formatNumber(badge.value)}` : `${formatNumber(badge.value)}${badge.suffix}`}</b>
                  <span>{badge.label}</span>
                </div>
              )}
            </div>
          </div>
        </section>

        {home.categories.length > 0 && (
          <section className="band families" aria-labelledby="famH">
            <div className="wrap">
              <div className="sec-head">
                <div>
                  <h2 id="famH" className="text-deep">{t.families.title}</h2>
                  <p>{t.families.lead}</p>
                </div>
                <a className="more text-field" href={href(locale, "products/")}>{t.families.all}</a>
              </div>
              <div className="fam-grid">
                {home.categories.map((c: Category) => {
                  const [cls, pack] = FAMILY[c.color] ?? FAMILY.field;
                  return (
                    <FamilyCard key={c.id} className={`fam ${cls}`} href={href(locale, `products/?c=${c.slug}`)}>
                      <span className="count">{t.families.count(c.products_count)}</span>
                      <h3>{c.name}</h3>
                      {c.description && <p>{c.description}</p>}
                      <ul>
                        {c.types.map((type) => <li key={type}>{type}</li>)}
                      </ul>
                      <Pack kind={pack} labels={t.families.packs} />
                    </FamilyCard>
                  );
                })}
              </div>
            </div>
          </section>
        )}

        <Journey t={t.journey} productsHref={href(locale, "products/")} />

        {site.stats.length > 0 && (
          <section className="band numbers" aria-labelledby="nH">
            <div className="wrap">
              <div className="sec-head">
                <div>
                  <h2 id="nH">{t.numbers.title}</h2>
                  <p className="text-[#DDF0F7]">{t.numbers.lead}</p>
                </div>
              </div>
              <div className="num-grid">
                {site.stats.map((s) => (
                  <div className="num" key={s.label}>
                    <CountUp value={s.value} suffix={s.suffix} />
                    <span>{s.label}</span>
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}

        {home.services.length > 0 && (
          <section className="band" aria-labelledby="aH">
            <div className="wrap">
              <div className="sec-head">
                <div>
                  <h2 id="aH" className="text-deep">{t.advice.title}</h2>
                  <p>{t.advice.lead}</p>
                </div>
                <a className="more text-field" href={href(locale, "experiences/")}>{t.advice.all}</a>
              </div>
              <div className="advice-grid">
                {home.services.map((a, i) => (
                  <a key={a.id} className={`adv${i === 0 ? " big" : ""}`} href={href(locale, `experiences/${a.slug}/`)} style={{ "--c": ADVICE[i % 5][0], "--ink": ADVICE[i % 5][1] } as React.CSSProperties}>
                    {a.image && (
                      // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
                      <img src={i === 0 ? a.image : a.image_sm!} alt="" loading="lazy" decoding="async" />
                    )}
                    <div className="in">
                      {a.type && <span className="tag">{a.type}</span>}
                      <h3>{a.title}</h3>
                      {i === 0 && a.excerpt && <p>{a.excerpt}</p>}
                      <small>{t.advice.read(a.read_minutes)}</small>
                    </div>
                  </a>
                ))}
              </div>
            </div>
          </section>
        )}

        {logos.length > 0 && (
          <section className="band partners" aria-labelledby="pH">
            <div className="wrap">
              <div className="sec-head">
                <div>
                  <h2 id="pH">{t.partners.title}</h2>
                  <p className="text-[#B9CDB2]">{t.partners.lead}</p>
                </div>
                <a className="more text-sun" href={href(locale, "partners/")}>{t.partners.all}</a>
              </div>
            </div>
            <div className="logos">
              <div className="row">
                {logos.map((p, i) => (
                  <div key={i} aria-hidden={i >= home.partners.length || undefined}>
                    {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
                    <img src={p.logo} alt={i < home.partners.length ? p.name : ""} loading="lazy" height={44} />
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}

        {home.testimonials.length > 0 && (
          <section className="band voices" aria-labelledby="vH">
            <div className="wrap">
              <div className="sec-head justify-center text-center">
                <div>
                  <h2 id="vH" className="text-[#7A2140]">{t.voices.title}</h2>
                </div>
              </div>
              <div className="stack">
                {home.testimonials.map((v, i) => (
                  <figure key={v.id} className="voice" style={{ "--i": i, background: VOICE[v.color] ?? VOICE.field, margin: 0 } as React.CSSProperties}>
                    <p>{rtl ? `«${v.quote}»` : `“${v.quote}”`}</p>
                    <figcaption className="who">
                      <i>{Array.from(v.name.replace(/^(م|أ|د|Eng|Mr|Mrs|Dr|Hajj|الحاج)\.?\s+/u, ""))[0]}</i>
                      <div>
                        <b>{v.name}</b>
                        {v.role && <small>{v.role}</small>}
                      </div>
                    </figcaption>
                  </figure>
                ))}
              </div>
              <div className="text-center mt-10">
                <a className="btn voices-more" href={href(locale, "about/#voices")}>
                  {t.voices.more} <ForwardArrow rtl={rtl} className="w-5 h-5" />
                </a>
              </div>
            </div>
          </section>
        )}
      </main>
      <BloomCta t={t.cta} whatsapp={whatsappHref(site.contact.whatsapp)} />
    </>
  );
}
