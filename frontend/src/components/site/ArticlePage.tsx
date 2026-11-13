import { notFound } from "next/navigation";
import { getPosts } from "@/lib/api";
import { href, type Locale, messages } from "@/lib/i18n";
import { formatDate } from "@/lib/format";
import { home, staticParams } from "@/lib/page";
import PageHero from "./PageHero";
import PostCard from "./PostCard";
import { CalendarIcon, ClockIcon, WhatsAppIcon, socialIcons } from "./icons";

const SITE = process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";

export const sectionPath = { advice: "experiences", blog: "blog" } as const;

/** A full article (advice or blog) with share links and three more from the same section. */
export default async function ArticlePage({ locale, section, slug }: { locale: Locale; section: "advice" | "blog"; slug: string }) {
  const m = messages(locale);
  const { posts } = await getPosts(locale, section);
  const post = posts.find((p) => p.slug === decodeURIComponent(slug));
  if (!post) notFound();
  const base = sectionPath[section];
  const url = `${SITE}/${locale}/${base}/${post.slug}/`;
  const more = [...posts.filter((p) => p.id !== post.id && p.category === post.category), ...posts.filter((p) => p.id !== post.id && p.category !== post.category)].slice(0, 3);
  const date = formatDate(post.published_at, locale);
  const listTitle = section === "advice" ? m.advicePage.crumb : m.blog.title;

  return (
    <main id="main">
      <PageHero color="deep" wrapClass="max-w-[920px]" crumbsLabel={m.common.crumbs} crumbs={[home(locale), { label: listTitle, href: href(locale, `${base}/`) }, { label: post.title }]}>
        {post.type && <span className="inline-block bg-field text-white rounded-lg px-3 text-sm font-bold mb-3">{post.type}</span>}
        <h1>{post.title}</h1>
        <div className="flex flex-wrap items-center gap-4 mt-5 text-[15px]">
          {post.author && (
            <span className="flex items-center gap-2">
              {post.author.photo && (
                // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
                <img src={post.author.photo} alt="" className="w-10 h-10 rounded-full object-cover" />
              )}
              {post.author.name}
            </span>
          )}
          <span className="flex items-center gap-1.5">
            <CalendarIcon className="w-4 h-4" />
            <time dateTime={post.published_at}>{date}</time>
          </span>
          <span className="flex items-center gap-1.5">
            <ClockIcon className="w-4 h-4" />
            {m.common.read(post.read_minutes)}
          </span>
        </div>
      </PageHero>
      <section className="pb-20">
        <div className="wrap max-w-[920px]">
          {post.image && (
            // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
            <img src={post.image} alt="" className="rounded-[28px] w-full aspect-[16/8] object-cover -mt-4 relative" />
          )}
          <div className="grid lg:grid-cols-[56px_1fr] gap-8 mt-10">
            <aside>
              <div className="lg:sticky lg:top-28 flex lg:flex-col gap-2">
                <span className="sr-only">{m.post.share}</span>
                <a href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`} target="_blank" rel="noopener noreferrer" aria-label={m.post.shareFacebook} className="w-12 h-12 rounded-2xl bg-sage grid place-items-center text-deep hover:bg-sun [&_svg]:w-5 [&_svg]:h-5">
                  {socialIcons.facebook}
                </a>
                <a href={`https://wa.me/?text=${encodeURIComponent(`${post.title} ${url}`)}`} target="_blank" rel="noopener noreferrer" aria-label={m.post.shareWhatsapp} className="w-12 h-12 rounded-2xl bg-sage grid place-items-center text-deep hover:bg-sun">
                  <WhatsAppIcon className="w-5 h-5" />
                </a>
              </div>
            </aside>
            {/* Cleaned on the server (HTMLPurifier allowlist) before it is stored. */}
            <article className="prose-ag" dangerouslySetInnerHTML={{ __html: post.body ?? "" }} />
          </div>
        </div>
      </section>
      {more.length > 0 && (
        <section className="band bg-sage">
          <div className="wrap">
            <div className="sec-head">
              <div>
                <h2 className="text-deep">{m.post.more}</h2>
              </div>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {more.map((p, i) => <PostCard key={p.id} post={p} locale={locale} i={i + 1} read={m.common.read(p.read_minutes)} />)}
            </div>
          </div>
        </section>
      )}
    </main>
  );
}

/** Static params for every published article of a section, in both languages. */
export async function articleParams(section: "advice" | "blog", locales: readonly Locale[]) {
  const lists = await Promise.all(locales.map(async (locale) => (await getPosts(locale, section)).posts.map((p) => ({ locale, slug: p.slug }))));
  return staticParams(lists.flat(), locales[0]);
}
