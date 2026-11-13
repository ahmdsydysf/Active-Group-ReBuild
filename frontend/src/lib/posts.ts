import type { Post } from "./types";

/** Card fields only (article bodies never go into list pages), with the reading time already worded. */
export const cards = (posts: Post[], read: (n: number) => string) =>
  posts.map((p) => ({
    id: p.id, slug: p.slug, section: p.section, title: p.title, excerpt: p.excerpt, category: p.category, type: p.type,
    image: p.image, image_sm: p.image_sm, read_minutes: p.read_minutes, published_at: p.published_at, is_featured: p.is_featured,
    read: read(p.read_minutes),
  }));
