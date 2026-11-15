/** Contact form types: label and colour, shared by the inbox and the overview. */
export const MESSAGE_TYPES: Record<string, [string, string]> = {
  quote: ["طلب عرض سعر", "#2E8FB0"],
  consult: ["استشارة فنية", "#E0607E"],
  complaint: ["شكوى أو اقتراح", "#D08A2A"],
  jobs: ["توظيف", "#1F6B2E"],
  other: ["أخرى", "#93A58C"],
};

export const messageType = (t: string) => MESSAGE_TYPES[t] ?? MESSAGE_TYPES.other;

/** First letter of a name for the round avatar. */
export const initial = (name: string) => Array.from(name.trim())[0] ?? "؟";

export type Message = {
  id: number; type: string; name: string; phone: string | null; email: string | null; governorate: string | null;
  body: string; status: "new" | "read" | "archived"; is_spam: boolean; reply: string | null; replied_at: string | null; created_at: string;
};
