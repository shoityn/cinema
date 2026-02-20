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
  // Request all movies (includes archived) so dashboard can show archived on load
  const res = await api<FilmesResponse>("/api/movies?all=1", {
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
  // If there are File objects in media, send multipart/form-data
  const hasFiles = !!(
    data?.media?.poster?.file || data?.media?.backdrop?.file || data?.media?.logo?.file || data?.media?.trailer?.file
  );

  let response: Response;

  if (hasFiles) {
    const form = new FormData();

    // Append simple fields
    form.append("title", data.title ?? "");
    form.append("overview", data.overview ?? "");
    form.append("release_date", data.release_date ?? "");
    form.append("duration_minutes", data.duration_minutes != null ? String(data.duration_minutes) : "");
    form.append("tmdb_id", data.tmdb_id != null ? String(data.tmdb_id) : "");
    form.append("imdb_id", data.imdb_id ?? "");
    form.append("homepage", data.homepage ?? "");
    form.append("status", data.status ?? "published");

    // Genres as JSON string
    form.append("genres", JSON.stringify(data.genres ?? []));

    // Media fields (provider/path) and files
    const sections = ["poster", "backdrop", "logo", "trailer"];
    for (const s of sections) {
      const m = data.media?.[s] ?? {};
      if (m.provider != null) form.append(`media[${s}][provider]`, m.provider);
      if (m.path != null) form.append(`media[${s}][path]`, m.path);
      if (m.url != null) form.append(`media[${s}][url]`, m.url);
      if (m.external_key != null) form.append(`media[${s}][external_key]`, m.external_key);
      if (m.file instanceof File) form.append(`media[${s}][file]`, m.file);
    }

    response = await fetch(`${apiUrl}/api/movies`, {
      method: "POST",
      // Tell Laravel we expect JSON so validation errors return JSON instead of a 302 redirect
      headers: {
        Accept: "application/json",
        // Do NOT set Content-Type when sending FormData; the browser will set the correct boundary
      },
      // If your API requires cookies (sanctum/session) enable credentials
      // credentials: 'include',
      body: form,
    });
  } else {
    response = await fetch(`${apiUrl}/api/movies`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
  }

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || "Erro ao criar o filme");
  }

  return response.json();
}

export async function updateMovie(id: number, data: any) {
  const apiUrl = process.env.NEXT_PUBLIC_API_URL;
  const hasFiles = !!(
    data?.media?.poster?.file || data?.media?.backdrop?.file || data?.media?.logo?.file || data?.media?.trailer?.file
  );

  let response: Response;

  if (hasFiles) {
    const form = new FormData();

    form.append("title", data.title ?? "");
    form.append("overview", data.overview ?? "");
    form.append("release_date", data.release_date ?? "");
    form.append("duration_minutes", data.duration_minutes != null ? String(data.duration_minutes) : "");
    form.append("tmdb_id", data.tmdb_id != null ? String(data.tmdb_id) : "");
    form.append("imdb_id", data.imdb_id ?? "");
    form.append("homepage", data.homepage ?? "");
    form.append("status", data.status ?? "published");
    form.append("genres", JSON.stringify(data.genres ?? []));

    const sections = ["poster", "backdrop", "logo", "trailer"];
    for (const s of sections) {
      const m = data.media?.[s] ?? {};
      if (m.provider != null) form.append(`media[${s}][provider]`, m.provider);
      if (m.path != null) form.append(`media[${s}][path]`, m.path);
      if (m.url != null) form.append(`media[${s}][url]`, m.url);
      if (m.external_key != null) form.append(`media[${s}][external_key]`, m.external_key);
      if (m.file instanceof File) form.append(`media[${s}][file]`, m.file);
    }

    // When sending files, PHP's $_FILES is populated for POST multipart requests.
    // Use POST with method override so Laravel correctly handles the upload as PUT.
    form.append("_method", "PUT");

    response = await fetch(`${apiUrl}/api/movies/${id}`, {
        method: "POST",
        headers: {
          Accept: "application/json",
        },
        // credentials: 'include',
        body: form,
    });
  } else {
    response = await fetch(`${apiUrl}/api/movies/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
  }

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