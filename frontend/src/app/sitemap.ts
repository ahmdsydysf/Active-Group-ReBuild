import type { MetadataRoute } from "next";
import { getCatalog, getPosts } from "@/lib/api";
import { locales } from "@/lib/i18n";

export const dynamic = "force-static";

const SITE = process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";
const PAGES = ["", "about/", "products/", "services/", "experiences/", "blog/", "partners/", "gallery/", "contact/"];

/** Every public page in both languages, with hreflang alternates. Rebuilt on each publish. */
export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [catalog, advice, blog] = await Promise.all([getCatalog("ar"), getPosts("ar", "advice"), getPosts("ar", "blog")]);
  const paths = [
    ...PAGES,
    ...catalog.products.map((p) => `products/${p.slug}/`),
    ...advice.posts.map((p) => `experiences/${p.slug}/`),
    ...blog.posts.map((p) => `blog/${p.slug}/`),
  ];
  return paths.map((path) => ({
    url: `${SITE}/ar/${path}`,
    alternates: { languages: Object.fromEntries(locales.map((l) => [l, `${SITE}/${l}/${path}`])) },
  }));
}
