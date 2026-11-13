/** Shapes returned by the public API (see Swagger: Site, Home, Catalog, Post, About, Pages). */
export type Stat = { value: number; suffix: string; label: string };

export type Site = {
  site_name: string;
  footer_about: string | null;
  contact: {
    phones: string[];
    whatsapp: string | null;
    email: string | null;
    address: string | null;
    hours: string | null;
    map_url: string | null;
  };
  social: Partial<Record<"facebook" | "youtube" | "linkedin" | "instagram", string>>;
  stats: Stat[];
  seo: { title: string | null; description: string | null };
  categories: { slug: string; name: string }[];
};

export type Slide = {
  id: number;
  title: string;
  subtitle: string | null;
  button: string | null;
  button_url: string | null;
  image: string;
  image_sm: string;
};

export type Category = {
  id: number;
  slug: string;
  name: string;
  description: string | null;
  color: string;
  products_count: number;
  types: string[];
};

/** An article card (advice or blog). `body` and `author` come only with the full list from /posts. */
export type Post = {
  id: number;
  slug: string;
  section: "advice" | "blog";
  title: string;
  excerpt: string | null;
  category: string | null;
  type: string | null;
  image: string | null;
  image_sm: string | null;
  read_minutes: number;
  published_at: string;
  is_featured: boolean;
  body?: string | null;
  author?: { name: string; photo: string | null } | null;
  seo_description?: string | null;
};

export type Dose = { crop: string; pest?: string | null; rate?: string | null; interval?: string | null; phi?: string | null };

export type Product = {
  id: number;
  slug: string;
  name: string;
  name_alt: string | null;
  active_ingredient: string | null;
  category: string;
  type: string;
  type_name: string;
  category_name: string;
  color: string;
  pack: string | null;
  crops: string[];
  image: string | null;
  image_lg: string | null;
  is_featured: boolean;
  summary: string | null;
  body: string | null;
  formulation: string | null;
  packs: string | null;
  registration_no: string | null;
  in_stock: boolean;
  doses: Dose[];
  faqs: { q: string; a: string }[];
  safety: string[];
  leaflet: string | null;
  factory: string | null;
  seo: { title: string | null; description: string | null };
};

export type Factory = { id: number; name: string; description: string | null; address: string | null; image_sm: string | null };

export type Catalog = { categories: Category[]; factories: Factory[]; products: Product[] };

export type Posts = { categories: { slug: string; name: string }[]; posts: Post[] };

export type Feature = { id: number; title: string; description: string | null; icon: string; color: string };

export type TeamMember = { id: number; name: string; role: string | null; photo: string | null };

export type About = {
  title: string | null;
  paragraphs: string[];
  timeline: { year: number; title: string; text: string | null }[];
  features: Feature[];
  factories: Factory[];
  team: TeamMember[];
  testimonials: Testimonial[];
};

export type Service = { id: number; title: string; summary: string | null; icon: string; image: string | null };

export type Photo = { id: number; album: string | null; caption: string | null; image: string; image_sm: string };

export type Pages = { partners: Partner[]; services: Service[]; albums: { slug: string; name: string }[]; photos: Photo[] };

export type Partner = { id: number; name: string; field: string | null; logo: string; website: string | null };

export type Testimonial = { id: number; name: string; role: string | null; quote: string; color: string };

export type Home = {
  slides: Slide[];
  categories: Category[];
  advice: Post[];
  partners: Partner[];
  testimonials: Testimonial[];
};
