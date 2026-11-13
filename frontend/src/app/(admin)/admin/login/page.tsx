import type { Metadata } from "next";
import LoginForm from "@/components/admin/LoginForm";
import Garden from "@/components/shared/Garden";

export const metadata: Metadata = { title: "تسجيل الدخول" };

export default function LoginPage() {
  return (
    <main className="login" id="main">
      <div className="art">
        <Garden />
        {/* eslint-disable-next-line @next/next/no-img-element -- static export serves images as-is */}
        <img src="/img/logo.webp" alt="أكتيف جروب" className="bg-white rounded-2xl p-2 h-14 w-auto self-start mb-10" />
        <h2 className="text-[clamp(26px,3vw,40px)] font-extrabold max-w-md">كل اللي في الموقع، تحت إيدك.</h2>
        <p className="text-[#CFE3C8] mt-2 max-w-md">المنتجات والمقالات والرسائل في مكان واحد. اضغط على الأرض وازرع زهرة وانت مستني.</p>
      </div>
      <LoginForm />
    </main>
  );
}
