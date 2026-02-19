"use client"

import { useState, useEffect } from "react"
import { Input } from "@/components/ui/input"
import { Card, CardContent } from "@/components/ui/card"
import { Loader2, Search } from "lucide-react"
import { motion, AnimatePresence } from "framer-motion"

interface Movie {
  tmdb_id: number
  title: string
  release_date?: string
  poster_url?: string | null
}

export function SearchBar({ onSelect }: { onSelect?: (movie: any) => void }) {
  const [query, setQuery] = useState("")
  const [results, setResults] = useState<Movie[]>([])
  const [loading, setLoading] = useState(false)
  const [open, setOpen] = useState(false)

  // Debounce
  useEffect(() => {
    if (!query.trim()) {
      setResults([])
      setOpen(false)
      return
    }

    const timeout = setTimeout(async () => {
      try {
        setLoading(true)

        const res = await fetch(
          `http://localhost:8000/api/tmdb/search?q=${encodeURIComponent(query)}`
        )

        if (!res.ok) throw new Error("Erro na busca")

        const data = await res.json()
        setResults(data)
        setOpen(true)
      } catch (err) {
        console.error(err)
      } finally {
        setLoading(false)
      }
    }, 400)

    return () => clearTimeout(timeout)
  }, [query])

  return (
    <div className="relative w-full max-w-m mx-auto m-3">
      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <Input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Buscar filme pelo nome..."
          className="pl-9"
        />
        {loading && (
          <Loader2 className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 animate-spin text-muted-foreground" />
        )}
      </div>

      <AnimatePresence>
        {open && results.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            transition={{ duration: 0.2 }}
            className="absolute mt-2 w-full z-50"
          >
            <Card className="shadow-lg rounded-2xl">
              <CardContent className="p-2">
                <ul className="space-y-1">
                  {results.map((movie) => (
                    <li
                      key={movie.tmdb_id}
                      onClick={async () => {
                        setQuery(movie.title)
                        setOpen(false)
                        setResults([])

                        if (onSelect) {
                          try {
                            const res = await fetch(`http://localhost:8000/api/tmdb/movies/${movie.tmdb_id}`)
                            if (res.ok) {
                              const details = await res.json()
                              onSelect(details)
                            }
                          } catch (e) {
                            console.error('Erro ao obter detalhes TMDB', e)
                          }
                        }
                      }}
                      className="p-2 rounded-lg hover:bg-muted cursor-pointer transition flex items-center gap-3"
                    >
                      {movie.poster_url ? (
                        <img
                          src={movie.poster_url}
                          alt={movie.title}
                          className="w-10 h-14 object-cover rounded"
                        />
                      ) : (
                        <div className="w-10 h-14 bg-muted/30 rounded" />
                      )}

                      <div className="flex-1">
                        <div className="text-sm font-medium">{movie.title}</div>
                        {movie.release_date && (
                          <div className="text-xs text-muted-foreground">
                            {new Date(movie.release_date).getFullYear()}
                          </div>
                        )}
                      </div>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  )
}
