import type { Metadata, Viewport } from "next";
import { alexandria, plexArabic } from "@/lib/fonts";
import "@/styles/admin.css";

export const metadata: Metadata = {
  title: { default: "لوحة التحكم | أكتيف جروب", template: "%s | لوحة التحكم" },
  robots: { index: false, follow: false },
  icons: { icon: [{ url: "/favicon.ico", sizes: "any" }, { url: "/img/favicon-32.png", type: "image/png", sizes: "32x32" }] },
};

export const viewport: Viewport = { themeColor: "#123F1C", width: "device-width", initialScale: 1, viewportFit: "cover" };

/** The dashboard is Arabic only and fully client-side: every page talks to the API with the signed-in token. */
export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ar" dir="rtl" className={`${alexandria.variable} ${plexArabic.variable}`}>
      <body>{children}</body>
    </html>
  );
}
