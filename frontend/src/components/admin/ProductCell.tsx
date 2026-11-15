import Pack, { packShape } from "@/components/site/Packs";

/** Product name cell for dashboard tables: photo or pack drawing, Arabic name, English name or a warning. */
export default function ProductCell({ name, nameEn, image, color, packs, label }: { name: string; nameEn: string | null; image: string | null; color: string; packs: string | null; label: string }) {
  return (
    <div className="nm">
      <span className="t">
        {image ? (
          // eslint-disable-next-line @next/next/no-img-element -- API image
          <img src={image} alt="" className="h-[34px] w-auto object-contain" />
        ) : (
          <Pack shape={packShape(color, packs)} color={color} label={label} style={{ height: 34, width: "auto" }} />
        )}
      </span>
      <div>
        <b><bdi>{name}</bdi></b>
        {nameEn ? <small><bdi>{nameEn}</bdi></small> : <small className="warn">ينقصه الاسم الإنجليزي</small>}
      </div>
    </div>
  );
}
