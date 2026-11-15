"use client";

import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { useShell } from "@/components/admin/Shell";
import { api, ApiError, fieldErrors } from "./api";

export type Values = Record<string, unknown>;

/** Multipart body: booleans as 1/0, arrays as JSON text (the API decodes them), files as they are. */
const toForm = (values: Values, files: Record<string, File | null>) => {
  const body = new FormData();
  for (const [k, v] of Object.entries(values)) {
    if (v === undefined || k.endsWith("_url")) continue;
    body.append(k, typeof v === "boolean" ? (v ? "1" : "0") : Array.isArray(v) ? JSON.stringify(v) : v === null ? "" : String(v));
  }
  for (const [k, f] of Object.entries(files)) if (f) body.append(k, f);
  return body;
};

/**
 * A full-page editor for one record (`?id=` in the URL, none for a new one).
 * Only the keys present in `blank` are sent, so read-only fields from the API never go back.
 */
export function useRecord(resource: string, id: string | null, blank: Values, listPath: string) {
  const { toast } = useShell();
  const router = useRouter();
  const [values, setValues] = useState<Values | null>(id ? null : blank);
  const [record, setRecord] = useState<Values | null>(null);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [loadError, setLoadError] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);
  // Bumps when a record loads, so uncontrolled editors (rich text) start again from its HTML.
  const [version, setVersion] = useState(0);

  useEffect(() => {
    if (!id) return;
    api<{ data: Values }>(`/${resource}/${id}`).then(
      (r) => {
        setRecord(r.data);
        setValues(Object.fromEntries(Object.keys(blank).map((k) => [k, r.data[k] ?? blank[k]])));
        setVersion((n) => n + 1);
      },
      (e) => setLoadError(e instanceof ApiError ? e.message : "حصلت مشكلة."),
    );
    // blank is a constant per page
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [resource, id]);

  const set = (k: string, v: unknown) => setValues((s) => ({ ...s, [k]: v }));

  const save = async (files: Record<string, File | null> = {}, override: Values = {}) => {
    if (!values) return;
    const body = toForm({ ...values, ...override }, files);
    if (id) body.append("_method", "PUT");
    setSaving(true);
    setErrors({});
    try {
      const { data } = await api<{ data: Values }>(id ? `/${resource}/${id}` : `/${resource}`, "POST", body);
      setRecord(data);
      setValues((s) => ({ ...s, ...override, slug: data.slug ?? s?.slug }));
      toast(id ? "تم الحفظ. اضغط «نشر التعديلات» عشان يظهر في الموقع." : "اتضاف. اضغط «نشر التعديلات» عشان يظهر في الموقع.");
      if (!id) router.replace(`?id=${data.id}`);
      return data;
    } catch (e) {
      setErrors(fieldErrors(e));
      toast(e instanceof ApiError && e.status === 422 ? "فيه حقول محتاجة تتصلح، بصّ على الرسائل الحمرا." : e instanceof ApiError ? e.message : "حصلت مشكلة.");
    } finally {
      setSaving(false);
    }
  };

  const remove = async () => {
    if (!id || !confirm("متأكد إنك عايز تحذف؟ مش هتقدر ترجع فيها.")) return;
    try {
      await api(`/${resource}/${id}`, "DELETE");
      toast("تم الحذف");
      router.replace(listPath);
    } catch (e) {
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    }
  };

  return { values, record, set, errors, loadError, saving, save, remove, version };
}
