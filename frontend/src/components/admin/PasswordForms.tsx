"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useState, type FormEvent } from "react";
import { api, ApiError, fieldErrors, LOGIN_PATH } from "@/lib/admin/api";

/**
 * "Forgot password" asks for a link by email; "reset" sets the password from that link.
 * Invites for new users land on the reset form too.
 */
export default function PasswordForms({ mode }: { mode: "forgot" | "reset" }) {
  const params = useSearchParams();
  const [busy, setBusy] = useState(false);
  const [done, setDone] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const token = params.get("token") ?? "";
  const email = params.get("email") ?? "";

  const submit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const f = Object.fromEntries(new FormData(e.currentTarget));
    setBusy(true);
    setErrors({});
    try {
      await api(mode === "forgot" ? "/forgot-password" : "/reset-password", "POST", mode === "forgot" ? f : { ...f, token, email });
      setDone(true);
    } catch (err) {
      const fields = fieldErrors(err);
      setErrors(Object.keys(fields).length ? fields : { form: err instanceof ApiError ? err.message : "حصلت مشكلة." });
    } finally {
      setBusy(false);
    }
  };

  const back = <Link className="text-field underline text-sm text-center" href={LOGIN_PATH}>رجوع لتسجيل الدخول</Link>;

  if (done)
    return (
      <div className="flex flex-col gap-4" role="status">
        <h1 className="text-3xl font-extrabold text-deep">{mode === "forgot" ? "بص على الإيميل" : "تمام، كلمة السر اتغيرت"}</h1>
        <p className="text-soil-2">
          {mode === "forgot" ? "لو الإيميل ده عنده حساب، هيوصله رابط تختار منه كلمة سر جديدة. لو موصلش، بص في الـ Spam." : "سجّل دخولك دلوقتي بكلمة السر الجديدة."}
        </p>
        {back}
      </div>
    );

  if (mode === "reset" && (!token || !email))
    return (
      <div className="flex flex-col gap-4">
        <h1 className="text-3xl font-extrabold text-deep">الرابط ناقص</h1>
        <p className="text-soil-2">افتح الرابط من الإيميل زي ما هو، أو اطلب رابط جديد.</p>
        <Link className="btn btn-wheat justify-center" href="/admin/forgot-password/">اطلب رابط جديد</Link>
      </div>
    );

  const err = (k: string) => errors[k] && <p className="ferr -mt-2" role="alert">{errors[k]}</p>;

  return (
    <form className="flex flex-col gap-4" onSubmit={submit}>
      <h1 className="text-3xl font-extrabold text-deep">{mode === "forgot" ? "نسيت كلمة السر؟" : "اختار كلمة السر"}</h1>
      <p className="text-soil-2 -mt-2">
        {mode === "forgot" ? "اكتب إيميلك وهنبعتلك رابط تختار منه كلمة سر جديدة." : <>للحساب <bdi dir="ltr">{email}</bdi>. 10 حروف على الأقل، فيها حروف وأرقام.</>}
      </p>
      {mode === "forgot" ? (
        <label className="sfield">
          <span>البريد الإلكتروني</span>
          <input name="email" type="email" required autoComplete="username" dir="ltr" maxLength={120} />
        </label>
      ) : (
        <>
          <label className="sfield">
            <span>كلمة السر الجديدة</span>
            <input name="password" type="password" required minLength={10} maxLength={200} autoComplete="new-password" dir="ltr" aria-invalid={!!errors.password || undefined} />
          </label>
          {err("password")}
          <label className="sfield">
            <span>اكتبها تاني</span>
            <input name="password_confirmation" type="password" required minLength={10} maxLength={200} autoComplete="new-password" dir="ltr" />
          </label>
        </>
      )}
      {err("email")}
      {err("form")}
      <button className="btn btn-wheat justify-center mt-2" type="submit" disabled={busy} aria-busy={busy}>
        {busy ? "لحظة…" : mode === "forgot" ? "ابعت الرابط" : "حفظ كلمة السر"}
      </button>
      {back}
    </form>
  );
}
