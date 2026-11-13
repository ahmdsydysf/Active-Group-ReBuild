"use client";

import { useRouter } from "next/navigation";
import { useEffect, useState, type FormEvent } from "react";
import { api, ApiError, session } from "@/lib/admin/api";
import { ShieldIcon } from "./icons";

export default function LoginForm() {
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    if (session.token()) router.replace("/admin/");
  }, [router]);

  const submit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const f = new FormData(e.currentTarget);
    setBusy(true);
    setError(null);
    try {
      const r = await api<{ token: string }>("/login", "POST", { email: f.get("email"), password: f.get("password") });
      session.save(r.token, f.get("remember") === "on");
      router.replace("/admin/");
    } catch (err) {
      setError(err instanceof ApiError && err.status === 422 ? "الإيميل أو كلمة المرور مش صح." : err instanceof ApiError ? err.message : "حصلت مشكلة.");
      setBusy(false);
    }
  };

  return (
    <form className="flex flex-col gap-4" onSubmit={submit}>
      <h1 className="text-3xl font-extrabold text-deep">أهلًا بيك تاني</h1>
      <p className="text-soil-2 -mt-2">سجّل دخولك عشان تكمل.</p>
      <label className="sfield">
        <span>البريد الإلكتروني</span>
        <input name="email" type="email" required autoComplete="username" dir="ltr" maxLength={120} aria-invalid={!!error || undefined} />
      </label>
      <label className="sfield">
        <span>كلمة المرور</span>
        <input name="password" type="password" required autoComplete="current-password" dir="ltr" maxLength={200} aria-invalid={!!error || undefined} />
      </label>
      <label className="flex items-center gap-2 text-[15px]">
        <input name="remember" type="checkbox" className="w-[18px] h-[18px] accent-field" />
        افتكرني على الجهاز ده
      </label>
      {error && <p className="ferr -mt-1" role="alert">{error}</p>}
      <button className="btn btn-wheat justify-center mt-2" type="submit" disabled={busy} aria-busy={busy}>
        {busy ? "بندخّلك…" : "تسجيل الدخول"}
      </button>
      <p className="text-sm text-soil-2 text-center mt-2">
        <ShieldIcon className="inline w-4 h-4 text-field" /> الدخول محمي، وبيتقفل بعد 5 محاولات غلط.
      </p>
    </form>
  );
}
