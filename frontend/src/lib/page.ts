import type { Metadata } from "next";
import { getSite } from "./api";
import { isLocale, type Locale, locales, messages } from "./i18n";

/** Reads the locale param of a site page; the layout already rejects anything else. */
export const pageLocale = async (params: Promise<{ locale: string }>): Promise<Locale> => {
  const { locale } = await params;
  return isLocale(locale) ? locale : "ar";
};

/** Title, description, canonical and hreflang links for a page at `path` (like "products/"). */
export async function pageMeta(locale: Locale, path: string, title: string, description?: string | null, image?: string | null): Promise<Metadata> {
  const site = await getSite(locale);
  return {
    title,
    description: description ?? site.seo.description ?? undefined,
    alternates: { canonical: `/${locale}/${path}`, languages: Object.fromEntries(locales.map((l) => [l, `/${l}/${path}`])) },
    openGraph: { title, description: description ?? undefined, locale, type: "website", siteName: site.site_name, images: image ? [image] : undefined },
  };
}

export const home = (locale: Locale) => ({ label: messages(locale).common.home, href: `/${locale}/` });

/** The plain-text labels list components need in the browser (functions cannot be sent there). */
export const listLabels = (locale: Locale) => {
  const { all, empty, pages, prev, next } = messages(locale).common;
  return { all, empty, pages, prev, next };
};
