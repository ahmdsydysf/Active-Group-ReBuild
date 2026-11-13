"use client";

import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useCallback, useEffect, useState, type DragEvent, type HTMLAttributes, type KeyboardEvent, type ReactNode } from "react";
import { api, ApiError, fieldErrors } from "@/lib/admin/api";
import type { Row } from "@/lib/admin/types";
import Drawer from "./Drawer";
import { useShell } from "./Shell";
import ImageField from "./ImageField";
import { EditIcon, GripIcon, PlusIcon, TrashIcon } from "./icons";
import LangTabs from "./LangTabs";
import Switch from "./Switch";
import TextField, { type Field } from "./TextField";

export type { Field };

type Controls = {
  /** Spread on the card root to make it draggable. */
  drag: HTMLAttributes<HTMLElement> & { draggable: boolean };
  grip: ReactNode;
  toggle: ReactNode;
  actions: ReactNode;
};

type Props<T extends Row> = {
  resource: string;
  /** Extra list query, e.g. "section=advice". */
  query?: string;
  /** Second section on a page: smaller heading. */
  sub?: boolean;
  /** Drag to reorder (off for lists ordered by date). */
  sortable?: boolean;
  /** Buttons next to "add", given a reload function. */
  toolbar?: (reload: () => void) => ReactNode;
  /** Called after a save or delete, for pages that show related data. */
  onChange?: () => void;
  title: string;
  intro: string;
  noun: string;
  addLabel: string;
  listClass: string;
  /** Translated fields: `name` is the base, the form edits `${name}_ar` and `${name}_en`. */
  fields: Field[];
  /** Fields with one value for both languages, shown under the tabs. */
  shared?: Field[];
  image?: { name: string; label: string; hint: string; contain?: boolean };
  /** Extra form controls (e.g. a colour picker) that edit `values` directly. */
  extra?: (values: Record<string, string>, set: (name: string, v: string) => void) => ReactNode;
  defaults?: Record<string, string>;
  card: (item: T, c: Controls) => ReactNode;
};

/**
 * One dashboard content section: list, drag to reorder, publish switch, add/edit drawer, delete.
 * Pages only describe their fields and how a card looks.
 */
export default function ContentManager<T extends Row>(p: Props<T>) {
  const { toast } = useShell();
  const router = useRouter();
  const pathname = usePathname();
  const search = useSearchParams();
  const [items, setItems] = useState<T[] | null>(null);
  const [loadError, setLoadError] = useState<string | null>(null);
  const [editing, setEditing] = useState<T | "new" | null>(null);
  const [values, setValues] = useState<Record<string, string>>({});
  const [file, setFile] = useState<File | null>(null);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [formKey, setFormKey] = useState(0);
  // Card being dragged, and the order before the drag started (to skip saving an unchanged order).
  const [dragId, setDragId] = useState<number | null>(null);
  const [orderBefore, setOrderBefore] = useState("");

  const load = useCallback(() => {
    api<{ data: T[] }>(`/${p.resource}${p.query ? `?${p.query}` : ""}`).then(
      (r) => setItems(r.data),
      (e: unknown) => setLoadError(e instanceof ApiError ? e.message : "حصلت مشكلة."),
    );
  }, [p.resource, p.query]);
  useEffect(load, [load]);

  const keys = [...p.fields.flatMap((f) => [`${f.name}_ar`, `${f.name}_en`]), ...(p.shared ?? []).map((f) => f.name), ...Object.keys(p.defaults ?? {})];

  const open = useCallback(
    (item: T | "new") => {
      const src = item === "new" ? (p.defaults ?? {}) : item;
      setValues(Object.fromEntries(keys.map((k) => [k, String(src[k] ?? "")])));
      setFile(null);
      setErrors({});
      setFormKey((k) => k + 1);
      setEditing(item);
    },
    // keys derive from props that never change for a page
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [p.resource],
  );
  const close = useCallback(() => setEditing(null), []);

  // "?new" (from the palette or the overview) opens an empty form once.
  useEffect(() => {
    if (search.has("new") && items) {
      // Syncs with the URL, an outside system: runs once per "?new" and removes it right away.
      // eslint-disable-next-line react-hooks/set-state-in-effect
      open("new");
      router.replace(pathname);
    }
  }, [search, items, open, router, pathname]);

  const set = (name: string, v: string) => setValues((s) => ({ ...s, [name]: v }));

  const save = async () => {
    if (!editing) return;
    const isNew = editing === "new";
    const body = new FormData();
    for (const [k, v] of Object.entries(values)) body.append(k, v);
    if (file && p.image) body.append(p.image.name, file);
    if (!isNew) body.append("_method", "PUT");
    setSaving(true);
    try {
      const { data } = await api<{ data: T }>(isNew ? `/${p.resource}` : `/${p.resource}/${editing.id}`, "POST", body);
      setItems((list) => (isNew ? [...(list ?? []), data] : (list ?? []).map((x) => (x.id === data.id ? data : x))));
      setEditing(null);
      p.onChange?.();
      toast(isNew ? `اتضاف ${p.noun}` : "تم الحفظ");
    } catch (e) {
      setErrors(fieldErrors(e));
      if (!(e instanceof ApiError) || e.status !== 422) toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    } finally {
      setSaving(false);
    }
  };

  const toggle = async (item: T, on: boolean) => {
    setItems((l) => l?.map((x) => (x.id === item.id ? { ...x, is_active: on } : x)) ?? null);
    try {
      await api(`/${p.resource}/${item.id}`, "PUT", { is_active: on });
      toast(on ? "اتفعّل" : "اتخفى من الموقع");
    } catch (e) {
      setItems((l) => l?.map((x) => (x.id === item.id ? { ...x, is_active: !on } : x)) ?? null);
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    }
  };

  const remove = async (item: T) => {
    if (!confirm("متأكد إنك عايز تحذف؟ مش هتقدر ترجع فيها.")) return;
    try {
      await api(`/${p.resource}/${item.id}`, "DELETE");
      setItems((l) => l?.filter((x) => x.id !== item.id) ?? null);
      p.onChange?.();
      toast("تم الحذف");
    } catch (e) {
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    }
  };

  const persistOrder = async (list: T[]) => {
    const ids = list.map((x) => x.id);
    if (ids.join() === orderBefore) return;
    setOrderBefore(ids.join());
    try {
      await api(`/${p.resource}/reorder`, "POST", { ids });
      toast("تم حفظ الترتيب الجديد");
    } catch (e) {
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
      load();
    }
  };

  const move = (id: number, to: number) =>
    setItems((l) => {
      if (!l) return l;
      const from = l.findIndex((x) => x.id === id);
      if (from < 0 || to < 0 || to >= l.length || from === to) return l;
      const next = [...l];
      next.splice(to, 0, ...next.splice(from, 1));
      return next;
    });

  const controls = (item: T, index: number): Controls => ({
    drag: p.sortable === false ? { draggable: false } : {
      draggable: true,
      onDragStart: (e: DragEvent) => {
        setDragId(item.id);
        setOrderBefore(items!.map((x) => x.id).join());
        e.dataTransfer.effectAllowed = "move";
        (e.currentTarget as HTMLElement).classList.add("dragging");
      },
      onDragOver: (e: DragEvent) => {
        if (dragId === null) return;
        e.preventDefault();
        if (dragId !== item.id) move(dragId, index);
      },
      onDragEnd: (e: DragEvent) => {
        (e.currentTarget as HTMLElement).classList.remove("dragging");
        setDragId(null);
        persistOrder(items!);
      },
    },
    grip: p.sortable === false ? null : (
      <button
        type="button"
        className="grip"
        aria-label={`ترتيب: ${index + 1} من ${items!.length}. استخدم الأسهم لأعلى ولأسفل`}
        onFocus={() => setOrderBefore(items!.map((x) => x.id).join())}
        onKeyDown={(e: KeyboardEvent) => {
          if (e.key !== "ArrowUp" && e.key !== "ArrowDown") return;
          e.preventDefault();
          move(item.id, index + (e.key === "ArrowUp" ? -1 : 1));
        }}
        onBlur={() => persistOrder(items!)}
      >
        <GripIcon />
      </button>
    ),
    toggle: <Switch on={item.is_active} onChange={(on) => toggle(item, on)} />,
    actions: (
      <div className="flex gap-1.5 justify-end">
        <button className="ebtn inline-flex items-center gap-1" type="button" onClick={() => open(item)}>
          <EditIcon className="w-4 h-4" />
          تعديل
        </button>
        <button className="ebtn !px-2 text-rust" type="button" aria-label="حذف" onClick={() => remove(item)}>
          <TrashIcon className="w-4 h-4" />
        </button>
      </div>
    ),
  });

  const lang = (l: "ar" | "en") =>
    p.fields.map((f) => (
      <TextField key={f.name} field={{ ...f, name: `${f.name}_${l}`, label: l === "en" ? f.labelEn ?? f.label : f.label, required: l === "ar" && f.required }} value={values[`${f.name}_${l}`] ?? ""} error={errors[`${f.name}_${l}`]} onChange={set} />
    ));

  const current = editing && editing !== "new" ? editing : null;
  const errorLang = (["ar", "en"] as const).find((l) => p.fields.some((f) => errors[`${f.name}_${l}`])) ?? null;
  const enMissing = p.fields.some((f) => values[`${f.name}_ar`] && !values[`${f.name}_en`]);

  return (
    <>
      <div className={`dhead${p.sub ? " mt-10" : ""}`}>
        <div>
          {p.sub ? <h2 className="text-2xl font-extrabold">{p.title}</h2> : <h1>{p.title}</h1>}
          <p>{p.intro}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          {p.toolbar?.(load)}
          <button className="dbtn pri" type="button" onClick={() => open("new")}>
            <PlusIcon />
            {p.addLabel}
          </button>
        </div>
      </div>

      {loadError ? (
        <p className="empty" role="alert">{loadError}</p>
      ) : !items ? (
        <p className="empty" aria-busy="true">بنحمّل…</p>
      ) : items.length === 0 ? (
        <p className="empty">لسه مفيش حاجة هنا. ابدأ بـ «{p.addLabel}».</p>
      ) : (
        <div className={`sortable ${p.listClass}`}>{items.map((item, i) => p.card(item, controls(item, i)))}</div>
      )}

      <Drawer open={editing !== null} title={current ? `تعديل ${p.noun}` : p.addLabel} saving={saving} onClose={close} onSubmit={save}>
        {p.image && (
          <ImageField label={p.image.label} hint={p.image.hint} contain={p.image.contain} current={current ? String(current[`${p.image.name}_url`] ?? "") : null} file={file} onFile={setFile} error={errors[p.image.name]} />
        )}
        <LangTabs key={formKey} ar={lang("ar")} en={lang("en")} enMissing={enMissing} invalid={errorLang} />
        {p.shared?.map((f) => <TextField key={f.name} field={f} value={values[f.name] ?? ""} error={errors[f.name]} onChange={set} />)}
        {p.extra?.(values, set)}
      </Drawer>
    </>
  );
}
