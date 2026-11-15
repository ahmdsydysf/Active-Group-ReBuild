/** Phone numbers are stored as typed in the dashboard (01097003465 or +20 109...). */
const digits = (phone: string) => phone.replace(/[^\d+]/g, "");

/** Egyptian local numbers start with 0; links need the +20 country code. */
const international = (phone: string) => {
  const d = digits(phone);
  if (d.startsWith("+")) return d.slice(1);
  if (d.startsWith("00")) return d.slice(2);
  if (d.startsWith("0")) return `20${d.slice(1)}`;
  return d;
};

export const telHref = (phone: string) => `tel:+${international(phone)}`;

export const whatsappHref = (phone: string | null | undefined) => (phone ? `https://wa.me/${international(phone)}` : null);
