"use client";

import { useState } from "react";
import type { Locale } from "@/lib/i18n";
import type { Messages } from "@/messages/ar";
import { CheckIcon } from "./icons";

type Errors = Partial<Record<"name" | "phone" | "email" | "body", string>>;

/** The contact form. Sends to the API; the server checks everything again and drops bot submissions. */
export default function ContactForm({ locale, t }: { locale: Locale; t: Messages["contact"] }) {
  const [state, setState] = useState<"idle" | "sending" | "ok" | "error">("idle");
  const [error, setError] = useState("");
  const [errors, setErrors] = useState<Errors>({});

  const submit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const form = e.currentTarget;
    if (!form.reportValidity()) return;
    const data = Object.fromEntries(new FormData(form));
    setState("sending");
    setErrors({});
    try {
      const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/v1/contact`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ ...data, locale }),
      });
      if (res.ok) {
        form.reset();
        setState("ok");
        return;
      }
      if (res.status === 422) {
        const body = (await res.json()) as { errors?: Record<string, string[]> };
        setErrors(Object.fromEntries(Object.entries(body.errors ?? {}).map(([k, v]) => [k, v[0]])));
      }
      setError(res.status === 429 ? t.tooMany : t.failed);
      setState("error");
    } catch {
      setError(t.failed);
      setState("error");
    }
  };

  const field = (name: keyof Errors) => ({ name, "aria-invalid": errors[name] ? true : undefined, "aria-describedby": errors[name] ? `${name}-err` : undefined });
  const err = (name: keyof Errors) => errors[name] && <small id={`${name}-err`} className="text-rust font-semibold">{errors[name]}</small>;

  return (
    <form id="quote" className="rounded-[32px] border border-line p-6 sm:p-8 grid sm:grid-cols-2 gap-5 scroll-mt-28" noValidate onSubmit={submit}>
      <h2 className="sm:col-span-2 text-3xl font-extrabold text-deep">{t.formTitle}</h2>
      <fieldset className="sm:col-span-2">
        <legend className="font-semibold mb-2">{t.type}</legend>
        <div className="flex flex-wrap gap-2">
          {Object.entries(t.types).map(([value, label], i) => (
            <label key={value} className="fchip cursor-pointer has-[:checked]:bg-field has-[:checked]:text-white has-[:focus-visible]:outline-2" style={{ "--c": "var(--field)" } as React.CSSProperties}>
              <input type="radio" name="type" value={value} className="sr-only" defaultChecked={i === 0} />
              {label}
            </label>
          ))}
        </div>
      </fieldset>
      <label className="sfield">
        <span>{t.name}</span>
        <input {...field("name")} required maxLength={120} autoComplete="name" />
        {err("name")}
      </label>
      <label className="sfield">
        <span>{t.phone}</span>
        <input {...field("phone")} type="tel" required maxLength={20} pattern="\+?[0-9 ]{7,20}" autoComplete="tel" dir="ltr" placeholder="01xxxxxxxxx" />
        {err("phone")}
      </label>
      <label className="sfield">
        <span>
          {t.email} <small className="text-soil-2">{t.optional}</small>
        </span>
        <input {...field("email")} type="email" maxLength={120} autoComplete="email" dir="ltr" />
        {err("email")}
      </label>
      <label className="sfield">
        <span>{t.governorate}</span>
        <select name="governorate" defaultValue={t.governorates[0]}>
          {t.governorates.map((g) => <option key={g}>{g}</option>)}
        </select>
      </label>
      <label className="sfield sm:col-span-2">
        <span>{t.message}</span>
        <textarea {...field("body")} required minLength={5} maxLength={3000} placeholder={t.messagePlaceholder} />
        {err("body")}
      </label>
      {/* Left empty by people; bots fill it. */}
      <input type="text" name="website" tabIndex={-1} autoComplete="off" className="hidden" aria-hidden="true" />
      <div className="sm:col-span-2 flex flex-wrap items-center gap-4">
        <button className="btn btn-wheat" type="submit" disabled={state === "sending"}>
          {state === "sending" ? t.sending : t.send}
        </button>
        <small className="text-soil-2">{t.privacy}</small>
      </div>
      <div className="sm:col-span-2" role="status" aria-live="polite">
        {state === "ok" && (
          <p className="rounded-2xl bg-sage p-4 text-deep font-semibold">
            <CheckIcon className="inline w-5 h-5 text-field" /> {t.ok}
          </p>
        )}
        {state === "error" && <p className="rounded-2xl bg-[#F7DCD4] p-4 text-[#5A1E12] font-semibold">{error}</p>}
      </div>
    </form>
  );
}
