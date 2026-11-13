"use client";

import { useState, type ReactNode } from "react";

type Lang = "ar" | "en";

/**
 * العربية / English tabs around the translated fields of a form. Both panes stay mounted so values survive
 * switching. When the server rejects a field, the tab that holds it opens.
 */
export default function LangTabs({ ar, en, enMissing, invalid }: { ar: ReactNode; en: ReactNode; enMissing?: boolean; invalid?: Lang | null }) {
  const [tab, setTab] = useState<Lang>("ar");
  const [shownInvalid, setShownInvalid] = useState(invalid);
  if (invalid !== shownInvalid) {
    setShownInvalid(invalid);
    if (invalid) setTab(invalid);
  }
  return (
    <>
      <div className="tabs" role="tablist">
        {(["ar", "en"] as const).map((l) => (
          <button key={l} type="button" role="tab" aria-selected={tab === l} onClick={() => setTab(l)}>
            {l === "ar" ? "العربية" : "English"}
            {l === "en" && enMissing && <span className="pill ms-2" style={{ "--c": "var(--rust)" } as React.CSSProperties}>ناقص</span>}
          </button>
        ))}
      </div>
      <div role="tabpanel" hidden={tab !== "ar"}>{ar}</div>
      <div role="tabpanel" hidden={tab !== "en"} dir="ltr" lang="en">{en}</div>
    </>
  );
}
