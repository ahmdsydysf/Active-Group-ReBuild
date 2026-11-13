/**
 * Product pack drawings from the design: a bottle for pesticides, a jerrycan for liquids and a bag for everything else.
 * The colour follows the product category.
 */
export type PackShape = "bottle" | "jerrycan" | "bag";

export const PACK_COLORS: Record<string, string> = {
  rust: "#B4442E", field: "#1F6B2E", wheat: "#D08A2A", sky: "#2E8FB0", plum: "#7A2140", deep: "#123F1C", bloom: "#E0607E",
};

const display = { fontFamily: "var(--display)" };
const LIQUID = /(لتر|مل|\bml\b|\bl\b|liter|litre)/i;

/** Pesticides come in bottles; other liquids in jerrycans; powders and granules in bags. */
export const packShape = (color: string, pack: string | null): PackShape => (color === "rust" ? "bottle" : pack && LIQUID.test(pack) ? "jerrycan" : "bag");

export default function Pack({ shape, color, label, sub, style }: { shape: PackShape; color: string; label: string; sub?: string; style?: React.CSSProperties }) {
  const fill = PACK_COLORS[color] ?? PACK_COLORS.field;
  const size = (base: number, room: number) => Math.min(base, Math.floor((room / Math.max(label.length, 1)) * 1.6));

  if (shape === "bottle")
    return (
      <svg className="pack" viewBox="0 0 120 200" style={style} aria-hidden="true">
        <rect x="44" y="6" width="32" height="20" rx="4" fill="#2B2118" />
        <path d="M40 26h40v14c14 6 22 16 22 30v116a8 8 0 0 1-8 8H26a8 8 0 0 1-8-8V70c0-14 8-24 22-30z" fill="#fff" />
        <path d="M18 92h84v74H18z" fill={fill} />
        <text x="60" y="124" textAnchor="middle" style={display} fontWeight="700" fontSize={size(12, 80)} fill="#fff">{label}</text>
        <rect x="34" y="136" width="52" height="4" rx="2" fill="#fff" opacity=".6" />
        <path d="M60 72c-8 0-13 6-13 12 7 0 13-5 13-12zm0 0c8 0 13 6 13 12-7 0-13-5-13-12z" fill={fill} />
      </svg>
    );
  if (shape === "jerrycan")
    return (
      <svg className="pack" viewBox="0 0 150 200" style={style} aria-hidden="true">
        <path d="M96 10h24v18H96z" fill="#2B2118" />
        <path d="M20 40c0-8 6-14 14-14h52l14-6h26v20c6 4 10 10 10 18v122a8 8 0 0 1-8 8H28a8 8 0 0 1-8-8z" fill="#fff" />
        <path d="M34 40h30v18H34z" fill="none" stroke="#2B2118" strokeWidth="5" />
        <path d="M20 86h116v76H20z" fill={fill} />
        <text x="78" y="124" textAnchor="middle" style={display} fontWeight="700" fontSize={size(14, 110)} fill="#fff">{label}</text>
        <rect x="50" y="136" width="56" height="4" rx="2" fill="#fff" opacity=".6" />
      </svg>
    );
  return (
    <svg className="pack" viewBox="0 0 150 200" style={style} aria-hidden="true">
      <path d="M14 18c20-8 102-8 122 0l-4 10 6 160c-30 8-96 8-126 0l6-160z" fill="#fff" />
      <path d="M12 74h126v80H12z" fill={fill} />
      <text x="75" y="108" textAnchor="middle" style={display} fontWeight="800" fontSize={size(15, 120)} fill="#fff">{label}</text>
      {sub && <text x="75" y="132" textAnchor="middle" fontSize="12" fill="#fff" opacity=".85">{sub}</text>}
      <path d="M75 38c-10 0-16 8-16 16 9 0 16-6 16-16zm0 0c10 0 16 8 16 16-9 0-16-6-16-16z" fill={fill} />
      <path d="M14 18c20-8 102-8 122 0" stroke="#2B2118" strokeWidth="4" fill="none" />
    </svg>
  );
}
