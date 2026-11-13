import type { Messages } from "@/messages/ar";

/** Product pack drawings on the family cards. Which one a category gets follows its colour. */
export type PackKind = "pesticide" | "fertilizer" | "regulator";

const display = { fontFamily: "var(--display)" };

export default function Pack({ kind, labels }: { kind: PackKind; labels: Messages["families"]["packs"] }) {
  if (kind === "pesticide")
    return (
      <svg className="pack" viewBox="0 0 120 200" aria-hidden="true">
        <rect x="44" y="6" width="32" height="20" rx="4" fill="#2B2118" />
        <path d="M40 26h40v14c14 6 22 16 22 30v116a8 8 0 0 1-8 8H26a8 8 0 0 1-8-8V70c0-14 8-24 22-30z" fill="#fff" />
        <path d="M18 92h84v74H18z" fill="#B4442E" />
        <text x="60" y="124" textAnchor="middle" style={display} fontWeight="700" fontSize="12" fill="#fff">{labels.pesticide}</text>
        <rect x="34" y="136" width="52" height="4" rx="2" fill="#fff" opacity=".6" />
        <path d="M60 72c-8 0-13 6-13 12 7 0 13-5 13-12zm0 0c8 0 13 6 13 12-7 0-13-5-13-12z" fill="#B4442E" />
      </svg>
    );
  if (kind === "regulator")
    return (
      <svg className="pack" viewBox="0 0 150 200" aria-hidden="true">
        <path d="M96 10h24v18H96z" fill="#2B2118" />
        <path d="M20 40c0-8 6-14 14-14h52l14-6h26v20c6 4 10 10 10 18v122a8 8 0 0 1-8 8H28a8 8 0 0 1-8-8z" fill="#fff" />
        <path d="M34 40h30v18H34z" fill="none" stroke="#2B2118" strokeWidth="5" />
        <path d="M20 86h116v76H20z" fill="#D08A2A" />
        <text x="78" y="124" textAnchor="middle" style={display} fontWeight="700" fontSize="14" fill="#fff">{labels.regulator}</text>
        <rect x="50" y="136" width="56" height="4" rx="2" fill="#fff" opacity=".6" />
      </svg>
    );
  return (
    <svg className="pack" viewBox="0 0 150 200" aria-hidden="true">
      <path d="M14 18c20-8 102-8 122 0l-4 10 6 160c-30 8-96 8-126 0l6-160z" fill="#fff" />
      <path d="M12 74h126v80H12z" fill="#1F6B2E" />
      <text x="75" y="108" textAnchor="middle" style={display} fontWeight="800" fontSize="15" fill="#fff">NPK 20-20-20</text>
      <text x="75" y="132" textAnchor="middle" fontSize="12" fill="#fff" opacity=".85">{labels.fertilizer}</text>
      <path d="M75 38c-10 0-16 8-16 16 9 0 16-6 16-16zm0 0c10 0 16 8 16 16-9 0-16-6-16-16z" fill="#1F6B2E" />
      <path d="M14 18c20-8 102-8 122 0" stroke="#2B2118" strokeWidth="4" fill="none" />
    </svg>
  );
}
