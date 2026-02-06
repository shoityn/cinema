import Image from "next/image";
import { getFilmes } from "@/lib/services/filmes";
import { getPosterUrl } from "@/lib/tmbd";

export default async function Home() {
  const filmes = await getFilmes();

  return (
    <main className="p-6">
      <h1 className="text-2xl font-bold mb-6">Em cartaz</h1>

      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        {filmes.map((filme) => {
          const posterUrl =
            filme.tmdb_id && filme.poster_url
              ? getPosterUrl(filme.poster_url)
              : null;

          return (
            <div
              key={filme.id}
              className="rounded-lg overflow-hidden shadow hover:shadow-lg transition"
            >
              <div className="relative w-full aspect-[2/3] bg-gray-200">
                {/* <Image
                  src={posterUrl ?? "/no_poster.png"}
                  alt={filme.titulo}
                  fill
                  className="object-cover"
                /> */}
              </div>

              <div className="p-2 text-center">
                <h2 className="text-sm font-semibold">{filme.titulo}</h2>
              </div>
            </div>
          );
        })}
      </div>
    </main>
  );
}