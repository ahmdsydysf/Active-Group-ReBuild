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
  sections: { key: "slides" | "testimonials" | "partners"; total: number; missing_en: number }[];
  subscribers: { total: number; this_month: number };
};

export type StatInput = { value: number | string; suffix: string | null; label_ar: string; label_en: string | null };
export type Settings = { stats: StatInput[]; [key: string]: string | null | StatInput[] };
