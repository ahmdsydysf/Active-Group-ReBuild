"use client";

import { useEffect, useRef, useState } from "react";
import type { Messages } from "@/messages/ar";

const STAGE_BG = ["#F6E7CF", "#F7DCD4", "#D9EBD1", "#FBE0E7", "#FCEFC4"];
const FLOWERS: [number, number, string][] = [[200, 80, "#E0607E"], [116, 210, "#F2C230"], [290, 168, "#E0607E"], [110, 320, "#F2C230"], [296, 275, "#E0607E"]];
const LEAVES: [number, string][] = [[0.2, "M198 330 Q140 300 110 320 Q150 350 198 330Z"], [0.3, "M203 290 Q262 255 295 275 Q255 310 203 290Z"], [0.45, "M204 230 Q150 195 118 210 Q160 245 204 230Z"], [0.6, "M206 180 Q258 150 288 168 Q250 198 206 180Z"]];
const ROOTS = ["M200 405 Q195 450 205 500 Q210 540 198 580", "M200 420 Q170 450 140 470 Q120 485 110 520", "M202 425 Q235 455 262 470 Q285 485 292 525", "M200 455 Q175 480 165 540", "M204 460 Q228 490 238 545"];

const clamp = (v: number) => Math.max(0, Math.min(1, v));

/**
 * "The plant's journey": a sticky section where scrolling grows a plant through five stages,
 * each with its recommended products. Every frame writes SVG attributes directly, no re-render.
 */
export default function Journey({ t, productsHref }: { t: Messages["journey"]; productsHref: string }) {
  const section = useRef<HTMLElement>(null);
  const svg = useRef<SVGSVGElement>(null);
  const [stage, setStage] = useState(0);

  useEffect(() => {
    const J = section.current, P = svg.current;
    if (!J || !P) return;
    const RM = matchMedia("(prefers-reduced-motion: reduce)").matches;
    const roots = [...P.querySelectorAll<SVGPathElement>(".j-root")];
    const stem = P.querySelector<SVGPathElement>(".j-stem")!;
    const leaves = [...P.querySelectorAll<SVGPathElement>(".j-leaf")];
    const pfs = [...P.querySelectorAll<SVGGElement>(".pf")];
    const pts = [...P.querySelectorAll<SVGGElement>(".pt")];
    const seedEl = P.querySelector<SVGEllipseElement>(".j-seed")!;
    const rails = [...J.querySelectorAll<HTMLElement>(".rail i")];

    const update = () => {
      const r = J.getBoundingClientRect();
      const p = RM ? 1 : clamp(-r.top / (J.offsetHeight - innerHeight));
      roots.forEach((el, i) => (el.style.strokeDashoffset = String(1 - clamp((p - 0.08 - i * 0.02) / 0.16))));
      stem.style.strokeDashoffset = String(1 - clamp((p - 0.22) / 0.2));
      leaves.forEach((l, i) => (l.style.transform = `scale(${clamp((p - 0.3 - LEAVES[i][0] * 0.25) / 0.08)})`));
      pfs.forEach((g, i) => {
        const s = clamp((p - 0.6 - i * 0.03) / 0.08) * (1 - clamp((p - 0.83) / 0.08) * 0.85);
        g.setAttribute("transform", `translate(${FLOWERS[i][0]} ${FLOWERS[i][1]}) scale(${s}) rotate(${s * 30})`);
      });
      pts.forEach((g, i) => g.setAttribute("transform", `translate(${FLOWERS[i][0]} ${FLOWERS[i][1] + 22}) scale(${clamp((p - 0.84 - i * 0.025) / 0.08)})`));
      seedEl.style.opacity = String(1 - clamp((p - 0.25) / 0.1) * 0.6);
      rails.forEach((el, i) => el.style.setProperty("--f", String(clamp(p * 5 - i))));
      setStage(Math.min(4, Math.floor(p * 5)));
    };
    addEventListener("scroll", update, { passive: true });
    update();
    return () => removeEventListener("scroll", update);
  }, []);

  return (
    <section className="band journey" id="journey" aria-labelledby="jH" ref={section} style={{ "--stage-bg": STAGE_BG[stage] } as React.CSSProperties}>
      <div className="stick">
        <div className="wrap">
          <div>
            <div className="kicker">{t.kicker}</div>
            <h2 id="jH">{t.title}</h2>
            <div className="rail">
              {t.stages.map((_, i) => (
                <span key={i}>
                  <i />
                </span>
              ))}
            </div>
            <div className="stage-text">
              {t.stages.map((s, i) => (
                <div key={i} className={`stage${i === stage ? " on" : ""}`}>
                  <h3>
                    <small>{s.step}</small>
                    {s.title}
                  </h3>
                  <p>{s.text}</p>
                  <div className="recs">
                    {s.recs.map(([name, color]) => (
                      <a key={name} href={productsHref}>
                        <i style={{ background: color }} />
                        {name}
                      </a>
                    ))}
                  </div>
                </div>
              ))}
            </div>
          </div>
          <div className="plant-wrap">
            <svg id="plant" ref={svg} viewBox="0 0 400 600" aria-hidden="true">
              <defs>
                <linearGradient id="soilG" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0" stopColor="#6B4A2E" />
                  <stop offset="1" stopColor="#3B2716" />
                </linearGradient>
              </defs>
              <rect x="0" y="380" width="400" height="220" rx="30" fill="url(#soilG)" />
              <g stroke="#8A6440" strokeWidth="2" fill="none" opacity=".5">
                <path d="M20 420 Q200 405 380 420" />
                <path d="M20 470 Q200 455 380 470" />
                <path d="M20 520 Q200 505 380 520" />
              </g>
              <ellipse className="j-seed" cx="200" cy="400" rx="16" ry="10" fill="#E3B66A" />
              <g stroke="#E8D3B0" strokeWidth="4" fill="none" strokeLinecap="round">
                {ROOTS.map((d) => (
                  <path key={d} className="j-root" pathLength={1} d={d} style={{ strokeDasharray: 1, strokeDashoffset: 1 }} />
                ))}
              </g>
              <path className="j-stem" pathLength={1} d="M200 392 Q190 300 205 220 Q215 150 200 80" stroke="#3E9A27" strokeWidth="10" fill="none" strokeLinecap="round" style={{ strokeDasharray: 1, strokeDashoffset: 1 }} />
              <g fill="#4CAF2E">
                {LEAVES.map(([, d]) => {
                  const [x, y] = d.split(" ").slice(0, 2);
                  return <path key={d} className="j-leaf" d={d} style={{ transform: "scale(0)", transformOrigin: `${x.slice(1)}px ${y}px` }} />;
                })}
              </g>
              <g>
                {FLOWERS.map(([x, y, c], i) => (
                  <g key={i} className="pf" transform={`translate(${x} ${y}) scale(0)`}>
                    {[0, 60, 120, 180, 240, 300].map((k) => (
                      <ellipse key={k} cx="0" cy="-14" rx="8" ry="15" fill={c} transform={`rotate(${k})`} />
                    ))}
                    <circle r="8" fill="#F2C230" stroke="#D08A2A" strokeWidth="2" />
                  </g>
                ))}
              </g>
              <g>
                {FLOWERS.map(([x, y], i) => (
                  <g key={i} className="pt" transform={`translate(${x} ${y + 22}) scale(0)`}>
                    <circle r="22" fill="#D93A2B" />
                    <ellipse cx="-7" cy="-8" rx="6" ry="4" fill="#fff" opacity=".35" />
                    <path d="M-8 -20 L0 -14 L8 -20 L4 -24 L0 -18 L-4 -24Z" fill="#3E9A27" />
                  </g>
                ))}
              </g>
            </svg>
          </div>
        </div>
      </div>
    </section>
  );
}
