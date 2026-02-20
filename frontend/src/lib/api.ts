const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

if (!process.env.NEXT_PUBLIC_API_URL) {

  console.warn("WARNING: NEXT_PUBLIC_API_URL não definida — usando fallback http://localhost:8000/api");
}

export async function api<T>(
  endpoint: string,
  options?: RequestInit
): Promise<T> {
  const res = await fetch(`${API_URL}${endpoint}`, {
    ...options,
  });

  if (!res.ok) {
    throw new Error(`Erro HTTP ${res.status}`);
  }

  return res.json();
}