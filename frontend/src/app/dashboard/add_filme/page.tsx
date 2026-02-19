

"use client"

import { AddFilmeForm } from "@/components/forms/AddFilmeForm"
import { SearchBar } from "@/components/forms/searchAddMovie";
import { useState } from "react";
import { Button } from '@/components/ui/button'

export default function AddFilmePage() {
  const [initialMovie, setInitialMovie] = useState<any | null>(null)

  return (
    <div className="container mx-auto py-10">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold">Adicionar Filme</h1>
        <p className="text-muted-foreground">
          Preencha os dados para cadastrar um novo filme.
        </p>
      </div>
      <div className="flex items-center gap-4">
        <SearchBar onSelect={(details) => setInitialMovie(details)} />

        <div>
          <Button
            onClick={async () => {
              if (!initialMovie) return alert('Selecione um filme antes de salvar.')

              const d = initialMovie
              const payload = {
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
              }

              try {
                const res = await fetch('http://localhost:8000/api/movies', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                  body: JSON.stringify(payload),
                })

                const body = await res.json().catch(() => null)
                if (!res.ok) throw new Error(body?.message || 'Erro ao salvar')

                alert('Filme criado com sucesso')
              } catch (err: any) {
                console.error(err)
                alert('Erro: ' + (err.message || err))
              }
            }}
          >
            Salvar selecionado
          </Button>
        </div>
      </div>

      <AddFilmeForm initial={initialMovie} />
    </div>
  )
}

