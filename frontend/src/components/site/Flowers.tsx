/** Opening flowers drawn in CSS (.fl). Decorative only. */
export type FlowerSpec = { left: string; top: string; s: number; pc: string; cc: string; d: number };

const PETALS = [0, 45, 90, 135, 180, 225, 270, 315];

/** The six flowers along the top of inner page headers, as in the design. */
export const HERO_FLOWERS: FlowerSpec[] = [
  { left: "4%", top: "22%", s: 96, pc: "#fff", cc: "#F2C230", d: -0.96 },
  { left: "16%", top: "62%", s: 56, pc: "#E0607E", cc: "#fff", d: -1.75 },
  { left: "27%", top: "8%", s: 64, pc: "#F2C230", cc: "#7A4A0C", d: -1.66 },
  { left: "38%", top: "58%", s: 44, pc: "#fff", cc: "#E0607E", d: -2.48 },
  { left: "10%", top: "-14%", s: 70, pc: "#F7D9A8", cc: "#B4442E", d: -4.04 },
  { left: "95%", top: "-12%", s: 50, pc: "#E0607E", cc: "#F2C230", d: -4.74 },
];

export default function Flowers({ items, opacity }: { items: FlowerSpec[]; opacity: number }) {
  return (
    <div className="flowers" aria-hidden="true">
      {items.map((f, i) => (
        <div key={i} className="fl" style={{ left: f.left, top: f.top, "--s": `${f.s}px`, "--pc": f.pc, "--cc": f.cc, "--d": `${f.d}s`, opacity } as React.CSSProperties}>
          {PETALS.map((r) => (
            <span key={r} className="petal" style={{ "--r": `${r}deg` } as React.CSSProperties} />
          ))}
          <span className="core" />
        </div>
      ))}
    </div>
  );
}
