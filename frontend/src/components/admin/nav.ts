import {
  BookIcon, BoxIcon, ChatIcon, FactoryIcon, FlaskIcon, HomeIcon, ImageIcon, InboxIcon, LeafIcon, MailIcon,
  PartnersIcon, SettingsIcon, SlidesIcon, StarIcon, TeamIcon, UserIcon,
} from "./icons";

/**
 * Dashboard sections, shown in the sidebar and the Ctrl+K palette.
 * `admin` entries only show for the admin role (the API refuses editors anyway).
 */
export const NAV = [
  { href: "/admin/", label: "لوحة المتابعة", color: "#4CAF2E", Icon: HomeIcon },
  { group: "المحتوى", href: "/admin/slider/", label: "السلايدر", color: "#F2C230", Icon: SlidesIcon },
  { href: "/admin/products/", label: "المنتجات", color: "#4CAF2E", Icon: BoxIcon },
  { href: "/admin/categories/", label: "الأقسام والأنواع", color: "#2E8FB0", Icon: LeafIcon },
  { href: "/admin/services/", label: "الخدمات", color: "#E0607E", Icon: BookIcon },
  { href: "/admin/advice/", label: "النصائح الفنية", color: "#1F6B2E", Icon: BookIcon },
  { href: "/admin/blog/", label: "المدونة", color: "#D08A2A", Icon: ChatIcon },
  { href: "/admin/gallery/", label: "المعرض", color: "#B4442E", Icon: ImageIcon },
  { group: "الشركة", href: "/admin/partners/", label: "الشركاء", color: "#F2C230", Icon: PartnersIcon },
  { href: "/admin/factories/", label: "المصانع", color: "#2E8FB0", Icon: FactoryIcon },
  { href: "/admin/team/", label: "فريق العمل", color: "#E0607E", Icon: TeamIcon },
  { href: "/admin/testimonials/", label: "آراء العملاء", color: "#D08A2A", Icon: StarIcon },
  { href: "/admin/why-us/", label: "لماذا نحن", color: "#4CAF2E", Icon: FlaskIcon },
  { group: "التواصل", href: "/admin/messages/", label: "الرسائل", color: "#E0607E", Icon: InboxIcon, badge: "messages" },
  { href: "/admin/newsletter/", label: "القائمة البريدية", color: "#2E8FB0", Icon: MailIcon },
  { group: "النظام", href: "/admin/users/", label: "المستخدمون", color: "#F2C230", Icon: UserIcon, admin: true },
  { href: "/admin/settings/", label: "إعدادات الموقع", color: "#93A58C", Icon: SettingsIcon, admin: true },
] as const;

/** Quick actions in the palette, on top of opening each section. `?new` opens the add form. */
export const ACTIONS = [
  { href: "/admin/products/edit/", label: "إضافة منتج", color: "#4CAF2E", Icon: BoxIcon },
  { href: "/admin/posts/edit/?section=advice", label: "كتابة نصيحة فنية", color: "#1F6B2E", Icon: BookIcon },
  { href: "/admin/posts/edit/?section=blog", label: "كتابة مقال للمدونة", color: "#D08A2A", Icon: ChatIcon },
  { href: "/admin/gallery/?new", label: "إضافة صورة للمعرض", color: "#B4442E", Icon: ImageIcon },
  { href: "/admin/slider/?new", label: "إضافة شريحة للسلايدر", color: "#F2C230", Icon: SlidesIcon },
  { href: "/admin/partners/?new", label: "إضافة شريك", color: "#F2C230", Icon: PartnersIcon },
  { href: "/admin/testimonials/?new", label: "إضافة رأي عميل", color: "#D08A2A", Icon: StarIcon },
  { href: "/admin/settings/#contact", label: "تعديل بيانات التواصل", color: "#93A58C", Icon: SettingsIcon },
] as const;
