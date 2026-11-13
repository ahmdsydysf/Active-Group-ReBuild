import type { ReactNode } from "react";
import Garden from "@/components/shared/Garden";

/** Login, forgot and reset password pages: the garden on one side, the form on the other. */
export default function AuthLayout({ children }: { children: ReactNode }) {
  return (
    <main className="login" id="main">
      <div className="art">
        <Garden />
        {/* eslint-disable-next-line @next/next/no-img-element -- static export serves images as-is */}
        <img src="/img/logo.webp" alt="أكتيف جروب" className="bg-white rounded-2xl p-2 h-14 w-auto self-start mb-10" />
        <h2 className="text-[clamp(26px,3vw,40px)] font-extrabold max-w-md">كل اللي في الموقع، تحت إيدك.</h2>
        <p className="text-[#CFE3C8] mt-2 max-w-md">المنتجات والمقالات والرسائل في مكان واحد. اضغط على الأرض وازرع زهرة وانت مستني.</p>
      </div>
      {children}
    </main>
  );
}
