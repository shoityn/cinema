"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { getPosterUrl } from "@/lib/tmbd";
import PosterImage from "@/components/PosterImage";
import Link from "next/link";

interface Genre {
  genre_id?: number;
  id?: number;
  tmdb_id?: number;
  name: string;
}

interface Media {
  poster?: { url?: string; path?: string };
  trailer?: { url?: string };
}

interface MovieDetails {
  movie_id: number;
  tmdb_id: number | null;
  title: string;
  overview: string | null;
  release_date: string | null;
  duration_minutes: number | null;
  runtime: number | null;
  poster_url: string | null;
  genres?: Genre[];
  media?: Media;
}

export default function MovieDetails() {
  const params = useParams();
  const router = useRouter();
  const [movie, setMovie] = useState<MovieDetails | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const movieId = params.id as string;

  useEffect(() => {
    async function fetchMovie() {
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL;
        const response = await fetch(`${apiUrl}/api/movies/${movieId}`);
        
        if (!response.ok) {
          throw new Error("Filme não encontrado");
        }

        const data = await response.json();
        setMovie(data.data);
      } catch (err) {
        setError(err instanceof Error ? err.message : "Erro ao carregar o filme");
      } finally {
        setLoading(false);
      }
    }

    fetchMovie();
  }, [movieId]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-muted-foreground">Carregando...</p>
        </div>
      </div>
    );
  }

  if (error || !movie) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <p className="text-xl text-red-500 mb-4">{error || "Filme não encontrado"}</p>
          <Link href="/" className="text-primary underline">Voltar para filmes</Link>
        </div>
      </div>
    );
  }

  // Resolve poster URL
  let posterUrl = "/no_poster.png";
  const mediaPosterUrl = movie.media?.poster?.url;
  const mediaPosterPath = movie.media?.poster?.path;
  if (mediaPosterUrl) {
    posterUrl = String(mediaPosterUrl);
  } else if (movie.poster_url) {
    const p = String(movie.poster_url);
    posterUrl = p.startsWith("http") ? p : (getPosterUrl(p) ?? "/no_poster.png");
  } else if (mediaPosterPath) {
    posterUrl = getPosterUrl(String(mediaPosterPath)) ?? "/no_poster.png";
  }

  const year = movie.release_date ? new Date(movie.release_date).getFullYear() : null;
  const genres = movie.genres ?? [];
  const runtime = movie.duration_minutes ?? (movie.runtime as number) ?? null;

  return (
    <main className="min-h-screen bg-gradient-to-b from-background to-muted/20">
      <div className="p-6 max-w-6xl mx-auto">
        {/* Header */}
        <div className="mb-8">
          <Link href="/" className="text-primary underline hover:opacity-80 mb-4 inline-block">
            ← Voltar
          </Link>
        </div>

        {/* Content */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Poster */}
          <div className="md:sticky md:top-6 md:h-fit">
            <div className="relative w-full aspect-[2/3] bg-gray-100 rounded-2xl overflow-hidden shadow-lg">
              <PosterImage src={posterUrl} alt={movie.title} className="object-cover w-full h-full" />
            </div>
            
            {/* Actions */}
            <div className="mt-6 space-y-3"> 
              {movie.media?.trailer?.url && (
                <a
                  href={movie.media.trailer.url}
                  target="_blank"
                  rel="noreferrer"
                  className="block w-full bg-secondary text-secondary-foreground py-2 px-4 rounded-lg text-center font-semibold hover:opacity-90 transition"
                >
                  Assitir Trailer
                </a>
              )}
            </div>
          </div>

          {/* Details */}
          <div className="md:col-span-2">
            {/* Title */}
            <h1 className="text-4xl md:text-5xl font-extrabold mb-4">{movie.title}</h1>

            {/* Meta Info */}
            <div className="flex flex-wrap gap-3 mb-6 text-muted-foreground">
              {year && (
                <span className="px-3 py-1 bg-muted/40 rounded-full">
                  📅 {year}
                </span>
              )}
              {runtime && (
                <span className="px-3 py-1 bg-muted/40 rounded-full">
                  ⏱️ {runtime} min
                </span>
              )}
              {movie.tmdb_id && (
                <span className="px-3 py-1 bg-muted/40 rounded-full">
                  🎬 TMDB: {movie.tmdb_id}
                </span>
              )}
            </div>

            {/* Genres */}
            {genres.length > 0 && (
              <div className="mb-6">
                <h3 className="text-sm font-semibold text-muted-foreground mb-2">GÊNEROS</h3>
                <div className="flex flex-wrap gap-2">
                  {genres.map((g) => (
                    <span
                      key={g.genre_id ?? g.id ?? g.tmdb_id}
                      className="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium"
                    >
                      {g.name}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Overview */}
            {movie.overview && (
              <div>
                <h3 className="text-sm font-semibold text-muted-foreground mb-2">SINOPSE</h3>
                <p className="text-base text-foreground leading-relaxed">{movie.overview}</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </main>
  );
}
