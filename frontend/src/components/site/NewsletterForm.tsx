"use client";

import { useState } from "react";
import type { Locale } from "@/lib/i18n";
import type { Messages } from "@/messages/ar";

/** Footer newsletter: posts straight to the API from the browser. */
export default function NewsletterForm({ locale, t }: { locale: Locale; t: Pick<Messages["footer"], "emailLabel" | "emailPlaceholder" | "subscribe" | "subscribed" | "failed"> }) {
  const [state, setState] = useState<"idle" | "busy" | "ok" | "error">("idle");

  async function submit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const form = e.currentTarget;
    const email = new FormData(form).get("email");
    setState("busy");
    try {
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/v1/newsletter`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ email, locale }),
      });
      if (!res.ok) throw new Error(String(res.status));
      form.reset();
      setState("ok");
    } catch {
      setState("error");
    }
  }

  return (
    <form className="news-form" onSubmit={submit}>
      <label className="sr-only" htmlFor="nlf">
        {t.emailLabel}
      </label>
      <input id="nlf" name="email" type="email" required maxLength={120} placeholder={t.emailPlaceholder} autoComplete="email" />
      <button className="btn btn-sun" type="submit" disabled={state === "busy"}>
        {t.subscribe}
      </button>
      <span className="news-ok" role="status" hidden={state !== "ok" && state !== "error"}>
        {state === "error" ? t.failed : t.subscribed}
      </span>
    </form>
  );
}
