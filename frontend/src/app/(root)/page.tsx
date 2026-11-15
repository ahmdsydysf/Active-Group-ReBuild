import type { Metadata } from "next";
import Link from "next/link";

export const metadata: Metadata = { title: "Active Group", robots: { index: false } };

/** Static hosting cannot redirect from code, so "/" ships a tiny page that forwards to /ar/. The .htaccess does it with a 301 on the server. */
export default function RootPage() {
  return (
    <>
      <meta httpEquiv="refresh" content="0;url=/ar/" />
      <p style={{ padding: 24 }}>
        <Link href="/ar/">أكتيف جروب</Link> · <Link href="/en/">Active Group</Link>
      </p>
    </>
  );
}
