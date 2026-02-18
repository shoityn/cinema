import Image from "next/image";
import { getMovies } from "@/lib/services/movies";
import { getPosterUrl } from "@/lib/tmbd";

export default async function Home() {
  const movies = await getMovies();

  return (
    <main className="p-6">
      <h1 className="text-2xl font-bold mb-6">Em cartaz</h1>

      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        {movies.map((movie) => {
          const posterUrl =
            movie.tmdb_id && movie.poster_url
              ? getPosterUrl(movie.poster_url)
              : null;

          return (
            <div
              key={movie.movie_id}
              className="rounded-lg overflow-hidden shadow hover:shadow-lg transition"
            >
              <div className="relative w-full aspect-[2/3] bg-gray-200">
                <Image
                  src={posterUrl ?? "/no_poster.png"}
                  alt={movie.title}
                  fill
                  className="object-cover"
                />
              </div>

              <div className="p-2 text-center">
                <h2 className="text-sm font-semibold">{movie.movie_id}</h2>
              </div>
            </div>
          );
        })}
      </div>
    </main>
  );
}