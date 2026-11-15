import PageHero from "@/components/site/PageHero";
import PostBrowser from "@/components/site/PostBrowser";
import { getPosts } from "@/lib/api";
import { messages } from "@/lib/i18n";
import { home, listLabels, pageLocale, pageMeta } from "@/lib/page";
import { cards } from "@/lib/posts";

export async function generateMetadata({ params }: PageProps<"/[locale]/experiences">) {
  const locale = await pageLocale(params);
  const t = messages(locale).advicePage;
  return pageMeta(locale, "experiences/", t.title, t.lead);
}

export default async function ExperiencesPage({ params }: PageProps<"/[locale]/experiences">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const { posts, categories } = await getPosts(locale, "advice");

  return (
    <main id="main">
      <PageHero color="deep" flowers crumbs={[home(locale), { label: m.advicePage.crumb }]} crumbsLabel={m.common.crumbs} title={m.advicePage.title} lead={m.advicePage.lead} />
      <section className="band !pt-14">
        <div className="wrap">
          <PostBrowser locale={locale} posts={cards(posts, m.common.read)} categories={categories} variant="chips" t={listLabels(locale)} heading={m.advicePage.filter} />
        </div>
      </section>
    </main>
  );
}
