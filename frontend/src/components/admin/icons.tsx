import type { ReactNode } from "react";

/** Line icons from the dashboard design, drawn on a 24px grid. */
const line = (d: ReactNode, width = 1.8) => {
  const Icon = ({ className }: { className?: string }) => (
    <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={width}>
      {d}
    </svg>
  );
  return Icon;
};

export const HomeIcon = line(<><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></>);
export const SlidesIcon = line(<><rect x="2" y="5" width="20" height="14" rx="2" /><path d="m9 9 5 3-5 3z" /></>);
export const PartnersIcon = line(<path d="m2 12 5-5 5 3 5-3 5 5-6 6-4-3-4 3z" />);
export const StarIcon = line(<path d="m12 3 2.8 5.8 6.2.9-4.5 4.4 1 6.2L12 17.4 6.5 20.3l1-6.2L3 9.7l6.2-.9z" />);
export const SettingsIcon = line(<><circle cx="12" cy="12" r="3" /><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1" /></>);
export const LogoutIcon = line(<path d="M9 4H5v16h4M15 8l4 4-4 4M19 12H9" />);
export const MenuIcon = line(<path d="M4 7h16M4 12h16M4 17h16" />, 2);
export const SearchIcon = line(<><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></>);
export const GlobeIcon = line(<><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3c3 3.5 3 14.5 0 18M12 3c-3 3.5-3 14.5 0 18" /></>);
export const MoonIcon = line(<path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5z" />);
export const SunIcon = line(<><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5 19 19M5 19l1.5-1.5M17.5 6.5 19 5" /></>);
export const PlusIcon = line(<path d="M12 5v14M5 12h14" />, 2);
export const EditIcon = line(<><path d="M4 20h4L19 9l-4-4L4 16z" /><path d="m14 6 4 4" /></>);
export const TrashIcon = line(<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" />);
export const CloseIcon = line(<path d="M6 6l12 12M18 6 6 18" />, 2);
export const UploadIcon = line(<path d="M12 16V4M7 9l5-5 5 5M4 20h16" />);
export const ShieldIcon = line(<><path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z" /><path d="m9 12 2 2 4-4" /></>);

export const GripIcon = ({ className }: { className?: string }) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
    <circle cx="9" cy="6" r="1.6" /><circle cx="15" cy="6" r="1.6" /><circle cx="9" cy="12" r="1.6" /><circle cx="15" cy="12" r="1.6" /><circle cx="9" cy="18" r="1.6" /><circle cx="15" cy="18" r="1.6" />
  </svg>
);

export const BurstIcon = () => (
  <svg className="burst" viewBox="0 0 24 24" aria-hidden="true">
    <circle cx="12" cy="12" r="5" fill="#F2C230" />
    <g stroke="#E0607E" strokeWidth="2" strokeLinecap="round"><path d="M12 1v4M12 19v4M1 12h4M19 12h4M4 4l3 3M17 17l3 3M4 20l3-3M17 7l3-3" /></g>
  </svg>
);
export const CheckIcon = line(<path d="m5 12 5 5 9-10" />, 2.2);
