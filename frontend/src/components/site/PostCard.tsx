import { href, type Locale } from "@/lib/i18n";
import type { Post } from "@/lib/types";
import { ClockIcon } from "./icons";

const BG = ["#E3EDDD", "#EEF3EA"];

/** Article tile for the advice and blog lists. */
export default function PostCard({ post, locale, i, read }: { post: Post; locale: Locale; i: number; read: string }) {
  const base = post.section === "advice" ? "experiences" : "blog";
  return (
    <a className="adv" href={href(locale, `${base}/${post.slug}/`)} style={{ "--c": BG[i % 2], "--ink": "var(--soil)" } as React.CSSProperties}>
      {post.image_sm && (
        // eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are
        <img src={post.image_sm} alt="" loading="lazy" decoding="async" />
      )}
      <div className="in">
        {post.type && <span className="tag">{post.type}</span>}
        <h3>{post.title}</h3>
        {post.excerpt && <p className="text-[15px] opacity-80">{post.excerpt}</p>}
        <small className="flex items-center gap-1.5">
          <ClockIcon className="w-4 h-4" />
          {read}
        </small>
      </div>
    </a>
  );
}
