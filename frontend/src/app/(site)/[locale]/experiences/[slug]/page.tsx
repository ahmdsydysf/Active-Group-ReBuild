import ArticlePage, { articleParams } from "@/components/site/ArticlePage";
import { getPosts } from "@/lib/api";
import { locales } from "@/lib/i18n";
import { pageLocale, pageMeta } from "@/lib/page";

export const dynamicParams = false;

export const generateStaticParams = () => articleParams("advice", locales);

export async function generateMetadata({ params }: PageProps<"/[locale]/experiences/[slug]">) {
  const locale = await pageLocale(params);
  const { slug } = await params;
  const post = (await getPosts(locale, "advice")).posts.find((p) => p.slug === decodeURIComponent(slug));
  return post ? pageMeta(locale, `experiences/${post.slug}/`, post.title, post.seo_description, post.image) : {};
}

export default async function Page({ params }: PageProps<"/[locale]/experiences/[slug]">) {
  return <ArticlePage locale={await pageLocale(params)} section="advice" slug={(await params).slug} />;
}
