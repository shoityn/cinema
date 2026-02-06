import { api } from "@/lib/api";
import { Filme } from "@/lib/types/filmes";

interface FilmesResponse {
  data: Filme[];
}

export async function getFilmes() {
  const res = await api<FilmesResponse>("/api/filmes", {
    cache: "no-store",
  });

  return res.data;
}