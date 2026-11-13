export type Field = {
  name: string;
  label: string;
  /** Label on the English tab, when it differs from the Arabic one. */
  labelEn?: string;
  type?: "text" | "url" | "email" | "tel" | "textarea" | "select" | "number" | "datetime-local";
  /** For `select`: [value, label] pairs. */
  options?: [string, string][];
  rows?: number;
  required?: boolean;
  ltr?: boolean;
  hint?: string;
  max?: number;
};

type Props = { field: Field; value: string; error?: string; onChange: (name: string, value: string) => void };

/** One labelled input or textarea in the design's `.fld` style, with its server error under it. */
export default function TextField({ field: f, value, error, onChange }: Props) {
  const common = {
    className: "mt-1.5",
    name: f.name,
    value,
    required: f.required,
    maxLength: f.max,
    dir: f.ltr ? "ltr" : undefined,
    "aria-invalid": !!error || undefined,
    "aria-describedby": error ? `${f.name}-err` : undefined,
    onChange: (e: { target: { value: string } }) => onChange(f.name, e.target.value),
  };
  return (
    <div className="fld">
      <label>
        {f.label}
        {f.required && <span className="text-rust" aria-hidden="true"> *</span>}
        {f.type === "textarea" ? (
          <textarea rows={f.rows ?? 3} {...common} />
        ) : f.type === "select" ? (
          <select {...common}>
            {(!f.required || !value) && <option value="">{f.required ? "اختار…" : "—"}</option>}
            {f.options?.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
          </select>
        ) : (
          <input type={f.type ?? "text"} {...common} />
        )}
      </label>
      {f.hint && <p className="hint-t">{f.hint}</p>}
      {error && <p className="ferr" id={`${f.name}-err`} role="alert">{error}</p>}
    </div>
  );
}
