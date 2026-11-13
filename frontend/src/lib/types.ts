/** Shapes returned by the public API (see Swagger: Site, Home). */
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

export type Article = {
  id: number;
  slug: string;
  title: string;
  excerpt: string | null;
  type: string | null;
  image: string | null;
  image_sm: string | null;
  read_minutes: number;
};

export type Partner = { id: number; name: string; field: string | null; logo: string; website: string | null };

export type Testimonial = { id: number; name: string; role: string | null; quote: string; color: string };

export type Home = {
  slides: Slide[];
  categories: Category[];
  services: Article[];
  partners: Partner[];
  testimonials: Testimonial[];
};
