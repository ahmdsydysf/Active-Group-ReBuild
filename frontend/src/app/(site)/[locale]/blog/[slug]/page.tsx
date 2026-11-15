import ArticlePage, { articleParams } from "@/components/site/ArticlePage";
import { getPosts } from "@/lib/api";
import { locales } from "@/lib/i18n";
import { pageLocale, pageMeta } from "@/lib/page";

export const dynamicParams = false;

export const generateStaticParams = () => articleParams("blog", locales);

export async function generateMetadata({ params }: PageProps<"/[locale]/blog/[slug]">) {
  const locale = await pageLocale(params);
  const { slug } = await params;
  const post = (await getPosts(locale, "blog")).posts.find((p) => p.slug === decodeURIComponent(slug));
  return post ? pageMeta(locale, `blog/${post.slug}/`, post.title, post.seo_description, post.image) : {};
}

export default async function Page({ params }: PageProps<"/[locale]/blog/[slug]">) {
  return <ArticlePage locale={await pageLocale(params)} section="blog" slug={(await params).slug} />;
}
