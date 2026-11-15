import { href, type Locale } from "@/lib/i18n";
import { telHref } from "@/lib/contact";
import type { Category, Site } from "@/lib/types";
import type { Messages } from "@/messages/ar";
import GrassLine from "./GrassLine";
import NewsletterForm from "./NewsletterForm";
import { MailIcon, PhoneIcon, PinIcon, socialIcons } from "./icons";

type Props = { locale: Locale; t: Messages; site: Site; categories: Pick<Category, "slug" | "name">[] };

export default function Footer({ locale, t, site, categories }: Props) {
  const f = t.footer;
  const { contact } = site;

  return (
    <footer className="ftr">
      <GrassLine />
      <div className="wrap">
        <div className="news">
          <div>
            <h4>{f.newsTitle}</h4>
            <p>{f.newsText}</p>
          </div>
          <NewsletterForm locale={locale} t={{ emailLabel: f.emailLabel, emailPlaceholder: f.emailPlaceholder, subscribe: f.subscribe, subscribed: f.subscribed, failed: f.failed }} />
        </div>
        <div className="cols">
          <div className="brand">
            <a className="logo" href={href(locale)}>
              {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
              <img src="/img/logo.webp" alt={site.site_name} width={170} height={52} loading="lazy" />
            </a>
            {site.footer_about && <p>{site.footer_about}</p>}
            <div className="social">
              {(Object.keys(socialIcons) as (keyof typeof socialIcons)[]).map((k) =>
                site.social[k] ? (
                  <a key={k} href={site.social[k]} aria-label={f.social[k]} target="_blank" rel="noopener noreferrer">
                    {socialIcons[k]}
                  </a>
                ) : null,
              )}
            </div>
          </div>
          <div>
            <h4>{f.products}</h4>
            <ul>
              {categories.map((c) => (
                <li key={c.slug}>
                  <a href={href(locale, `products/?c=${c.slug}`)}>{c.name}</a>
                </li>
              ))}
              <li>
                <a href={href(locale, "experiences/")}>{f.leaflets}</a>
              </li>
            </ul>
          </div>
          <div>
            <h4>{f.company}</h4>
            <ul>
              {f.companyLinks.map((l) => (
                <li key={l.href}>
                  <a href={href(locale, l.href)}>{l.label}</a>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4>{f.contact}</h4>
            <ul className="contact">
              {contact.address && (
                <li>
                  <PinIcon />
                  {contact.address}
                </li>
              )}
              {contact.phones.length > 0 && (
                <li>
                  <PhoneIcon />
                  <span>
                    {contact.phones.map((p, i) => (
                      <span key={p}>
                        {i > 0 && " · "}
                        <a dir="ltr" href={telHref(p)}>
                          {p}
                        </a>
                      </span>
                    ))}
                  </span>
                </li>
              )}
              {contact.email && (
                <li>
                  <MailIcon />
                  <a href={`mailto:${contact.email}`}>{contact.email}</a>
                </li>
              )}
            </ul>
          </div>
        </div>
        <div className="copy">
          <span>{f.rights(new Date().getFullYear(), site.site_name)}</span>
          <a href={href(locale, "privacy/")}>{f.privacy}</a>
        </div>
      </div>
    </footer>
  );
}
