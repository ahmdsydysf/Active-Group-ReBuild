"use client";

import { useEffect, useState } from "react";
import { api, ApiError, CHANGED } from "@/lib/admin/api";
import { SendIcon } from "./icons";

type Status = { configured: boolean; pending: boolean; published_at: string | null; changed_at: string | null };

/**
 * The site is static HTML, so dashboard edits go live only after a rebuild.
 * This button shows when there are unpublished changes and starts the rebuild.
 */
export default function PublishButton({ path, toast }: { path: string; toast: (m: string) => void }) {
  const [status, setStatus] = useState<Status | null>(null);
  const [busy, setBusy] = useState(false);

  // Re-check on every page change and after every save anywhere in the dashboard.
  useEffect(() => {
    const check = () => void api<{ data: Status }>("/publish").then((r) => setStatus(r.data), () => {});
    check();
    addEventListener(CHANGED, check);
    return () => removeEventListener(CHANGED, check);
  }, [path]);

  if (!status?.pending) return null;

  const publish = async () => {
    if (!confirm("هننشر كل التعديلات على الموقع. الموقع هيتحدث خلال دقايق. نكمل؟")) return;
    setBusy(true);
    try {
      const r = await api<{ data: Status }>("/publish", "POST");
      setStatus(r.data);
      toast("بدأ النشر. الموقع هيتحدث خلال دقايق.");
    } catch (e) {
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    } finally {
      setBusy(false);
    }
  };

  return (
    <button className="dbtn pri !py-2" type="button" onClick={publish} disabled={busy} title="فيه تعديلات لسه مش ظاهرة في الموقع">
      <SendIcon />
      <span className="max-sm:sr-only">{busy ? "بننشر…" : "نشر التعديلات"}</span>
    </button>
  );
}
