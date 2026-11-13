import PageHero from "@/components/site/PageHero";
import PostBrowser from "@/components/site/PostBrowser";
import { CalendarIcon } from "@/components/site/icons";
import { getPosts } from "@/lib/api";
import { href, messages } from "@/lib/i18n";
import { formatDate } from "@/lib/format";
import { home, listLabels, pageLocale, pageMeta } from "@/lib/page";
import { cards } from "@/lib/posts";

export async function generateMetadata({ params }: PageProps<"/[locale]/blog">) {
  const locale = await pageLocale(params);
  const t = messages(locale).blog;
  return pageMeta(locale, "blog/", t.title, t.lead);
}

export default async function BlogPage({ params }: PageProps<"/[locale]/blog">) {
  const locale = await pageLocale(params);
  const m = messages(locale);
  const { posts, categories } = await getPosts(locale, "blog");
  const featured = posts.find((p) => p.is_featured) ?? null;
  const rest = posts.filter((p) => p !== featured);

  return (
    <main id="main">
      <PageHero crumbs={[home(locale), { label: m.blog.title }]} crumbsLabel={m.common.crumbs} title={m.blog.title} lead={m.blog.lead} />
      <section className="band !pt-14">
        <div className="wrap">
          {featured && (
            <a href={href(locale, `blog/${featured.slug}/`)} className="group grid lg:grid-cols-2 rounded-[32px] overflow-hidden bg-deep text-white mb-10">
              <div className="overflow-hidden min-h-[280px]">
                {featured.image && (
                  // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
                  <img src={featured.image} alt="" className="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
                )}
              </div>
              <div className="p-8 lg:p-12 flex flex-col justify-center gap-4">
                <span className="self-start bg-leaf text-white rounded-lg px-3 text-sm font-bold">{m.blog.featured}</span>
                <h2 className="text-[clamp(26px,3vw,40px)] font-extrabold">{featured.title}</h2>
                {featured.excerpt && <p className="text-[#CFE3C8]">{featured.excerpt}</p>}
                <small className="flex items-center gap-2 text-[#CFE3C8]">
                  <CalendarIcon className="w-4 h-4" />
                  {`${formatDate(featured.published_at, locale)} · ${m.common.read(featured.read_minutes)}`}
                </small>
              </div>
            </a>
          )}
          <PostBrowser locale={locale} posts={cards(rest, m.common.read)} categories={categories} variant="sidebar" t={listLabels(locale)} heading={m.blog.categories} />
        </div>
      </section>
    </main>
  );
}
