"use client"

import { AddFilmeForm } from "@/components/forms/AddFilmeForm"
import { SearchBar } from "@/components/forms/searchAddMovie";
import { useSearchParams, useRouter } from "next/navigation";
import { useState, useEffect } from "react";
import { Button } from '@/components/ui/button'
import { getMovieById, createMovie, updateMovie } from "@/lib/services/movies";

export default function AddFilmePage() {
  const [initialMovie, setInitialMovie] = useState<any | null>(null)
  const [loading, setLoading] = useState(false)
  const searchParams = useSearchParams()
  const router = useRouter()
  const movieId = searchParams.get('id')
  const isEditing = !!movieId

  useEffect(() => {
    if (movieId) {
      setLoading(true)
      getMovieById(Number(movieId))
        .then(movie => setInitialMovie(movie))
        .catch(error => {
          console.error('Erro ao carregar filme:', error)
          alert('Erro ao carregar dados do filme')
        })
        .finally(() => setLoading(false))
    }
  }, [movieId])

  const preparePayload = (d: any) => ({
    title: d.title,
    overview: d.overview,
    release_date: d.release_date || null,
    duration_minutes: d.duration_minutes ?? d.runtime ?? null,
    tmdb_id: d.tmdb_id ?? d.id ?? null,
    imdb_id: d.imdb_id ?? null,
    homepage: d.homepage ?? null,
    genres: (d.genres || []).map((g: any) => ({ tmdb_id: g.tmdb_id ?? g.id ?? null, name: g.name ?? null })),
    media: {
      poster: {
        provider: d.media?.poster?.provider ?? 'tmdb',
        path: d.media?.poster?.path ?? d.poster_path ?? null,
        url: d.media?.poster?.url ?? d.poster_url ?? null,
      },
      backdrop: {
        provider: d.media?.backdrop?.provider ?? 'tmdb',
        path: d.media?.backdrop?.path ?? d.backdrop_path ?? null,
        url: d.media?.backdrop?.url ?? d.backdrop_url ?? null,
      },
      logo: {
        provider: d.media?.logo?.provider ?? 'tmdb',
        path: d.media?.logo?.path ?? null,
        url: d.media?.logo?.url ?? null,
      },
      trailer: {
        provider: d.media?.trailer?.provider ?? 'tmdb',
        external_key: d.media?.trailer?.external_key ?? null,
        url: d.media?.trailer?.url ?? null,
      },
    },
  })

  const handleSaveFromSearch = async () => {
    if (!initialMovie) return alert('Selecione um filme antes de salvar.')

    try {
      const payload = preparePayload(initialMovie)
      await createMovie(payload)
      alert('Filme criado com sucesso')
      setInitialMovie(null)
      router.push('/dashboard')
    } catch (err: any) {
      console.error(err)
      alert('Erro: ' + (err.message || err))
    }
  }

  return (
    <div className="container mx-auto py-10">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold">
          {isEditing ? 'Editar Filme' : 'Adicionar Filme'}
        </h1>
        <p className="text-muted-foreground">
          {isEditing ? 'Atualize os dados do filme.' : 'Preencha os dados para cadastrar um novo filme.'}
        </p>
      </div>

      {loading ? (
        <div className="flex items-center justify-center py-12">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
            <p className="text-muted-foreground">Carregando...</p>
          </div>
        </div>
      ) : (
        <>
          {!isEditing && (
            <div className="flex items-center gap-4 mb-6">
              <SearchBar onSelect={(details) => setInitialMovie(details)} />
              <Button onClick={handleSaveFromSearch}>
                Salvar selecionado
              </Button>
            </div>
          )}

          <AddFilmeForm 
            initial={initialMovie} 
            movieId={movieId ? Number(movieId) : undefined}
            isEditing={isEditing}
          />
        </>
      )}
    </div>
  )
}

