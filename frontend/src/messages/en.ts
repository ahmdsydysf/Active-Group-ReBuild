import type { Messages } from "./ar";

/** English interface copy. Must have every key the Arabic file has. */
const en: Messages = {
  skip: "Skip to content",
  brandHome: "Active Group, home page",
  nav: {
    label: "Main menu",
    mobileLabel: "Mobile menu",
    open: "Open menu",
    close: "Close menu",
    items: [
      { href: "", label: "Home" },
      { href: "about/", label: "About us" },
      { href: "products/", label: "Products" },
      { href: "services/", label: "Technical services" },
      { href: "experiences/", label: "Experiences" },
      { href: "gallery/", label: "Gallery" },
      { href: "blog/", label: "Blog" },
      { href: "contact/", label: "Contact us" },
    ],
    quote: "Request a quote",
    otherLang: "عربي",
  },
  hero: {
    label: "Introduction",
    secondary: "Book a technical consultation",
    hint: "Tap the soil below to plant a flower",
  },
  families: {
    title: "Three product families",
    lead: "Pick the family that fits your crop’s problem. Every product has a technical leaflet with doses and spray timings.",
    all: "All products",
    count: (n: number) => `${n} ${n === 1 ? "product" : "products"}`,
    packs: { pesticide: "Fungicide", fertilizer: "Active Group", regulator: "Regulator" },
  },
  journey: {
    kicker: "The plant’s journey with Active",
    title: "The right product at every stage",
    stages: [
      {
        step: "Stage 1 of 5 · Before planting",
        title: "Seed and soil",
        text: "Prepare and sterilize the soil, and add the organic and phosphate fertilizers the seed needs for a strong start.",
        recs: [["Superphosphate", "#1F6B2E"], ["Soil disinfectant", "#B4442E"]],
      },
      {
        step: "Stage 2 of 5 · First 3 weeks",
        title: "Roots",
        text: "Root boosters and amino acids build a strong root system, and a nematicide protects the roots from day one.",
        recs: [["Humic 80%", "#1F6B2E"], ["Nemato-Stop", "#B4442E"]],
      },
      {
        step: "Stage 3 of 5 · Leaves and stems",
        title: "Vegetative growth",
        text: "Balanced nitrogen and micronutrients, with preventive sprays against mildew and aphids.",
        recs: [["NPK 20-20-20", "#1F6B2E"], ["Chelated Zinc 12%", "#2E8FB0"], ["Acti-Mix 72%", "#B4442E"]],
      },
      {
        step: "Stage 4 of 5 · The key stage of the season",
        title: "Flowering and fruit set",
        text: "Boron, calcium and growth regulators raise fruit set and reduce flower drop.",
        recs: [["Boron 20%", "#E0607E"], ["Gibberellin 10%", "#D08A2A"]],
      },
      {
        step: "Stage 5 of 5 · Size and colour",
        title: "Fruit and harvest",
        text: "High potassium for size, colour and taste, keeping to the pre-harvest interval.",
        recs: [["Potassium 50%", "#D08A2A"], ["NPK 10-10-40", "#1F6B2E"]],
      },
    ],
  },
  numbers: {
    title: "Our results in numbers",
    lead: "From plant to field, numbers we’re proud of after years of work with Egyptian farmers.",
  },
  advice: {
    title: "Technical advice from our engineers",
    lead: "Farming advice from our engineers, sorted by crop and season.",
    all: "All advice",
    read: (n: number) => `${n} min read`,
  },
  partners: {
    title: "Our partners",
    lead: "We work with international and local companies in pesticides and fertilizers.",
    all: "All partners",
  },
  voices: { title: "What farmers say", more: "More reviews" },
  cta: {
    title: "Need a fertilization program for your farm?",
    text: "Tell us your crop and area, and one of our engineers will call you within one working day.",
    button: "Chat on WhatsApp",
  },
  footer: {
    newsTitle: "Join the newsletter",
    newsText: "One farming tip a week in your inbox. No spam.",
    emailLabel: "Email",
    emailPlaceholder: "Your email",
    subscribe: "Subscribe",
    subscribed: "You’re subscribed. Your first tip arrives this week.",
    failed: "We couldn’t sign you up just now. Please try again in a moment.",
    products: "Products",
    leaflets: "Technical leaflets",
    company: "Company",
    companyLinks: [
      { href: "about/", label: "About us" },
      { href: "partners/", label: "Partners" },
      { href: "gallery/", label: "Gallery" },
      { href: "blog/", label: "Blog" },
    ],
    contact: "Contact us",
    rights: (year: number, name: string) => `© ${year} ${name}. All rights reserved.`,
    privacy: "Privacy policy",
    social: { facebook: "Facebook", youtube: "YouTube", linkedin: "LinkedIn", instagram: "Instagram" },
  },
  sprout: {
    title: "Contact us",
    whatsapp: "WhatsApp",
    call: "Call us",
    quote: "Request a quote",
    hint: "Need advice? Our engineers can help",
  },
};

export default en;
