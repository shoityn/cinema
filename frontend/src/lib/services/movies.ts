import { api } from "@/lib/api";
import { Movie } from "@/lib/types/movies";

interface FilmesResponse {
  data: Movie[];
}

interface MovieResponse {
  data: Movie;
}

interface ApiResponse {
  message: string;
  data?: Movie;
}

export async function getMovies() {
  const res = await api<FilmesResponse>("/api/movies", {
    cache: "no-store",
  });

  return res.data;
}

export async function getMovieById(id: number) {
  const res = await api<MovieResponse>(`/api/movies/${id}`, {
    cache: "no-store",
  });

  return res.data;
}

export async function createMovie(data: any) {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  const response = await fetch(`${apiUrl}/api/movies`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || "Erro ao criar o filme");
  }

  return response.json();
}

export async function updateMovie(id: number, data: any) {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  const response = await fetch(`${apiUrl}/api/movies/${id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || "Erro ao atualizar o filme");
  }

  return response.json();
}

export async function archiveMovie(id: number) {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  const response = await fetch(`${apiUrl}/api/movies/${id}/archive`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || "Erro ao arquivar o filme");
  }

  return response.json();
}

export async function publishMovie(id: number) {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  const response = await fetch(`${apiUrl}/api/movies/${id}/publish`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || "Erro ao publicar o filme");
  }

  return response.json();
}