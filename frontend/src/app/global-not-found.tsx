import type { Metadata } from "next";
import Link from "next/link";
import Flowers, { type FlowerSpec } from "@/components/site/Flowers";
import { alexandria, plexArabic } from "@/lib/fonts";
import ar from "@/messages/ar";
import en from "@/messages/en";
import "@/styles/site.css";

export const metadata: Metadata = { title: "404 | أكتيف جروب", robots: { index: false } };

const FLOWERS: FlowerSpec[] = [
  { left: "6%", top: "14%", s: 110, pc: "#fff", cc: "#F2C230", d: -0.4 },
  { left: "18%", top: "66%", s: 70, pc: "#E0607E", cc: "#fff", d: -1.4 },
  { left: "80%", top: "12%", s: 90, pc: "#F2C230", cc: "#7A4A0C", d: -2.1 },
  { left: "90%", top: "60%", s: 120, pc: "#fff", cc: "#E0607E", d: -3.0 },
  { left: "60%", top: "80%", s: 60, pc: "#E0607E", cc: "#F2C230", d: -3.8 },
  { left: "36%", top: "-4%", s: 80, pc: "#F7D9A8", cc: "#B4442E", d: -4.1 },
  { left: "70%", top: "40%", s: 50, pc: "#fff", cc: "#2E8FB0", d: -4.8 },
];

/** One 404 page for every unknown URL (the host serves it as /404.html). Arabic first, with the English line under it. */
export default function GlobalNotFound() {
  return (
    <html lang="ar" dir="rtl" className={`${alexandria.variable} ${plexArabic.variable}`}>
      <body className="site">
        <main className="e404">
          <Flowers items={FLOWERS} opacity={0.35} />
          <div className="relative z-10 max-w-xl">
            <Link className="logo mb-10 inline-block" href="/ar/">
              {/* eslint-disable-next-line @next/next/no-img-element -- static export serves files as they are */}
              <img src="/img/logo.webp" alt={ar.brandHome} width={150} height={46} />
            </Link>
            <b>404</b>
            <h1 className="text-3xl font-extrabold mt-4">{ar.notFound.title}</h1>
            <p className="text-[#CFE3C8] mt-3 text-lg">{ar.notFound.text}</p>
            <div className="flex flex-wrap gap-3 justify-center mt-8">
              <Link className="btn btn-sun" href="/ar/">{ar.common.home}</Link>
              <Link className="btn btn-line" href="/ar/products/">{ar.notFound.products}</Link>
            </div>
            <p className="mt-10 text-[#CFE3C8]" lang="en" dir="ltr">
              {en.notFound.title}. <Link className="underline text-white" href="/en/">{en.common.home}</Link>
            </p>
          </div>
        </main>
      </body>
    </html>
  );
}
