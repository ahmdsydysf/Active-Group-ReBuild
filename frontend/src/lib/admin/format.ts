const day = new Intl.DateTimeFormat("ar-EG", { day: "numeric", month: "long" });
const full = new Intl.DateTimeFormat("ar-EG", { day: "numeric", month: "long", year: "numeric" });
const time = new Intl.DateTimeFormat("ar-EG", { hour: "numeric", minute: "2-digit" });

/** "منذ ساعتين", "أمس", "3 نوفمبر": short times for tables. */
export function ago(iso: string | null | undefined): string {
  if (!iso) return "—";
  const d = new Date(iso);
  const mins = Math.round((Date.now() - d.getTime()) / 60000);
  if (mins < 1) return "دلوقتي";
  if (mins < 60) return `منذ ${mins} دقيقة`;
  if (mins < 60 * 24) {
    const h = Math.round(mins / 60);
    return h === 1 ? "منذ ساعة" : h === 2 ? "منذ ساعتين" : `منذ ${h} ساعات`;
  }
  if (mins < 60 * 48) return "أمس";
  return d.getFullYear() === new Date().getFullYear() ? day.format(d) : full.format(d);
}

export const fullDate = (iso: string | null | undefined) => (iso ? full.format(new Date(iso)) : "—");
export const dateTime = (iso: string) => `${full.format(new Date(iso))}، ${time.format(new Date(iso))}`;

/** ISO date to the value a datetime-local input wants, in the browser's time zone. */
export const toLocalInput = (iso: string | null | undefined) => {
  if (!iso) return "";
  const d = new Date(iso);
  return new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
};
