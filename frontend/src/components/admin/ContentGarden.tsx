import type { Overview } from "@/lib/admin/types";

const LABELS: Record<string, [string, string]> = {
  slides: ["السلايدر", "#E0607E"],
  partners: ["الشركاء", "#F2C230"],
  testimonials: ["آراء العملاء", "#D08A2A"],
};
const BUDS = ["#F2C230", "#2E8FB0"];

const items = (n: number) => `${n} ${n === 1 ? "عنصر" : n === 2 ? "عنصرين" : n <= 10 ? "عناصر" : "عنصر"}`;

/**
 * One plant per dashboard section: height follows the item count, a full flower means everything
 * has English, a bud means some items still miss it.
 */
export default function ContentGarden({ sections }: { sections: Overview["sections"] }) {
  return (
    <section className="tile span12">
      <h3>حديقة المحتوى</h3>
      <div className="garden">
        {sections.map((s, i) => {
          const [label, color] = LABELS[s.key] ?? [s.key, "#E0607E"];
          const h = Math.min(190, 50 + 14.4 * Math.sqrt(s.total));
          const top = 200 - h;
          const tip = `${label}: ${items(s.total)}، ${s.missing_en ? `${s.missing_en} ناقص ترجمة` : "مترجم بالكامل"}`;
          return (
            <div key={s.key} className="gplant" tabIndex={0} aria-label={tip}>
              <span className="tip">{tip}</span>
              <svg viewBox="0 0 60 200" style={{ height: "100%" }} aria-hidden="true">
                <g className="stem" style={{ "--d": `${i * 0.12}s` } as React.CSSProperties}>
                  <path d={`M30 200 Q26 ${200 - h / 2} 30 ${top}`} stroke="#3E9A27" strokeWidth="3.5" fill="none" />
                  <path d={`M30 ${200 - h * 0.45} q-16 -10 -22 -2 q10 8 22 2z`} fill="#4CAF2E" />
                  <path d={`M30 ${200 - h * 0.65} q16 -10 22 -2 q-10 8 -22 2z`} fill="#3E9A27" />
                  {s.missing_en || !s.total ? (
                    <>
                      <ellipse cx="30" cy={top - 6} rx="7" ry="12" fill="#4CAF2E" />
                      <ellipse cx="30" cy={top - 10} rx="4" ry="6" fill={BUDS[i % 2]} />
                    </>
                  ) : (
                    <>
                      {[0, 60, 120, 180, 240, 300].map((r) => (
                        <ellipse key={r} cx="30" cy={top - 10} rx="6" ry="11" fill={color} transform={`rotate(${r} 30 ${top})`} />
                      ))}
                      <circle cx="30" cy={top} r="6" fill="#F2C230" stroke="#fff" strokeWidth="1.5" />
                    </>
                  )}
                </g>
              </svg>
            </div>
          );
        })}
      </div>
      <div className="glabels">
        {sections.map((s) => <span key={s.key}>{LABELS[s.key]?.[0] ?? s.key}</span>)}
      </div>
      <div className="legend">
        <span><i style={{ background: "var(--bloom)" }} />زهرة متفتحة = القسم مترجم بالكامل</span>
        <span><i style={{ background: "var(--leaf)" }} />برعم = فيه محتوى ناقصه ترجمة</span>
        <span>طول النبتة = عدد العناصر</span>
      </div>
    </section>
  );
}
