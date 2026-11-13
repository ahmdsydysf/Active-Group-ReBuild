/** Inline SVG icons from the design kit. Decorative: always aria-hidden. */
type P = { className?: string };

export const WhatsAppIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
    <path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-3.9-4.7-4.1-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.5.1.6-.1l.9-1.1c.2-.3.4-.2.6-.1l2 1c.3.1.5.2.5.3.1.2.1.7-.1 1.3z" />
  </svg>
);

export const PhoneIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
    <path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2" />
  </svg>
);

export const MailIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
    <rect x="3" y="5" width="18" height="14" rx="2" />
    <path d="m3 7 9 6 9-6" />
  </svg>
);

export const PinIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
    <path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z" />
    <circle cx="12" cy="9" r="2.5" />
  </svg>
);

export const BoxIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
    <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9z" />
    <path d="m4 7.5 8 4.5 8-4.5M12 12v9" />
  </svg>
);

export const MenuIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
    <path d="M4 7h16M4 12h16M4 17h16" />
  </svg>
);

export const CloseIcon = ({ className }: P) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
    <path d="M6 6l12 12M18 6 6 18" />
  </svg>
);

/** Points to the reading direction's "forward" (left in Arabic, right in English). */
export const ForwardArrow = ({ className, rtl }: P & { rtl: boolean }) => (
  <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
    <path d={rtl ? "M19 12H5M11 6l-6 6 6 6" : "M5 12h14M13 6l6 6-6 6"} />
  </svg>
);

export const socialIcons = {
  facebook: (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
      <path d="M14 8h3V4h-3c-2.8 0-4 1.8-4 4.3V10H7v4h3v8h4v-8h3l1-4h-4V8.6c0-.4.3-.6.6-.6z" />
    </svg>
  ),
  youtube: (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
      <path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12 31 31 0 0 0 2 15.8a3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8zM10 15V9l5.2 3z" />
    </svg>
  ),
  linkedin: (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
      <path d="M4.5 3a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM3 8.5h3V21H3zM9 8.5h2.9v1.7c.4-.8 1.4-1.9 3.4-1.9 3.2 0 3.7 2.1 3.7 4.8V21h-3v-6.9c0-1.4 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z" />
    </svg>
  ),
  instagram: (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
      <rect x="3" y="3" width="18" height="18" rx="5" />
      <circle cx="12" cy="12" r="4" />
      <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
    </svg>
  ),
};

const line = (paths: React.ReactNode, width = "1.8") =>
  function Icon({ className }: P) {
    return (
      <svg className={className} aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={width}>
        {paths}
      </svg>
    );
  };

export const ClockIcon = line(<><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></>);
export const CalendarIcon = line(<><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M3 10h18M8 3v4M16 3v4" /></>);
export const DownloadIcon = line(<path d="M12 3v12M7 10l5 5 5-5M4 19h16" />);
export const FactoryIcon = line(<path d="M3 21V10l6 4V10l6 4V5h6v16z" />);
export const CheckIcon = line(<path d="m5 12 5 5 9-10" />, "2.2");
export const SearchIcon = line(<><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></>);
export const LinkIcon = line(<><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1" /><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1" /></>);
/** Chevron toward the visual left or right, independent of reading direction. */
export const ChevronLeft = line(<path d="m15 6-6 6 6 6" />, "2");
export const ChevronRight = line(<path d="m9 6 6 6-6 6" />, "2");

/** Service and "why us" icons; the keys match the API's `icon` values. */
const shield = <><path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z" /><path d="m9 12 2 2 4-4" /></>;
const people = <><circle cx="9" cy="8" r="3.2" /><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6" /><circle cx="17" cy="9" r="2.4" /><path d="M16 14c3 0 5 2 5 5" /></>;
const lab = <path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3" />;
const truck = <><path d="M2 6h12v10H2zM14 9h4l4 4v3h-8" /><circle cx="6" cy="18" r="2" /><circle cx="18" cy="18" r="2" /></>;
export const topicIcons: Record<string, (p: P) => React.ReactElement> = {
  shield: line(shield),
  people: line(people),
  visit: line(people),
  lab: line(lab),
  truck: line(truck),
  leaf: line(<><path d="M5 19c0-8 5-13 14-14 0 9-5 14-13 14" /><path d="M5 19c3-4 6-7 10-9" /></>),
  training: line(<path d="M4 4h7a2 2 0 0 1 2 2v14a2 2 0 0 0-2-2H4zM20 4h-5a2 2 0 0 0-2 2v14a2 2 0 0 1 2-2h5z" />),
};
export const ShieldIcon = topicIcons.shield;
