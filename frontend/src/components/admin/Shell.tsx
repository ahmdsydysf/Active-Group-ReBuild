"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { createContext, Suspense, useCallback, useContext, useEffect, useRef, useState, type ReactNode } from "react";
import { api, CHANGED, LOGIN_PATH, session } from "@/lib/admin/api";
import type { AdminUser } from "@/lib/admin/types";
import { BellIcon, BurstIcon, GlobeIcon, LogoutIcon, MenuIcon, MoonIcon, SearchIcon, SunIcon } from "./icons";
import { ACTIONS, NAV } from "./nav";
import Palette from "./Palette";
import PublishButton from "./PublishButton";

type Ctx = { user: AdminUser; toast: (message: string) => void };
const ShellContext = createContext<Ctx | null>(null);

export const useShell = () => {
  const ctx = useContext(ShellContext);
  if (!ctx) throw new Error("useShell must be used inside <Shell>");
  return ctx;
};

const ROLES: Record<string, string> = { admin: "مدير النظام", editor: "محرر" };
const DARK_KEY = "ag-dark";
const trim = (p: string) => p.replace(/\/+$/, "") || "/";

/**
 * The dashboard frame: sidebar, top bar, Ctrl+K palette, toast and dark mode.
 * Nothing renders until the token is checked against /me, so a signed-out visitor only sees a redirect.
 */
export default function Shell({ children }: { children: ReactNode }) {
  const router = useRouter();
  const path = trim(usePathname());
  const [user, setUser] = useState<AdminUser | null>(null);
  const [dark, setDark] = useState(() => {
    try {
      return localStorage.getItem(DARK_KEY) === "1";
    } catch {
      return false;
    }
  });
  const [menu, setMenu] = useState(false);
  const [palette, setPalette] = useState(false);
  const [toastMsg, setToastMsg] = useState<string | null>(null);
  const toastTimer = useRef<ReturnType<typeof setTimeout>>(undefined);
  const [fresh, setFresh] = useState(0);

  useEffect(() => {
    if (!session.token()) return router.replace(LOGIN_PATH);
    api<{ data: AdminUser }>("/me").then((r) => setUser(r.data), () => {});
  }, [router]);

  const refreshBadges = useCallback(() => {
    api<{ counts: { new: number } }>("/messages?box=new&per_page=1").then((r) => setFresh(r.counts.new), () => {});
  }, []);
  // Badges follow the page: each navigation re-checks new messages and pending changes.
  useEffect(refreshBadges, [refreshBadges, path]);
  useEffect(() => {
    addEventListener(CHANGED, refreshBadges);
    return () => removeEventListener(CHANGED, refreshBadges);
  }, [refreshBadges]);

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        setPalette((o) => !o);
      }
      if (e.key === "Escape") {
        setPalette(false);
        setMenu(false);
      }
    };
    addEventListener("keydown", onKey);
    return () => removeEventListener("keydown", onKey);
  }, []);

  const toast = useCallback((message: string) => {
    setToastMsg(message);
    clearTimeout(toastTimer.current);
    toastTimer.current = setTimeout(() => setToastMsg(null), 2200);
  }, []);

  const toggleDark = () => {
    setDark((d) => {
      try {
        localStorage.setItem(DARK_KEY, d ? "0" : "1");
      } catch {}
      return !d;
    });
  };

  const logout = async () => {
    await api("/logout", "POST").catch(() => {});
    session.clear();
    router.replace(LOGIN_PATH);
  };

  if (!user) return <div className="min-h-screen" style={{ background: "#F3F6F1" }} aria-busy="true" />;

  return (
    <ShellContext.Provider value={{ user, toast }}>
      <div id="dashboard" className={[dark && "dark", menu && "menu"].filter(Boolean).join(" ")}>
        <aside className="side" aria-label="قائمة لوحة التحكم">
          <Link className="lg" href="/admin/">
            {/* eslint-disable-next-line @next/next/no-img-element -- static export serves images as-is */}
            <img src="/img/logo.webp" alt="أكتيف جروب" height={40} />
          </Link>
          {NAV.filter((n) => !("admin" in n) || user.role === "admin").map((n) => {
            const on = trim(n.href) === path;
            return (
              <div key={n.href} className="contents">
                {"group" in n && <div className="grp">{n.group}</div>}
                <Link href={n.href} onClick={() => setMenu(false)} className={on ? "on" : ""} style={{ "--c": n.color } as React.CSSProperties} aria-current={on ? "page" : undefined}>
                  <span className="ic"><n.Icon /></span>
                  {n.label}
                  {"badge" in n && fresh > 0 && <span className="n">{fresh}</span>}
                </Link>
              </div>
            );
          })}
          <button type="button" onClick={logout} className="side-out mt-auto" style={{ "--c": "#93A58C" } as React.CSSProperties}>
            <span className="ic"><LogoutIcon /></span>
            تسجيل الخروج
          </button>
        </aside>

        <main className="dmain" id="main">
          <div className="dtop">
            <button className="iconbtn side-toggle" type="button" aria-label="فتح القائمة" aria-expanded={menu} onClick={() => setMenu(true)}>
              <MenuIcon />
            </button>
            <button className="dsearch" type="button" onClick={() => setPalette(true)}>
              <SearchIcon className="w-[18px]" />
              ابحث أو نفّذ أمر…<kbd>Ctrl K</kbd>
            </button>
            <div className="sp" />
            <PublishButton path={path} toast={toast} />
            <a className="iconbtn" href="/ar/" target="_blank" rel="noopener" aria-label="عرض الموقع" title="عرض الموقع">
              <GlobeIcon />
            </a>
            <button className="iconbtn" type="button" aria-label="تبديل الوضع الليلي" aria-pressed={dark} onClick={toggleDark}>
              {dark ? <SunIcon /> : <MoonIcon />}
            </button>
            <Link className="iconbtn" href="/admin/messages/" aria-label={fresh ? `الرسائل، ${fresh} جديدة` : "الرسائل"}>
              <BellIcon className="w-5" />
              {fresh > 0 && <span className="badge">{fresh > 9 ? "9+" : fresh}</span>}
            </Link>
            <div className="me">
              <i aria-hidden="true">{Array.from(user.name)[0]}</i>
              <div>
                <b>{user.name}</b>
                <small>{ROLES[user.role] ?? user.role}</small>
              </div>
            </div>
          </div>
          <Suspense>{children}</Suspense>
        </main>

        <div className="menu-ov" onClick={() => setMenu(false)} />
        <Palette open={palette} onClose={() => setPalette(false)} items={[...ACTIONS, ...NAV.slice(1).filter((n) => !("admin" in n) || user.role === "admin")]} />
        <div className={`toast${toastMsg ? " show" : ""}`} role="status">
          <BurstIcon />
          <span>{toastMsg}</span>
        </div>
      </div>
    </ShellContext.Provider>
  );
}
