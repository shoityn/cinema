"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { archiveMovie, publishMovie } from "@/lib/services/movies";

interface Genre {
  genre_id?: number;
  id?: number;
  tmdb_id?: number;
  name: string;
}

interface Movie {
  movie_id: number;
  tmdb_id: number | null;
  title: string;
  release_date: string | null;
  poster_url: string | null;
  status?: string;
  genres?: Genre[];
}

interface MoviesDashboardProps {
  initialMovies: Movie[];
}

export default function MoviesDashboard({ initialMovies }: MoviesDashboardProps) {
  const [movies, setMovies] = useState(initialMovies);
  const [actioningId, setActioningId] = useState<number | null>(null);
  const router = useRouter();

  // Separate and sort movies: published first, then archived
  const [publishedMovies, archivedMovies] = movies.reduce(
    (acc, movie) => {
      if (movie.status === "archived") {
        acc[1].push(movie);
      } else {
        acc[0].push(movie);
      }
      return acc;
    },
    [[], []] as [Movie[], Movie[]]
  );

  const handleArchive = async (id: number) => {
    if (!confirm("Tem certeza que deseja arquivar este filme?")) {
      return;
    }

    setActioningId(id);
    try {
      await archiveMovie(id);
      setMovies(
        movies.map((m) =>
          m.movie_id === id ? { ...m, status: "archived" } : m
        )
      );
    } catch (error) {
      alert("Erro ao arquivar o filme");
      console.error(error);
    } finally {
      setActioningId(null);
    }
  };

  const handlePublish = async (id: number) => {
    setActioningId(id);
    try {
      await publishMovie(id);
      setMovies(
        movies.map((m) =>
          m.movie_id === id ? { ...m, status: "published" } : m
        )
      );
    } catch (error) {
      alert("Erro ao reativar o filme");
      console.error(error);
    } finally {
      setActioningId(null);
    }
  };

  const getYear = (dateString: string | null) => {
    if (!dateString) return "—";
    return new Date(dateString).getFullYear();
  };

  const getGenres = (genres: Genre[] | undefined) => {
    if (!genres || genres.length === 0) return "—";
    return genres.map((g) => g.name).join(", ");
  };

  const renderMovieTable = (movieList: Movie[], isArchived: boolean) => (
    <div className="overflow-x-auto border rounded-lg mb-8">
      <table className="w-full">
        <thead className="bg-muted/50 border-b">
          <tr>
            <th className="px-6 py-3 text-left text-sm font-semibold">Título</th>
            <th className="px-6 py-3 text-left text-sm font-semibold">Ano</th>
            <th className="px-6 py-3 text-left text-sm font-semibold">Gêneros</th>
            <th className="px-6 py-3 text-left text-sm font-semibold">TMDB ID</th>
            <th className="px-6 py-3 text-center text-sm font-semibold">Ações</th>
          </tr>
        </thead>
        <tbody>
          {movieList.map((movie) => (
            <tr
              key={movie.movie_id}
              className={`border-b transition ${
                isArchived ? "bg-gray-50 hover:bg-gray-100" : "hover:bg-muted/20"
              }`}
            >
              <td className="px-6 py-4">
                <p className={`font-medium ${isArchived ? "text-gray-500 line-through" : "text-foreground"}`}>
                  {movie.title}
                </p>
              </td>
              <td className="px-6 py-4 text-muted-foreground">{getYear(movie.release_date)}</td>
              <td className="px-6 py-4 text-muted-foreground text-sm">{getGenres(movie.genres)}</td>
              <td className="px-6 py-4 text-muted-foreground text-sm">{movie.tmdb_id ?? "—"}</td>
              <td className="px-6 py-4">
                <div className="flex justify-center gap-2">
                  <Link
                    href={`/movies/${movie.movie_id}`}
                    className="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm font-medium hover:bg-blue-200 transition"
                  >
                    👁️ Ver
                  </Link>
                  <Link
                    href={`/dashboard/add_filme?id=${movie.movie_id}`}
                    className="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm font-medium hover:bg-yellow-200 transition"
                  >
                    ✏️ Editar
                  </Link>
                  {isArchived ? (
                    <button
                      onClick={() => handlePublish(movie.movie_id)}
                      disabled={actioningId === movie.movie_id}
                      className="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium hover:bg-green-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      {actioningId === movie.movie_id ? "Ativando..." : "✓ Reativar"}
                    </button>
                  ) : (
                    <button
                      onClick={() => handleArchive(movie.movie_id)}
                      disabled={actioningId === movie.movie_id}
                      className="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-medium hover:bg-red-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      {actioningId === movie.movie_id ? "Arquivando..." : "📦 Arquivar"}
                    </button>
                  )}
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );

  return (
    <div className="w-full">
      <div className="mb-6">
        <Link
          href="/dashboard/add_filme"
          className="bg-primary text-primary-foreground px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition"
        >
          + Adicionar Novo Filme
        </Link>
      </div>

      {movies.length === 0 ? (
        <div className="text-center py-12 bg-muted/20 rounded-lg">
          <p className="text-muted-foreground mb-4">Nenhum filme cadastrado</p>
          <Link
            href="/dashboard/add_filme"
            className="text-primary underline hover:opacity-80"
          >
            Adicionar o primeiro filme
          </Link>
        </div>
      ) : (
        <>
          {publishedMovies.length > 0 && (
            <div>
              <h2 className="text-xl font-semibold mb-4 text-foreground">Publicados ({publishedMovies.length})</h2>
              {renderMovieTable(publishedMovies, false)}
            </div>
          )}

          {archivedMovies.length > 0 && (
            <div>
              <h2 className="text-xl font-semibold mb-4 text-muted-foreground">Arquivados ({archivedMovies.length})</h2>
              {renderMovieTable(archivedMovies, true)}
            </div>
          )}
        </>
      )}
    </div>
  );
}
