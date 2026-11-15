import type { Metadata } from "next";
import { Suspense } from "react";
import AuthLayout from "@/components/admin/AuthLayout";
import PasswordForms from "@/components/admin/PasswordForms";

export const metadata: Metadata = { title: "اختيار كلمة المرور" };

export default function ResetPasswordPage() {
  return (
    <AuthLayout>
      <Suspense>
        <PasswordForms mode="reset" />
      </Suspense>
    </AuthLayout>
  );
}
