import type { Messages } from "@/messages/ar";
import { ChevronLeft, ChevronRight } from "./icons";

/** Page numbers for lists filtered in the browser. Arrows point the reading direction. */
export default function Pager({ pages, current, go, t, rtl }: { pages: number; current: number; go: (n: number) => void; t: Pick<Messages["common"], "pages" | "prev" | "next">; rtl: boolean }) {
  if (pages < 2) return null;
  const Back = rtl ? ChevronRight : ChevronLeft;
  const Forward = rtl ? ChevronLeft : ChevronRight;
  return (
    <nav className="pager mt-10" aria-label={t.pages}>
      <button type="button" aria-label={t.prev} disabled={current === 1} onClick={() => go(current - 1)}>
        <Back className="w-5 h-5" />
      </button>
      {Array.from({ length: pages }, (_, i) => i + 1).map((n) => (
        <button key={n} type="button" aria-current={n === current ? "page" : undefined} onClick={() => go(n)}>
          {n}
        </button>
      ))}
      <button type="button" aria-label={t.next} disabled={current === pages} onClick={() => go(current + 1)}>
        <Forward className="w-5 h-5" />
      </button>
    </nav>
  );
}

/** Scrolls a list back into view after a page change. */
export const scrollToList = (id: string) =>
  document.getElementById(id)?.scrollIntoView({ behavior: matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth", block: "start" });
