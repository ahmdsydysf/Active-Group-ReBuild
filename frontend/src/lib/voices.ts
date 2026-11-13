/** Testimonial card colours (Testimonial::COLORS on the API), shared by the home stack and the about slider. */
export const VOICE_COLORS: Record<string, string> = { field: "var(--field)", rust: "var(--rust)", sky: "var(--sky)", plum: "#7A2140", deep: "var(--deep)", wheat: "var(--wheat)", bloom: "var(--bloom)" };

/** First letter of a name for the avatar, skipping titles like "م." or "Eng.". */
export const initial = (name: string) => Array.from(name.replace(/^(م|أ|د|Eng|Mr|Mrs|Dr|Hajj|الحاج)\.?\s+/u, ""))[0];
