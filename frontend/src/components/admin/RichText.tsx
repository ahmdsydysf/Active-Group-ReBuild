"use client";

import { useEffect, useRef } from "react";

type Tool = [cmd: string, label: string, text: React.ReactNode, value?: string];

const TOOLS: Tool[] = [
  ["bold", "عريض", "B"],
  ["italic", "مائل", <i key="i">I</i>],
  ["formatBlock", "عنوان رئيسي", "H2", "h2"],
  ["formatBlock", "عنوان فرعي", "H3", "h3"],
  ["formatBlock", "فقرة", "¶", "p"],
  ["insertUnorderedList", "قائمة", "•"],
  ["insertOrderedList", "قائمة مرقمة", "1."],
  ["formatBlock", "اقتباس", "❝", "blockquote"],
  ["createLink", "رابط", "🔗"],
  ["removeFormat", "إزالة التنسيق", "⌫"],
];

/**
 * A small editor for article and product bodies. The browser edits the HTML; the API cleans it
 * (HTMLPurifier) before saving, so pasted scripts or styles never reach the site.
 */
export default function RichText({ label, value, onChange, dir }: { label: string; value: string; onChange: (html: string) => void; dir?: "ltr" | "rtl" }) {
  const ref = useRef<HTMLDivElement>(null);

  // Set the starting HTML once per record; after that the editor owns its content.
  useEffect(() => {
    if (ref.current && ref.current.innerHTML !== value) ref.current.innerHTML = value;
    // eslint-disable-next-line react-hooks/exhaustive-deps -- only on mount; typing must not reset the caret
  }, []);

  const run = (cmd: string, v?: string) => {
    ref.current?.focus();
    if (cmd === "createLink") {
      const url = prompt("الرابط (يبدأ بـ https://)");
      if (!url || !/^(https?:\/\/|\/|mailto:)/.test(url)) return;
      v = url;
    }
    document.execCommand(cmd, false, v ? (cmd === "formatBlock" ? `<${v}>` : v) : undefined);
    onChange(ref.current?.innerHTML ?? "");
  };

  return (
    <div className="fld">
      <span className="block font-semibold text-sm mb-1.5">{label}</span>
      <div className="rte-bar" role="toolbar" aria-label="تنسيق">
        {TOOLS.map(([cmd, name, text, v]) => (
          <button key={name} type="button" aria-label={name} title={name} onMouseDown={(e) => e.preventDefault()} onClick={() => run(cmd, v)}>
            {text}
          </button>
        ))}
      </div>
      <div
        ref={ref}
        className="rte"
        contentEditable
        suppressContentEditableWarning
        role="textbox"
        aria-multiline="true"
        aria-label={label}
        dir={dir}
        onInput={(e) => onChange(e.currentTarget.innerHTML)}
        onPaste={(e) => {
          // Paste as plain text so Word and web styles don't come along.
          e.preventDefault();
          document.execCommand("insertText", false, e.clipboardData.getData("text/plain"));
        }}
      />
    </div>
  );
}
