import type { Metadata } from "next";
import { Suspense } from "react";
import AuthLayout from "@/components/admin/AuthLayout";
import PasswordForms from "@/components/admin/PasswordForms";

export const metadata: Metadata = { title: "نسيت كلمة المرور" };

export default function ForgotPasswordPage() {
  return (
    <AuthLayout>
      <Suspense>
        <PasswordForms mode="forgot" />
      </Suspense>
    </AuthLayout>
  );
}
