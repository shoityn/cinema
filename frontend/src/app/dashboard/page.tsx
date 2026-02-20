


import { getMovies } from "@/lib/services/movies";
import MoviesDashboard from "@/components/MoviesDashboard";

export default async function Dashboard() {
  const movies = await getMovies();

  return (
    <div className="min-h-screen bg-gradient-to-b from-background to-muted/20 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="mb-8">
          <h1 className="text-4xl font-extrabold mb-2">Gerenciar Filmes</h1>
          <p className="text-muted-foreground">Adicione, edite ou remova filmes do seu catálogo</p>
        </div>

        <MoviesDashboard initialMovies={movies} />
      </div>
    </div>
  );
}

