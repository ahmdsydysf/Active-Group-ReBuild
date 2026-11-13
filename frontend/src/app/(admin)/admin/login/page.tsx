import type { Metadata } from "next";
import AuthLayout from "@/components/admin/AuthLayout";
import LoginForm from "@/components/admin/LoginForm";

export const metadata: Metadata = { title: "تسجيل الدخول" };

export default function LoginPage() {
  return (
    <AuthLayout>
      <LoginForm />
    </AuthLayout>
  );
}
