import { HomeIcon, PartnersIcon, SettingsIcon, SlidesIcon, StarIcon } from "./icons";

/** Dashboard sections. Each new site page adds its section here, and it shows in the sidebar and the Ctrl+K palette. */
export const NAV = [
  { href: "/admin/", label: "لوحة المتابعة", color: "#4CAF2E", Icon: HomeIcon },
  { group: "المحتوى", href: "/admin/slider/", label: "السلايدر", color: "#F2C230", Icon: SlidesIcon },
  { group: "الشركة", href: "/admin/partners/", label: "الشركاء", color: "#F2C230", Icon: PartnersIcon },
  { href: "/admin/testimonials/", label: "آراء العملاء", color: "#D08A2A", Icon: StarIcon },
  { group: "النظام", href: "/admin/settings/", label: "إعدادات الموقع", color: "#93A58C", Icon: SettingsIcon },
] as const;

/** Quick actions in the palette, on top of opening each section. `?new` opens the add drawer. */
export const ACTIONS = [
  { href: "/admin/slider/?new", label: "إضافة شريحة للسلايدر", color: "#F2C230", Icon: SlidesIcon },
  { href: "/admin/partners/?new", label: "إضافة شريك", color: "#F2C230", Icon: PartnersIcon },
  { href: "/admin/testimonials/?new", label: "إضافة رأي عميل", color: "#D08A2A", Icon: StarIcon },
  { href: "/admin/settings/#contact", label: "تعديل بيانات التواصل", color: "#93A58C", Icon: SettingsIcon },
] as const;
