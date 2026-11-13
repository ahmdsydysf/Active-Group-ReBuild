import PageHero from "@/components/site/PageHero";
import VoicesSlider from "@/components/site/VoicesSlider";
import { FactoryIcon, topicIcons } from "@/components/site/icons";
import { getAbout, getSite } from "@/lib/api";
import { formatNumber } from "@/lib/format";
import { href, messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta } from "@/lib/page";

// Tile colour and its soft background, by the colour picked in the dashboard.
const TILE: Record<string, [string, string]> = {
  field: ["var(--field)", "#D9EBD1"], bloom: ["var(--bloom)", "#FBE0E7"], sky: ["var(--sky)", "#DCEEF5"],
  wheat: ["var(--wheat)", "#FCEFC4"], rust: ["var(--rust)", "#F7DCD4"], deep: ["var(--deep)", "#D9EBD1"],
};
const TIMELINE = ["var(--leaf)", "var(--sky)", "var(--wheat)", "var(--bloom)"];
const FACTORY = ["var(--field)", "var(--sky)", "var(--rust)", "var(--wheat)"];
const TEAM = ["var(--sage)", "#FCEFC4", "#FBE0E7", "#DCEEF5"];

export async function generateMetadata({ params }: PageProps<"/[locale]/about">) {
  const locale = await pageLocale(params);
  const t = messages(locale).about;
  return pageMeta(locale, "about/", t.title, t.lead);
}

export default async function AboutPage({ params }: PageProps<"/[locale]/about">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const t = m.about;
  const rtl = locale === "ar";
  const [about, site] = await Promise.all([getAbout(locale), getSite(locale)]);
  const badge = site.stats[2];

  return (
    <main id="main">
      <PageHero crumbs={[home(locale), { label: t.title }]} crumbsLabel={m.common.crumbs} title={t.title} lead={t.lead} />

      <section className="band">
        <div className="wrap grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <span className="kick">{t.story}</span>
            {about.title && <h2 className="text-[clamp(28px,3.4vw,44px)] font-extrabold text-deep mt-2">{about.title}</h2>}
            {about.paragraphs.map((p, i) => <p key={i} className="mt-4 text-lg">{p}</p>)}
            <div className="flex flex-wrap gap-3 mt-7">
              <a className="btn btn-wheat" href={href(locale, "contact/")}>{m.common.contact}</a>
              <a className="btn border border-line text-deep" href={href(locale, "products/")}>{t.products}</a>
            </div>
          </div>
          <div className="relative">
            {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
            <img src="/img/gardener.webp" alt={t.photoAlt} width={876} height={584} loading="lazy" className="rounded-[32px] w-full aspect-[4/3] object-cover" />
            {badge && (
              <div className="absolute -bottom-6 start-6 bg-sun text-deep rounded-3xl px-6 py-4 shadow-xl">
                <b className="font-display text-4xl font-black block leading-none" dir="ltr">{rtl ? `${badge.suffix}${formatNumber(badge.value)}` : `${formatNumber(badge.value)}${badge.suffix}`}</b>
                <span className="text-sm font-semibold">{badge.label}</span>
              </div>
            )}
          </div>
        </div>
      </section>

      {about.features.length > 0 && (
        <section className="band bg-sage">
          <div className="wrap">
            <div className="sec-head">
              <div>
                <h2 className="text-deep">{t.whyTitle}</h2>
                <p>{t.whyLead}</p>
              </div>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {about.features.map((f) => {
                const [c, bg] = TILE[f.color] ?? TILE.field;
                const Icon = topicIcons[f.icon] ?? topicIcons.shield;
                return (
                  <div key={f.id} className="vtile" style={{ "--c": c, "--bg": bg } as React.CSSProperties}>
                    <div className="vi"><Icon /></div>
                    <h3>{f.title}</h3>
                    {f.description && <p>{f.description}</p>}
                  </div>
                );
              })}
            </div>
          </div>
        </section>
      )}

      {about.timeline.length > 0 && (
        <section className="band">
          <div className="wrap grid lg:grid-cols-[1fr_1.2fr] gap-12">
            <div>
              <span className="kick" style={{ "--c": "var(--wheat)" } as React.CSSProperties}>{t.journey}</span>
              <h2 className="text-[clamp(28px,3.4vw,44px)] font-extrabold text-deep mt-2">{t.journeyTitle}</h2>
            </div>
            <div className="tl">
              {about.timeline.map((s, i) => (
                <div key={`${s.year}-${i}`} style={{ "--c": TIMELINE[i % TIMELINE.length] } as React.CSSProperties}>
                  <small className="text-soil-2 font-semibold">{s.year}</small>
                  <b>{s.title}</b>
                  {s.text && <p>{s.text}</p>}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {about.factories.length > 0 && (
        <section className="band bg-[#F6E7CF]">
          <div className="wrap">
            <div className="sec-head">
              <div>
                <h2 className="text-deep">{t.factoriesTitle}</h2>
                <p>{t.factoriesLead}</p>
              </div>
            </div>
            <div className="grid md:grid-cols-3 gap-5">
              {about.factories.map((f, i) => (
                <div key={f.id} className="rounded-[26px] p-6 text-white" style={{ background: FACTORY[i % FACTORY.length] }}>
                  <div className="w-12 h-12 rounded-2xl bg-white/20 grid place-items-center mb-4">
                    <FactoryIcon className="w-6 h-6" />
                  </div>
                  <h3 className="text-xl">{f.name}</h3>
                  {f.description && <p className="opacity-90 mt-1">{f.description}</p>}
                  {f.address && <p className="opacity-75 mt-1 text-sm">{f.address}</p>}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {about.testimonials.length > 0 && (
        <section className="band voices-slider" id="voices" aria-labelledby="avH">
          <div className="wrap">
            <VoicesSlider
              items={about.testimonials}
              rtl={rtl}
              labels={{ prev: m.common.prev, next: m.common.next, region: t.voicesKick }}
              heading={
                <div>
                  <span className="kick" style={{ "--c": "#7A2140" } as React.CSSProperties}>{t.voicesKick}</span>
                  <h2 id="avH" className="text-[#7A2140] mt-2">{m.voices.title}</h2>
                  <p>{t.voicesLead}</p>
                </div>
              }
            />
          </div>
        </section>
      )}

      {about.team.length > 0 && (
        <section className="band">
          <div className="wrap">
            <div className="sec-head">
              <div>
                <h2 className="text-deep">{t.teamTitle}</h2>
                <p>{t.teamLead}</p>
              </div>
            </div>
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
              {about.team.map((p, i) => (
                <div key={p.id} className="tcard" style={{ "--c": TEAM[i % TEAM.length] } as React.CSSProperties}>
                  <div className="ph">
                    {p.photo && (
                      // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
                      <img src={p.photo} alt={p.name} loading="lazy" decoding="async" />
                    )}
                  </div>
                  <b>{p.name}</b>
                  {p.role && <small>{p.role}</small>}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}
    </main>
  );
}
