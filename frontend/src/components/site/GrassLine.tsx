/**
 * Animated grass along the top of the footer. Blade shapes come from a fixed seed,
 * so every build renders the same markup.
 */
function blades() {
  let seed = 7;
  const rnd = () => ((seed = (seed * 16807) % 2147483647) - 1) / 2147483646;
  const out: { d: string; fill: string; delay: string }[] = [];
  for (let x = 0; x < 1200; x += 9) {
    const tip = 2 + rnd() * 23;
    const lean = -1 + rnd() * 8;
    const mid = (40 + tip) / 2;
    out.push({
      d: `M${x} 40 Q${x + 3} ${mid.toFixed(1)} ${(x + 3 + lean).toFixed(1)} ${tip.toFixed(1)} Q${x + 5} ${mid.toFixed(1)} ${x + 8} 40Z`,
      fill: rnd() > 0.5 ? "#123F1C" : "#1F6B2E",
      delay: `-${(rnd() * 4).toFixed(2)}s`,
    });
  }
  return out;
}

const BLADES = blades();

export default function GrassLine() {
  return (
    <svg className="grassline" viewBox="0 0 1200 40" preserveAspectRatio="none" aria-hidden="true">
      {BLADES.map((b, i) => (
        <path key={i} d={b.d} fill={b.fill} style={{ animationDelay: b.delay }} />
      ))}
    </svg>
  );
}
