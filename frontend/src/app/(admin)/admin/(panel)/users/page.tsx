"use client";

import { useCallback, useEffect, useState } from "react";
import Drawer from "@/components/admin/Drawer";
import { TableState } from "@/components/admin/ListParts";
import { useShell } from "@/components/admin/Shell";
import Switch from "@/components/admin/Switch";
import TextField from "@/components/admin/TextField";
import { EditIcon, PlusIcon, SendIcon, TrashIcon } from "@/components/admin/icons";
import { api, ApiError, fieldErrors } from "@/lib/admin/api";
import { ago } from "@/lib/admin/format";

type User = { id: number; name: string; email: string; role: "admin" | "editor"; is_active: boolean; last_login_at: string | null };

const ROLES: [string, string][] = [["editor", "محرر: يعدّل المحتوى والرسائل"], ["admin", "مدير النظام: كمان المستخدمين والإعدادات"]];
const ROLE_LABEL = { admin: ["مدير النظام", "var(--bloom)"], editor: ["محرر", "var(--sky)"] } as const;

export default function UsersPage() {
  const { toast, user: me } = useShell();
  const [users, setUsers] = useState<User[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [editing, setEditing] = useState<User | "new" | null>(null);
  const [values, setValues] = useState({ name: "", email: "", role: "editor" });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);

  const load = useCallback(() => {
    api<{ data: User[] }>("/users").then((r) => setUsers(r.data), (e) => setError(e instanceof ApiError ? e.message : "حصلت مشكلة."));
  }, []);
  useEffect(load, [load]);

  const fail = (e: unknown) => toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
  const open = (u: User | "new") => {
    setValues(u === "new" ? { name: "", email: "", role: "editor" } : { name: u.name, email: u.email, role: u.role });
    setErrors({});
    setEditing(u);
  };

  const save = async () => {
    setSaving(true);
    try {
      if (editing === "new") {
        const r = await api<{ invite_sent: boolean }>("/users", "POST", values);
        toast(r.invite_sent ? "اتبعتت الدعوة على الإيميل" : "اتضاف، بس الإيميل متبعتش. راجع إعدادات البريد وابعت الدعوة تاني.");
      } else if (editing) {
        await api(`/users/${editing.id}`, "PUT", { name: values.name, role: values.role });
        toast("تم الحفظ");
      }
      setEditing(null);
      load();
    } catch (e) {
      setErrors(fieldErrors(e));
      if (!(e instanceof ApiError) || e.status !== 422) fail(e);
    } finally {
      setSaving(false);
    }
  };

  const update = (u: User, data: Partial<User>, done: string) => api(`/users/${u.id}`, "PUT", data).then(() => (toast(done), load()), fail);
  const invite = (u: User) => api(`/users/${u.id}/invite`, "POST").then(() => toast(`اتبعت رابط تعيين كلمة السر لـ ${u.email}`), fail);
  const remove = (u: User) => confirm(`هتحذف ${u.name} وهيخرج من لوحة التحكم. متأكد؟`) && api(`/users/${u.id}`, "DELETE").then(() => (toast("تم الحذف"), load()), fail);
  const set = (k: string, v: string) => setValues((s) => ({ ...s, [k]: v }));

  return (
    <>
      <div className="dhead">
        <div>
          <h1>المستخدمون</h1>
          <p>الناس اللي ليهم دخول على لوحة التحكم. المستخدم الجديد بيوصله إيميل يختار منه كلمة السر.</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <button className="dbtn pri" type="button" onClick={() => open("new")}>
            <PlusIcon />
            دعوة مستخدم
          </button>
        </div>
      </div>
      <section className="tile">
        <div className="overflow-x-auto">
          <table className="ptable">
            <thead>
              <tr>
                <th>المستخدم</th>
                <th>الدور</th>
                <th className="hide-s">آخر دخول</th>
                <th>مفعّل</th>
                <th><span className="sr-only">إجراءات</span></th>
              </tr>
            </thead>
            <tbody>
              <TableState items={users} error={error} cols={5} empty="مفيش مستخدمين." />
              {users?.map((u) => {
                const self = u.id === me.id;
                return (
                  <tr key={u.id}>
                    <td>
                      <div className="nm">
                        <i className="w-10 h-10 rounded-xl grid place-items-center not-italic text-white font-display font-bold flex-none" style={{ background: "linear-gradient(135deg,var(--sun),var(--bloom))" }} aria-hidden="true">{Array.from(u.name)[0]}</i>
                        <div>
                          <b>{u.name}{self && <span className="text-d-mute font-normal"> (إنت)</span>}</b>
                          <small dir="ltr" className="block text-d-mute text-end">{u.email}</small>
                        </div>
                      </div>
                    </td>
                    <td><span className="stat" style={{ "--c": ROLE_LABEL[u.role][1] } as React.CSSProperties}>{ROLE_LABEL[u.role][0]}</span></td>
                    <td className="hide-s text-d-mute">{u.last_login_at ? ago(u.last_login_at) : "لسه مدخلش"}</td>
                    <td>{self ? <span className="text-d-mute text-sm">—</span> : <Switch on={u.is_active} label="مفعّل" onChange={(on) => update(u, { is_active: on }, on ? "اتفعّل" : "اتوقف دخوله")} />}</td>
                    <td>
                      <div className="flex gap-1.5 justify-end">
                        {!u.last_login_at && (
                          <button className="ebtn inline-flex items-center gap-1" type="button" onClick={() => invite(u)}>
                            <SendIcon className="w-4 h-4" />
                            ابعت الدعوة تاني
                          </button>
                        )}
                        <button className="ebtn inline-flex items-center gap-1" type="button" onClick={() => open(u)}>
                          <EditIcon className="w-4 h-4" />
                          تعديل
                        </button>
                        {!self && (
                          <button className="ebtn !px-2 text-rust" type="button" aria-label={`حذف ${u.name}`} onClick={() => remove(u)}>
                            <TrashIcon className="w-4 h-4" />
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </section>

      <Drawer open={editing !== null} title={editing === "new" ? "دعوة مستخدم" : "تعديل المستخدم"} saving={saving} onClose={() => setEditing(null)} onSubmit={save}>
        <TextField field={{ name: "name", label: "الاسم", required: true, max: 120 }} value={values.name} error={errors.name} onChange={set} />
        {editing === "new" ? (
          <TextField field={{ name: "email", label: "الإيميل", type: "email", required: true, ltr: true, max: 120, hint: "هيوصله رابط يختار منه كلمة السر، صالح 3 أيام." }} value={values.email} error={errors.email} onChange={set} />
        ) : (
          <p className="hint-t mb-4" dir="ltr">{values.email}</p>
        )}
        {editing !== "new" && editing?.id === me.id ? (
          <p className="hint-t">مينفعش تغيّر دورك بنفسك.</p>
        ) : (
          <TextField field={{ name: "role", label: "الدور", type: "select", required: true, options: ROLES }} value={values.role} error={errors.role} onChange={set} />
        )}
      </Drawer>
    </>
  );
}
