"use client";

import { useCallback, useEffect, useState } from "react";
import { DPager } from "@/components/admin/ListParts";
import { useShell } from "@/components/admin/Shell";
import { ArchiveIcon, DownloadIcon, SendIcon, TrashIcon } from "@/components/admin/icons";
import { api, ApiError, CHANGED, download } from "@/lib/admin/api";
import { ago, dateTime } from "@/lib/admin/format";
import { initial, messageType, type Message } from "@/lib/admin/messages";
import type { Meta } from "@/lib/admin/useList";

type Counts = { inbox: number; new: number; archived: number; spam: number };
type Res = { data: Message[]; meta: Meta; counts: Counts };

const TABS = [
  { key: "inbox", label: "الكل", q: "box=inbox" },
  { key: "new", label: "جديدة", q: "box=new" },
  { key: "quote", label: "عروض أسعار", q: "box=inbox&type=quote" },
  { key: "archived", label: "الأرشيف", q: "box=archived" },
  { key: "spam", label: "سبام", q: "box=spam" },
] as const;
const PER = 30;

const whatsapp = (phone: string) => `https://wa.me/${phone.replace(/\D/g, "").replace(/^0/, "20")}`;

export default function MessagesPage() {
  const { toast } = useShell();
  const [tab, setTab] = useState<(typeof TABS)[number]["key"]>("inbox");
  const [page, setPage] = useState(1);
  const [res, setRes] = useState<Res | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [open, setOpen] = useState<Message | null>(null);
  const [reply, setReply] = useState("");
  const [sending, setSending] = useState(false);
  const q = TABS.find((t) => t.key === tab)!.q;

  const load = useCallback(() => {
    api<Res>(`/messages?${q}&page=${page}`).then((r) => (setRes(r), setError(null)), (e) => setError(e instanceof ApiError ? e.message : "حصلت مشكلة."));
  }, [q, page]);
  useEffect(load, [load]);

  const fail = (e: unknown) => toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");

  const show = async (m: Message) => {
    setOpen(m);
    setReply("");
    if (m.status !== "new") return;
    try {
      const r = await api<{ data: Message }>(`/messages/${m.id}`);
      setOpen(r.data);
      setRes((s) => s && { ...s, data: s.data.map((x) => (x.id === m.id ? r.data : x)), counts: { ...s.counts, new: Math.max(0, s.counts.new - 1) } });
      dispatchEvent(new Event(CHANGED));
    } catch (e) {
      fail(e);
    }
  };

  const change = async (m: Message, data: Partial<Message>, done: string) => {
    try {
      await api(`/messages/${m.id}`, "PUT", data);
      toast(done);
      setOpen(null);
      load();
    } catch (e) {
      fail(e);
    }
  };

  const remove = async (m: Message) => {
    if (!confirm("متأكد إنك عايز تحذف الرسالة؟ مش هتقدر ترجع فيها.")) return;
    try {
      await api(`/messages/${m.id}`, "DELETE");
      toast("تم الحذف");
      setOpen(null);
      load();
    } catch (e) {
      fail(e);
    }
  };

  const send = async () => {
    if (!open || !reply.trim()) return;
    setSending(true);
    try {
      const r = await api<{ data: Message }>(`/messages/${open.id}/reply`, "POST", { body: reply });
      setOpen(r.data);
      setReply("");
      toast("اتبعت الرد");
    } catch (e) {
      fail(e);
    } finally {
      setSending(false);
    }
  };

  const counts = res?.counts;
  const box = q.match(/box=(\w+)/)![1];

  return (
    <>
      <div className="dhead">
        <div>
          <h1>الرسائل</h1>
          <p>{counts ? `${counts.new} رسالة جديدة. الرسائل اللي شكلها سبام بتروح لتبويب «سبام» لوحدها.` : "رسائل نموذج التواصل في الموقع."}</p>
        </div>
        <div className="flex flex-wrap gap-2">
          <button className="dbtn" type="button" onClick={() => download(`/messages/export?box=${box}`, "messages.csv").catch(fail)}>
            <DownloadIcon />
            تصدير
          </button>
        </div>
      </div>
      <div className="ibox">
        <section className={`tile inbox !p-3 list${open ? " max-lg:hidden" : ""}`}>
          <div className="vtabs !mb-2" role="tablist">
            {TABS.map((t) => {
              const n = t.key === "new" || t.key === "spam" ? counts?.[t.key] : undefined;
              return (
                <button key={t.key} type="button" role="tab" aria-selected={tab === t.key} onClick={() => { setTab(t.key); setPage(1); setOpen(null); }}>
                  {t.label}{n ? ` (${n})` : ""}
                </button>
              );
            })}
          </div>
          {error ? (
            <p className="empty" role="alert">{error}</p>
          ) : !res ? (
            <p className="empty" aria-busy="true">بنحمّل…</p>
          ) : !res.data.length ? (
            <p className="empty">مفيش رسائل هنا.</p>
          ) : (
            res.data.map((m) => (
              <button key={m.id} type="button" className={`m w-full text-start${open?.id === m.id ? " sel" : ""}`} style={{ "--c": messageType(m.type)[1] } as React.CSSProperties} onClick={() => show(m)}>
                <i aria-hidden="true">{initial(m.name)}</i>
                <div className="min-w-0">
                  <b>{m.name}{m.status === "new" && <span className="new" aria-label="جديدة" />}</b>
                  <p><span className="pill">{messageType(m.type)[0]}</span>{m.body}</p>
                </div>
                <small>{ago(m.created_at)}</small>
              </button>
            ))
          )}
          <DPager meta={res?.meta} per={PER} onPage={setPage} />
        </section>

        <section className={`tile mailv${open ? "" : " max-lg:hidden"}`} aria-live="polite">
          {!open ? (
            <p className="empty">اختار رسالة من القائمة عشان تقراها.</p>
          ) : (
            <>
              <div className="flex flex-wrap gap-2 justify-between items-start">
                <div>
                  <button type="button" className="ebtn lg:hidden mb-2" onClick={() => setOpen(null)}>رجوع للرسائل</button>
                  <span className="pill block w-fit" style={{ "--c": messageType(open.type)[1] } as React.CSSProperties}>{messageType(open.type)[0]}</span>
                  <h2 className="mt-2">رسالة من {open.name}</h2>
                </div>
                <div className="flex flex-wrap gap-2">
                  {open.status === "archived" || open.is_spam ? (
                    <button className="dbtn" type="button" onClick={() => change(open, { status: "read", is_spam: false }, "رجعت للوارد")}>رجّعها للوارد</button>
                  ) : (
                    <>
                      <button className="dbtn" type="button" onClick={() => change(open, { status: "archived" }, "اتنقلت للأرشيف")}><ArchiveIcon />أرشفة</button>
                      <button className="dbtn" type="button" onClick={() => change(open, { is_spam: true }, "اتنقلت للسبام")}>سبام</button>
                    </>
                  )}
                  <button className="dbtn danger" type="button" onClick={() => remove(open)}><TrashIcon />حذف</button>
                </div>
              </div>
              <div className="from">
                <i className="w-11 h-11 rounded-2xl grid place-items-center not-italic text-white font-display font-extrabold" style={{ background: messageType(open.type)[1] }} aria-hidden="true">{initial(open.name)}</i>
                <div className="flex-1 min-w-0">
                  <b className="block">{open.name}</b>
                  <small className="text-d-mute">
                    {[open.phone && <a key="p" dir="ltr" href={`tel:${open.phone}`}>{open.phone}</a>, open.email && <a key="e" href={`mailto:${open.email}`}>{open.email}</a>, open.governorate]
                      .filter(Boolean)
                      .flatMap((x, i) => (i ? [" · ", x] : [x]))}
                  </small>
                </div>
                <small className="text-d-mute">{dateTime(open.created_at)}</small>
              </div>
              <div className="leading-loose whitespace-pre-line">{open.body}</div>

              {open.reply && (
                <div className="mt-6 rounded-xl p-4 bg-[color-mix(in_srgb,var(--leaf)_10%,transparent)]">
                  <b className="block text-sm mb-1">ردّك{open.replied_at ? ` · ${dateTime(open.replied_at)}` : ""}</b>
                  <div className="whitespace-pre-line">{open.reply}</div>
                </div>
              )}

              <div className="mt-6 border-t border-d-line pt-4">
                <label className="font-semibold text-sm block mb-2" htmlFor="reply">{open.reply ? "رد تاني" : "الرد"}</label>
                <textarea id="reply" className="dinput min-h-[120px]" maxLength={5000} disabled={!open.email} placeholder={open.email ? "اكتب ردك هنا، وهيتبعت على إيميل العميل" : "الرسالة دي من غير إيميل. كلّم العميل على الموبايل أو واتساب."} value={reply} onChange={(e) => setReply(e.target.value)} />
                <div className="flex flex-wrap gap-2 mt-3">
                  {open.email && (
                    <button className="dbtn pri" type="button" disabled={sending || !reply.trim()} onClick={send}>
                      <SendIcon />
                      {sending ? "بنبعت…" : "إرسال الرد"}
                    </button>
                  )}
                  {open.phone && <a className="dbtn" href={whatsapp(open.phone)} target="_blank" rel="noopener">رد على واتساب</a>}
                </div>
              </div>
            </>
          )}
        </section>
      </div>
    </>
  );
}
