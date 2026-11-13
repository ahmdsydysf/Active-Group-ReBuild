import ContactForm from "@/components/site/ContactForm";
import PageHero from "@/components/site/PageHero";
import { ClockIcon, MailIcon, PhoneIcon, PinIcon } from "@/components/site/icons";
import { getSite } from "@/lib/api";
import { telHref } from "@/lib/contact";
import { messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta } from "@/lib/page";

export async function generateMetadata({ params }: PageProps<"/[locale]/contact">) {
  const locale = await pageLocale(params);
  const t = messages(locale).contact;
  return pageMeta(locale, "contact/", t.title, t.lead);
}

function Row({ icon, title, children }: { icon: React.ReactNode; title: string; children: React.ReactNode }) {
  return (
    <div className="flex gap-4">
      <span className="w-12 h-12 rounded-2xl bg-white/10 grid place-items-center text-sun flex-none">{icon}</span>
      <div>
        <b className="font-display block">{title}</b>
        {children}
      </div>
    </div>
  );
}

export default async function ContactPage({ params }: PageProps<"/[locale]/contact">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const t = m.contact;
  const { contact, site_name } = await getSite(locale);

  return (
    <main id="main">
      <PageHero color="deep" crumbs={[home(locale), { label: t.title }]} crumbsLabel={m.common.crumbs} title={t.title} lead={t.lead} />
      <section className="band !pt-14">
        <div className="wrap grid lg:grid-cols-[1.3fr_1fr] gap-10">
          <ContactForm locale={locale} t={t} />
          <div className="space-y-5">
            <div className="rounded-[28px] bg-deep text-white p-7 space-y-5">
              {contact.address && (
                <Row icon={<PinIcon className="w-6 h-6" />} title={t.office}>
                  <span className="text-[#CFE3C8]">{contact.address}</span>
                </Row>
              )}
              {contact.phones.length > 0 && (
                <Row icon={<PhoneIcon className="w-6 h-6" />} title={t.phoneLabel}>
                  {contact.phones.map((p, i) => (
                    <span key={p}>
                      {i > 0 && " · "}
                      <a dir="ltr" href={telHref(p)} className="text-[#CFE3C8]">{p}</a>
                    </span>
                  ))}
                </Row>
              )}
              {contact.email && (
                <Row icon={<MailIcon className="w-6 h-6" />} title={t.emailLabel}>
                  <a href={`mailto:${contact.email}`} className="text-[#CFE3C8] break-all">{contact.email}</a>
                </Row>
              )}
              {contact.hours && (
                <Row icon={<ClockIcon className="w-6 h-6" />} title={t.hours}>
                  <span className="text-[#CFE3C8]">{contact.hours}</span>
                </Row>
              )}
            </div>
            <div className="rounded-[28px] overflow-hidden border border-line aspect-[4/3] relative bg-[#E3EBDF]">
              <svg viewBox="0 0 400 300" className="absolute inset-0 w-full h-full" aria-hidden="true">
                <g stroke="#fff" strokeWidth="10" fill="none"><path d="M-10 90 Q150 70 410 120" /><path d="M120 -10 Q140 160 100 310" /><path d="M-10 230 Q200 200 410 250" /></g>
                <g stroke="#fff" strokeWidth="4" fill="none" opacity=".7"><path d="M260 -10 L300 310" /><path d="M-10 160 L410 170" /></g>
              </svg>
              <div className="absolute start-1/2 top-[44%] -translate-x-1/2 rtl:translate-x-1/2 -translate-y-full flex flex-col items-center">
                <span className="bg-deep text-white text-sm rounded-xl px-3 py-1 mb-1 font-semibold whitespace-nowrap">{site_name}</span>
                <span className="w-10 h-10 rounded-full bg-bloom grid place-items-center text-white shadow-lg"><PinIcon className="w-5 h-5" /></span>
              </div>
              {contact.map_url && (
                <a href={contact.map_url} target="_blank" rel="noopener noreferrer" className="absolute bottom-3 end-3 btn bg-white text-deep !py-2 !px-4 text-sm">
                  {t.map}
                </a>
              )}
            </div>
          </div>
        </div>
      </section>
    </main>
  );
}
