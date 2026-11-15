import { Alexandria, IBM_Plex_Sans_Arabic } from "next/font/google";

// Downloaded at build time and served from the site itself: no request to Google from visitors.
export const alexandria = Alexandria({
  subsets: ["arabic", "latin"],
  variable: "--font-alexandria",
  display: "swap",
});

export const plexArabic = IBM_Plex_Sans_Arabic({
  subsets: ["arabic", "latin"],
  weight: ["400", "500", "600", "700"],
  variable: "--font-plex-arabic",
  display: "swap",
});
