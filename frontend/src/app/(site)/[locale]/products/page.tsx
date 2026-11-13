import ProductBrowser from "@/components/site/ProductBrowser";
import PageHero from "@/components/site/PageHero";
import { getCatalog } from "@/lib/api";
import { messages } from "@/lib/i18n";
import { home, listLabels, pageLocale, pageMeta } from "@/lib/page";

export async function generateMetadata({ params }: PageProps<"/[locale]/products">) {
  const locale = await pageLocale(params);
  const t = messages(locale).products;
  return pageMeta(locale, "products/", t.title, t.lead);
}

export default async function ProductsPage({ params }: PageProps<"/[locale]/products">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const { categories, products } = await getCatalog(locale);
  // Only what a card and the filters need goes to the browser.
  const cards = products.map(({ id, slug, name, name_alt, active_ingredient, category, type, type_name, category_name, color, pack, crops, image }) => ({
    id, slug, name, name_alt, active_ingredient, category, type, type_name, category_name, color, pack, crops, image,
  }));

  return (
    <main id="main">
      <PageHero color="deep" flowers crumbs={[home(locale), { label: m.products.title }]} crumbsLabel={m.common.crumbs} title={m.products.title} lead={m.products.lead} />
      <ProductBrowser locale={locale} products={cards} categories={categories} t={m.products} c={listLabels(locale)} brand={m.families.packs.fertilizer} />
    </main>
  );
}
