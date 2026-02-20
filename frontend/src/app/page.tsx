import { getMovies } from "@/lib/services/movies";
import { getPosterUrl } from "@/lib/tmbd";
import PosterImage from "@/components/PosterImage";
import Link from "next/link";

export default async function Home() {
  const movies = await getMovies();

  return (
    <main className="p-6">
      <h1 className="text-3xl font-extrabold mb-6">Em cartaz</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {movies.map((movie) => {
          // Resolve poster URL from multiple possible sources:
          // 1) movie.media?.poster?.url (full URL)
          // 2) movie.poster_url (may be full URL or TMDB path)
          // 3) movie.media?.poster?.path (TMDB path)
          // fallback: /no_poster.png
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

          return (
            <Link key={movie.movie_id} href={`/movies/${movie.movie_id}`}>
              <article className="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 cursor-pointer h-full">
                <div className="relative w-full aspect-[2/3] bg-gray-100">
                  <PosterImage src={posterUrl} alt={movie.title} className="object-cover w-full h-full" />
                </div>

                <div className="p-4">
                  <h2 className="text-lg font-semibold line-clamp-2">{movie.title}</h2>
                  <div className="flex items-center gap-2 mt-2 text-sm text-muted-foreground">
                    {year && <span className="px-2 py-0.5 bg-muted/20 rounded">{year}</span>}
                    <div className="flex gap-1 flex-wrap">
                      {genres.slice(0, 2).map((g: any) => (
                        <span key={g.genre_id ?? g.id ?? g.tmdb_id} className="text-xs px-2 py-0.5 bg-primary/10 text-primary rounded">{g.name}</span>
                      ))}
                    </div>
                  </div>
                </div>
              </article>
            </Link>
          );
        })}
      </div>
    </main>
  );
}