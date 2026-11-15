/** Western digits with thousands separators, as in the design (1,200). */
export const formatNumber = (n: number) => n.toLocaleString("en-US");

/** "3 نوفمبر 2026" / "3 November 2026", with Latin digits as in the design. */
export const formatDate = (iso: string, locale: "ar" | "en") =>
  new Intl.DateTimeFormat(locale === "ar" ? "ar-EG-u-nu-latn" : "en-GB", { day: "numeric", month: "long", year: "numeric" }).format(new Date(iso));
