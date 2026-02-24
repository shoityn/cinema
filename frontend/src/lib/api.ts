const API_URL =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

if (!process.env.NEXT_PUBLIC_API_URL) {
  console.warn(
    "WARNING: NEXT_PUBLIC_API_URL não definida — usando fallback http://localhost:8000/api"
  );
}

export async function api<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const token =
    typeof window !== "undefined"
      ? localStorage.getItem("token")
      : null;

  const res = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      ...(token && { Authorization: `Bearer ${token}` }),
      ...options.headers,
    },
  });

  if (res.status === 401) {
    localStorage.removeItem("token");
    window.location.href = "/login";
    throw new Error("Não autenticado");
  }

  if (!res.ok) {
    const errorBody = await res.text();
    throw new Error(`Erro HTTP ${res.status}: ${errorBody}`);
  }

  return res.json();
}