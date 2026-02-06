const API_URL = process.env.NEXT_PUBLIC_API_URL;

if (!API_URL) {
  throw new Error("NEXT_PUBLIC_API_URL não definida");
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