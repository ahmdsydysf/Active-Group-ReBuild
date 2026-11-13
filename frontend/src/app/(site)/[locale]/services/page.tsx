import PageHero from "@/components/site/PageHero";
import { topicIcons } from "@/components/site/icons";
import { getPages } from "@/lib/api";
import { href, messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta } from "@/lib/page";

const TONES = [["#E3EDDD", "var(--field)"], ["#EEF3EA", "var(--deep)"]];

export async function generateMetadata({ params }: PageProps<"/[locale]/services">) {
  const locale = await pageLocale(params);
  const t = messages(locale).services;
  return pageMeta(locale, "services/", t.title, t.lead);
}

export default async function ServicesPage({ params }: PageProps<"/[locale]/services">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const t = m.services;
  const { services } = await getPages(locale);

  return (
    <main id="main">
      <PageHero flowers crumbs={[home(locale), { label: t.title }]} crumbsLabel={m.common.crumbs} title={t.title} lead={t.lead} />
      {services.length > 0 && (
        <section className="band">
          <div className="wrap">
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {services.map((s, i) => {
                const [bg, color] = TONES[i % 2];
                const Icon = topicIcons[s.icon] ?? topicIcons.leaf;
                return (
                  <article key={s.id} className="group rounded-[28px] overflow-hidden flex flex-col transition hover:-translate-y-1.5" style={{ background: bg }}>
                    <div className="relative">
                      <div className="h-48 overflow-hidden">
                        {s.image && (
                          // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
                          <img src={s.image} alt="" loading="lazy" decoding="async" className="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
                        )}
                      </div>
                      <span className="absolute bottom-0 start-6 translate-y-1/2 w-14 h-14 rounded-2xl grid place-items-center text-white shadow-lg" style={{ background: color }}>
                        <Icon className="w-7 h-7" />
                      </span>
                    </div>
                    <div className="p-6 pt-10 flex flex-col gap-2 flex-1">
                      <h2 className="text-[22px] text-deep">{s.title}</h2>
                      {s.summary && <p>{s.summary}</p>}
                      <a href={href(locale, "contact/#quote")} className="more mt-auto pt-3 self-start text-sm" style={{ color }}>
                        {t.request}
                      </a>
                    </div>
                  </article>
                );
              })}
            </div>
          </div>
        </section>
      )}
      <section className="band bg-deep text-white">
        <div className="wrap">
          <div className="sec-head">
            <div>
              <h2>{t.howTitle}</h2>
              <p className="text-[#CFE3C8]">{t.howLead}</p>
            </div>
          </div>
          <ol className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {t.steps.map(([title, text], i) => (
              <li key={title} className="relative bg-white/10 rounded-3xl p-6">
                <b className="font-display text-5xl font-black text-sun block">{i + 1}</b>
                <h3 className="text-xl mt-3">{title}</h3>
                <p className="text-[#CFE3C8] mt-1">{text}</p>
              </li>
            ))}
          </ol>
        </div>
      </section>
    </main>
  );
}
