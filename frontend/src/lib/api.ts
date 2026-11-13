import "server-only";
import type { Locale } from "./i18n";
import type { Home, Site } from "./types";

const API = process.env.API_URL ?? "http://localhost:8000";

/** Runs during `next build`. A failed call stops the build instead of publishing an empty site. */
async function get<T>(path: string, locale: Locale): Promise<T> {
  const res = await fetch(`${API}/api/v1/${path}?lang=${locale}`, {
    headers: { Accept: "application/json" },
    cache: "force-cache",
  });
  if (!res.ok) throw new Error(`API ${path} (${locale}) answered ${res.status}`);
  return ((await res.json()) as { data: T }).data;
}

export const getSite = (locale: Locale) => get<Site>("site", locale);
export const getHome = (locale: Locale) => get<Home>("home", locale);
