export type AdminUser = { id: number; name: string; email: string; role: string };

/** Fields every dashboard list row carries (see ContentController::present). */
export type Row = { id: number; is_active: boolean; sort_order: number; missing_en: boolean } & Record<string, unknown>;

export type Slide = Row & {
  title_ar: string; title_en: string | null; subtitle_ar: string | null; subtitle_en: string | null;
  button_ar: string | null; button_en: string | null; button_url: string | null; image_url: string;
};
export type Testimonial = Row & {
  name_ar: string; name_en: string | null; role_ar: string | null; role_en: string | null;
  quote_ar: string; quote_en: string | null; color: string;
};
export type Partner = Row & {
  name_ar: string; name_en: string | null; field_ar: string | null; field_en: string | null; logo_url: string;
};

export type Overview = {
  sections: { key: string; total: number; missing_en: number }[];
  kpis: {
    messages_new: number; quotes_new: number; products: number; products_this_month: number;
    subscribers: number; subscribers_this_month: number; posts: number; last_post_at: string | null;
  };
  messages_by_month: number[];
  latest_messages: import("./messages").Message[];
  products_by_category: { name: string; color: string; count: number }[];
  latest_products: {
    id: number; name_ar: string; name_en: string | null; image_url: string | null; category: string | null; color: string | null;
    packs_ar: string | null; type: string | null; factory: string | null; is_active: boolean;
  }[];
};

export type StatInput = { value: number | string; suffix: string | null; label_ar: string; label_en: string | null };
export type TimelineItem = { year: number | string; title_ar: string; title_en: string | null; text_ar: string | null; text_en: string | null };
export type Settings = { stats: StatInput[]; timeline: TimelineItem[]; [key: string]: string | null | StatInput[] | TimelineItem[] };
