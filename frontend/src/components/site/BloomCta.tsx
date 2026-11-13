import type { Messages } from "@/messages/ar";
import Flowers, { type FlowerSpec } from "./Flowers";
import { WhatsAppIcon } from "./icons";

// Positions and colours of the opening flowers, as in the design.
const FLOWERS: FlowerSpec[] = [
  { left: "8%", top: "20%", s: 110, pc: "#fff", cc: "#F2C230", d: -0.24 },
  { left: "22%", top: "62%", s: 70, pc: "#E0607E", cc: "#fff", d: -1.34 },
  { left: "78%", top: "10%", s: 90, pc: "#F2C230", cc: "#7A4A0C", d: -1.97 },
  { left: "90%", top: "58%", s: 130, pc: "#fff", cc: "#E0607E", d: -3.0 },
  { left: "55%", top: "78%", s: 60, pc: "#E0607E", cc: "#F2C230", d: -3.83 },
  { left: "40%", top: "-6%", s: 80, pc: "#F7D9A8", cc: "#B4442E", d: -4.07 },
  { left: "66%", top: "40%", s: 50, pc: "#fff", cc: "#2E8FB0", d: -4.81 },
];

export default function BloomCta({ t, whatsapp }: { t: Messages["cta"]; whatsapp: string | null }) {
  return (
    <section className="band bloomcta" aria-labelledby="ctaH">
      <Flowers items={FLOWERS} opacity={0.55} />
      <div className="wrap">
        <div>
          <h2 id="ctaH">{t.title}</h2>
          <p>{t.text}</p>
        </div>
        {whatsapp && (
          <a className="btn" href={whatsapp} target="_blank" rel="noopener noreferrer">
            <WhatsAppIcon />
            {t.button}
          </a>
        )}
      </div>
    </section>
  );
}
