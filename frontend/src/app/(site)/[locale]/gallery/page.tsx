import GalleryGrid from "@/components/site/GalleryGrid";
import PageHero from "@/components/site/PageHero";
import { getPages } from "@/lib/api";
import { messages } from "@/lib/i18n";
import { home, pageLocale, pageMeta } from "@/lib/page";

export async function generateMetadata({ params }: PageProps<"/[locale]/gallery">) {
  const locale = await pageLocale(params);
  const t = messages(locale).gallery;
  return pageMeta(locale, "gallery/", t.title, t.lead);
}

export default async function GalleryPage({ params }: PageProps<"/[locale]/gallery">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const { photos, albums } = await getPages(locale);

  return (
    <main id="main">
      <PageHero color="deep" crumbs={[home(locale), { label: m.gallery.crumb }]} crumbsLabel={m.common.crumbs} title={m.gallery.title} lead={m.gallery.lead} />
      <section className="band !pt-14">
        <div className="wrap">
          <GalleryGrid photos={photos} albums={albums} t={m.gallery} all={m.common.all} rtl={locale === "ar"} />
        </div>
      </section>
    </main>
  );
}
