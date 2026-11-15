"use client";

import ContentManager from "@/components/admin/ContentManager";
import { SimpleCard } from "@/components/admin/cards";
import type { Row } from "@/lib/admin/types";

type Member = Row & { name_ar: string; role_ar: string | null; photo_url: string };

export default function TeamPage() {
  return (
    <ContentManager<Member>
      resource="team"
      title="فريق العمل"
      intro="بيظهروا في آخر صفحة من نحن. تقدر كمان تختار أي حد منهم ككاتب للمقالات."
      noun="العضو"
      addLabel="عضو جديد"
      listClass="grid grid-cols-2 lg:grid-cols-4 gap-4"
      image={{ name: "photo", label: "الصورة الشخصية", hint: "مطلوبة. صورة مربعة والوش في النص." }}
      fields={[
        { name: "name", label: "الاسم", labelEn: "Name", required: true, max: 120 },
        { name: "role", label: "الوظيفة", labelEn: "Role", max: 120 },
      ]}
      card={(m, c) => <SimpleCard key={m.id} id={m.id} c={c} image={m.photo_url} round title={m.name_ar} sub={m.role_ar} missingEn={m.missing_en} />}
    />
  );
}
