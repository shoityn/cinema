export interface Genre {
  genre_id?: number;
  id?: number;
  tmdb_id?: number;
  name: string;
}

export interface Movie {
  movie_id: number;
  tmdb_id: number | null;
  title: string;
  poster_url: string | null;
  release_date?: string | null;
  status?: string;
  genres?: Genre[];
}