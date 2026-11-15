import type { Metadata, Viewport } from "next";
import { notFound } from "next/navigation";
import Footer from "@/components/site/Footer";
import Header from "@/components/site/Header";
import Sprout from "@/components/site/Sprout";
import { getSite } from "@/lib/api";
import { telHref, whatsappHref } from "@/lib/contact";
import { alexandria, plexArabic } from "@/lib/fonts";
import { dir, href, isLocale, locales, messages } from "@/lib/i18n";
import "@/styles/site.css";

export const dynamicParams = false;

export function generateStaticParams() {
  return locales.map((locale) => ({ locale }));
}

export const viewport: Viewport = { themeColor: "#123F1C", width: "device-width", initialScale: 1, viewportFit: "cover" };

export async function generateMetadata({ params }: LayoutProps<"/[locale]">): Promise<Metadata> {
  const { locale } = await params;
  if (!isLocale(locale)) return {};
  const site = await getSite(locale);
  return {
    metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"),
    title: { default: site.seo.title ?? site.site_name, template: `%s | ${site.site_name}` },
    description: site.seo.description ?? undefined,
    icons: {
      icon: [{ url: "/favicon.ico", sizes: "any" }, { url: "/img/favicon-32.png", type: "image/png", sizes: "32x32" }],
      apple: "/img/apple-touch-icon.png",
    },
  };
}

export default async function SiteLayout({ children, params }: LayoutProps<"/[locale]">) {
  const { locale } = await params;
  if (!isLocale(locale)) notFound();
  const t = messages(locale);
  const site = await getSite(locale);
  const phone = site.contact.phones[0];

  return (
    <html lang={locale} dir={dir(locale)} className={`${alexandria.variable} ${plexArabic.variable}`}>
      <body className="site">
        <a href="#main" className="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-[100] focus:bg-sun focus:text-deep focus:px-4 focus:py-2 focus:rounded-xl">
          {t.skip}
        </a>
        <Header locale={locale} t={t.nav} brandHome={t.brandHome} siteName={site.site_name} />
        {children}
        <Footer locale={locale} t={t} site={site} categories={site.categories} />
        <Sprout t={t.sprout} whatsapp={whatsappHref(site.contact.whatsapp)} phone={phone ? telHref(phone) : null} quote={href(locale, "contact/#quote")} />
      </body>
    </html>
  );
}
