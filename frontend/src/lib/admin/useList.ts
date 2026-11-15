"use client";

import { useCallback, useEffect, useState } from "react";
import { useShell } from "@/components/admin/Shell";
import { api, ApiError } from "./api";
import type { Row } from "./types";

export type Meta = { total: number; page: number; last_page: number };
type Page<T> = { data: T[]; meta: Meta } & Record<string, unknown>;

const message = (e: unknown) => (e instanceof ApiError ? e.message : "حصلت مشكلة.");

/** Wait until typing stops before searching. */
export function useDebounced<T>(value: T, ms = 300): T {
  const [v, setV] = useState(value);
  useEffect(() => {
    const t = setTimeout(() => setV(value), ms);
    return () => clearTimeout(t);
  }, [value, ms]);
  return v;
}

/**
 * A paged dashboard table: loads `/{resource}` with the given filters, and handles selection,
 * bulk actions, the publish switch and delete. Changing a filter goes back to page 1.
 */
export function useList<T extends Row>(resource: string, filters: Record<string, string>, perPage = 20) {
  const { toast } = useShell();
  const [page, setPage] = useState(1);
  const [res, setRes] = useState<Page<T> | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [selected, setSelected] = useState<number[]>([]);
  const query = new URLSearchParams(Object.entries(filters).filter(([, v]) => v !== "")).toString();
  const [lastQuery, setLastQuery] = useState(query);
  if (query !== lastQuery) {
    setLastQuery(query);
    setPage(1);
  }

  const reload = useCallback(() => {
    api<Page<T>>(`/${resource}?${query}&page=${page}&per_page=${perPage}`).then(
      (r) => {
        setRes(r);
        setError(null);
        setSelected([]);
      },
      (e) => setError(message(e)),
    );
  }, [resource, query, page, perPage]);
  useEffect(reload, [reload]);

  const items = res?.data ?? null;
  const patch = (id: number, data: Partial<T>) => setRes((r) => r && { ...r, data: r.data.map((x) => (x.id === id ? { ...x, ...data } : x)) });

  return {
    res,
    items,
    error,
    page,
    setPage,
    reload,
    patch,
    selected,
    toggleRow: (id: number) => setSelected((s) => (s.includes(id) ? s.filter((x) => x !== id) : [...s, id])),
    allChecked: !!items?.length && selected.length === items.length,
    toggleAll: () => setSelected((s) => (items && s.length < items.length ? items.map((x) => x.id) : [])),
    async bulk(action: "publish" | "hide" | "delete") {
      if (action === "delete" && !confirm(`هتحذف ${selected.length} عنصر. متأكد؟ مش هتقدر ترجع فيها.`)) return;
      try {
        await api(`/${resource}/bulk`, "POST", { ids: selected, action });
        toast(action === "delete" ? "تم الحذف" : action === "publish" ? "اتنشروا" : "اتخفوا من الموقع");
        reload();
      } catch (e) {
        toast(message(e));
      }
    },
    async toggle(item: T, on: boolean) {
      patch(item.id, { is_active: on } as Partial<T>);
      try {
        await api(`/${resource}/${item.id}`, "PUT", { is_active: on });
        toast(on ? "اتنشر" : "اتخفى من الموقع");
      } catch (e) {
        patch(item.id, { is_active: !on } as Partial<T>);
        toast(message(e));
      }
    },
    async remove(item: T) {
      if (!confirm("متأكد إنك عايز تحذف؟ مش هتقدر ترجع فيها.")) return;
      try {
        await api(`/${resource}/${item.id}`, "DELETE");
        toast("تم الحذف");
        reload();
      } catch (e) {
        toast(message(e));
      }
    },
  };
}
