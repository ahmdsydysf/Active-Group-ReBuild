import PageHero from "@/components/site/PageHero";
import { getPages } from "@/lib/api";
import { href, messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta } from "@/lib/page";

const BG = ["#E3EDDD", "#EEF3EA"];

export async function generateMetadata({ params }: PageProps<"/[locale]/partners">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  return pageMeta(locale, "partners/", m.partners.title, m.partnersPage.lead);
}

export default async function PartnersPage({ params }: PageProps<"/[locale]/partners">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const t = m.partnersPage;
  const { partners } = await getPages(locale);

  return (
    <main id="main">
      <PageHero crumbs={[home(locale), { label: t.crumb }]} crumbsLabel={m.common.crumbs} title={m.partners.title} lead={t.lead} />
      <section className="band">
        <div className="wrap">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {partners.map((p, i) => (
              <article key={p.id} className="rounded-[28px] p-6 flex flex-col gap-4" style={{ background: BG[i % 2] }}>
                <div className="bg-white rounded-2xl h-28 grid place-items-center px-6">
                  {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
                  <img src={p.logo} alt={p.name} loading="lazy" className="h-12 w-auto max-w-full object-contain" />
                </div>
                <h2 className="text-xl text-deep">{p.name}</h2>
                {p.field && <p>{p.field}</p>}
                {p.website && (
                  <a className="more text-field self-start text-sm mt-auto" href={p.website} target="_blank" rel="noopener noreferrer">
                    {t.visit}
                  </a>
                )}
              </article>
            ))}
            <article className="rounded-[28px] p-6 flex flex-col justify-center gap-3 bg-deep text-white">
              <h2 className="text-2xl">{t.joinTitle}</h2>
              <p className="text-[#CFE3C8]">{t.joinText}</p>
              <a className="btn bg-leaf text-white self-start" href={href(locale, "contact/")}>{m.common.contact}</a>
            </article>
          </div>
        </div>
      </section>
    </main>
  );
}
