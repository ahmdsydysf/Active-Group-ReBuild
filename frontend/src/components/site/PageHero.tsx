import Flowers, { HERO_FLOWERS } from "./Flowers";

export type Crumb = { label: string; href?: string };

/** Coloured header of every inner page: breadcrumb, title, lead and the furrow edge. */
export default function PageHero({
  color = "field",
  crumbs,
  crumbsLabel,
  title,
  lead,
  flowers = false,
  wrapClass = "",
  children,
}: {
  color?: "field" | "deep";
  crumbs: Crumb[];
  crumbsLabel: string;
  title?: string;
  lead?: string | null;
  flowers?: boolean;
  wrapClass?: string;
  children?: React.ReactNode;
}) {
  return (
    <section className="phero" style={{ "--c": `var(--${color})`, "--ink": "#fff" } as React.CSSProperties}>
      {flowers && <Flowers items={HERO_FLOWERS} opacity={0.4} />}
      <div className={`wrap ${wrapClass}`}>
        <nav className="crumbs" aria-label={crumbsLabel}>
          {crumbs.map((c, i) => (
            <span key={i} className="contents">
              {i > 0 && <span aria-hidden="true">/</span>}
              {c.href ? <a href={c.href}>{c.label}</a> : <span aria-current="page">{c.label}</span>}
            </span>
          ))}
        </nav>
        {title && <h1>{title}</h1>}
        {lead && <p>{lead}</p>}
        {children}
      </div>
      <svg className="furrow" viewBox="0 0 1200 46" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 30 C150 6 300 46 450 24 S750 4 900 26 1100 40 1200 18 V46 H0Z" fill="#fff" />
      </svg>
    </section>
  );
}
