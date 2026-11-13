"use client";

import { useEffect, useRef, useState } from "react";
import ContentManager from "@/components/admin/ContentManager";
import { useShell } from "@/components/admin/Shell";
import { SimpleCard } from "@/components/admin/cards";
import { UploadIcon } from "@/components/admin/icons";
import { api, ApiError } from "@/lib/admin/api";
import type { Row } from "@/lib/admin/types";

type Album = Row & { name_ar: string; photos_count: number };
type Photo = Row & { caption_ar: string | null; image_url: string; gallery_album_id: number | null };

const MAX_MB = 5;

/** Albums, photos and a button that uploads up to 20 photos at once. */
export default function GalleryPage() {
  const { toast } = useShell();
  const [albums, setAlbums] = useState<[string, string][]>([]);
  const [album, setAlbum] = useState("");
  const [version, setVersion] = useState(0);
  const [busy, setBusy] = useState(false);
  const input = useRef<HTMLInputElement>(null);

  useEffect(() => {
    api<{ data: Album[] }>("/gallery-albums").then((r) => setAlbums(r.data.map((a) => [String(a.id), `${a.name_ar} (${a.photos_count})`])), () => {});
  }, [version]);

  const upload = async (files: FileList | null, reload: () => void) => {
    const list = Array.from(files ?? []).filter((f) => f.size <= MAX_MB * 1024 * 1024).slice(0, 20);
    if (!list.length) return toast(`اختار صور لحد ${MAX_MB} ميجا للصورة.`);
    const body = new FormData();
    list.forEach((f) => body.append("images[]", f));
    if (album) body.append("gallery_album_id", album);
    setBusy(true);
    try {
      await api("/gallery-photos/upload", "POST", body);
      toast(`اترفعت ${list.length} صورة`);
      reload();
      setVersion((n) => n + 1);
    } catch (e) {
      toast(e instanceof ApiError ? e.message : "حصلت مشكلة.");
    } finally {
      setBusy(false);
    }
  };

  const albumName = (id: number | null) => albums.find(([v]) => v === String(id))?.[1]?.replace(/ \(\d+\)$/, "") ?? "بدون ألبوم";

  return (
    <>
      <ContentManager<Photo>
        key={album}
        resource="gallery-photos"
        query={album ? `album=${album}` : undefined}
        title="المعرض"
        intro="الصور بتتعرض في صفحة المعرض بالترتيب ده. تقدر ترفع لحد 20 صورة مرة واحدة."
        noun="الصورة"
        addLabel="صورة واحدة"
        listClass="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4"
        image={{ name: "image", label: "الصورة", hint: `مطلوبة. لحد ${MAX_MB} ميجا.` }}
        defaults={album ? { gallery_album_id: album } : undefined}
        onChange={() => setVersion((n) => n + 1)}
        toolbar={(reload) => (
          <>
            <select className="dinput dsel !w-auto" aria-label="الألبوم" value={album} onChange={(e) => setAlbum(e.target.value)}>
              <option value="">كل الألبومات</option>
              {albums.map(([v, l]) => <option key={v} value={v}>{l}</option>)}
            </select>
            <button className="dbtn" type="button" disabled={busy} onClick={() => input.current?.click()}>
              <UploadIcon />
              {busy ? "بنرفع…" : "رفع صور كتير"}
            </button>
            <input ref={input} type="file" accept="image/jpeg,image/png,image/webp" multiple className="sr-only" onChange={(e) => (upload(e.target.files, reload), (e.target.value = ""))} />
          </>
        )}
        fields={[{ name: "caption", label: "التعليق", labelEn: "Caption", max: 255 }]}
        shared={[{ name: "gallery_album_id", label: "الألبوم", type: "select", options: albums.map(([v, l]) => [v, l.replace(/ \(\d+\)$/, "")]) }]}
        card={(ph, c) => <SimpleCard key={ph.id} id={ph.id} c={c} image={ph.image_url} title={ph.caption_ar || "بدون تعليق"} sub={albumName(ph.gallery_album_id)} missingEn={ph.missing_en} />}
      />
      <ContentManager<Album>
        sub
        resource="gallery-albums"
        title="الألبومات"
        intro="أزرار التصفية فوق صور المعرض."
        noun="الألبوم"
        addLabel="ألبوم جديد"
        listClass="grid sm:grid-cols-2 xl:grid-cols-4 gap-4"
        onChange={() => setVersion((n) => n + 1)}
        fields={[{ name: "name", label: "اسم الألبوم", labelEn: "Name", required: true, max: 120 }]}
        card={(a, c) => <SimpleCard key={a.id} id={a.id} c={{ ...c, toggle: null }} title={a.name_ar} sub={`${a.photos_count} صورة`} missingEn={a.missing_en} />}
      />
    </>
  );
}
