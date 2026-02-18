import { api } from "@/lib/api";
import { Movie } from "@/lib/types/movies";

interface FilmesResponse {
  data: Movie[];
}

export async function getMovies() {
  const res = await api<FilmesResponse>("/api/movies", {
    cache: "no-store",
  });

  return res.data;
}