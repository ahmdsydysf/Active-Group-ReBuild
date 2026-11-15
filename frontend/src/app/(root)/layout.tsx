/** Root layout for "/" only: it sends visitors to the Arabic home page. */
export default function RootRedirectLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ar" dir="rtl">
      <body>{children}</body>
    </html>
  );
}
