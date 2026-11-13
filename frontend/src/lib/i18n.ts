import ar from "@/messages/ar";
import en from "@/messages/en";

export const locales = ["ar", "en"] as const;
export type Locale = (typeof locales)[number];

export const isLocale = (v: string): v is Locale => (locales as readonly string[]).includes(v);

export const messages = (locale: Locale) => (locale === "en" ? en : ar);

export const dir = (locale: Locale) => (locale === "ar" ? "rtl" : "ltr");

/** Site path for a locale: href("ar", "products/") -> "/ar/products/" */
export const href = (locale: Locale, path = "") => `/${locale}/${path}`;
