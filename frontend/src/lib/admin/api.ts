"use client";

/**
 * Dashboard client for the Laravel admin API. The bearer token lives in sessionStorage,
 * or in localStorage when the user ticks "remember me"; it expires on the server anyway.
 */
const BASE = `${process.env.NEXT_PUBLIC_API_URL ?? ""}/api/v1/admin`;
const KEY = "ag-token";
export const LOGIN_PATH = "/admin/login/";

const stores = () => {
  try {
    return [sessionStorage, localStorage];
  } catch {
    return [];
  }
};

export const session = {
  token: () => stores().map((s) => s.getItem(KEY)).find(Boolean) ?? null,
  save(token: string, remember: boolean) {
    this.clear();
    stores()[remember ? 1 : 0]?.setItem(KEY, token);
  },
  clear: () => stores().forEach((s) => s.removeItem(KEY)),
};

/** A failed request. `errors` holds Laravel's per-field validation messages (422). */
export class ApiError extends Error {
  constructor(
    public status: number,
    message: string,
    public errors: Record<string, string[]> = {},
  ) {
    super(message);
  }
}

type Body = FormData | Record<string, unknown>;

export async function api<T = unknown>(path: string, method = "GET", body?: Body): Promise<T> {
  const headers: Record<string, string> = { Accept: "application/json" };
  const token = session.token();
  if (token) headers.Authorization = `Bearer ${token}`;
  if (body && !(body instanceof FormData)) headers["Content-Type"] = "application/json";

  let res: Response;
  try {
    res = await fetch(BASE + path, { method, headers, body: body instanceof FormData ? body : body && JSON.stringify(body) });
  } catch {
    throw new ApiError(0, "مش قادرين نوصل للسيرفر. اتأكد من الإنترنت وجرب تاني.");
  }

  if (res.status === 204) return undefined as T;
  const json = await res.json().catch(() => ({}));

  if (res.status === 401 && path !== "/login") {
    session.clear();
    location.replace(LOGIN_PATH);
  }
  if (!res.ok) {
    const message =
      res.status === 429 ? "محاولات كتير ورا بعض. استنى دقيقة وجرب تاني." : res.status >= 500 ? "حصلت مشكلة في السيرفر. جرب تاني بعد شوية." : (json.message ?? "حصلت مشكلة.");
    throw new ApiError(res.status, message, json.errors);
  }
  return json as T;
}

/** First validation message per field, for showing under inputs. */
export const fieldErrors = (e: unknown): Record<string, string> =>
  e instanceof ApiError ? Object.fromEntries(Object.entries(e.errors).map(([k, v]) => [k, v[0]])) : {};
