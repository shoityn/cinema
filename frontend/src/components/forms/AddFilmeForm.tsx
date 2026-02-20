"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

import { useEffect } from "react";
import { createMovie, updateMovie } from "@/lib/services/movies";

interface AddFilmeFormProps {
  initial?: any | null;
  movieId?: number;
  isEditing?: boolean;
}

export function AddFilmeForm({ initial, movieId, isEditing = false }: AddFilmeFormProps = { initial: null }) {
  const router = useRouter();
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    // movie fields expected by backend
    title: "",
    overview: "",
    release_date: "",
    duration_minutes: "",
    tmdb_id: "",
    imdb_id: "",
    homepage: "",
    status: "published",

    // genres: array of objects { tmdb_id, name }
    genres: [{ tmdb_id: "", name: "" }],

    // media: poster/backdrop/logo/trailer
    media: {
      poster: { provider: "tmdb", path: "", url: "" },
      backdrop: { provider: "tmdb", path: "", url: "" },
      logo: { provider: "tmdb", path: "", url: "" },
      trailer: { provider: "tmdb", external_key: "", url: "" },
    },
  });

  function handleChange(
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>,
  ) {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  }

  function handleStatusChange(value: string) {
    setFormData((prev) => ({
      ...prev,
      status: value,
    }));
  }

  function updateGenre(index: number, key: string, value: string) {
    setFormData((prev) => {
      const genres = [...prev.genres];
      genres[index] = { ...genres[index], [key]: value };
      return { ...prev, genres };
    });
  }

  function addGenre() {
    setFormData((prev) => ({ ...prev, genres: [...prev.genres, { tmdb_id: "", name: "" }] }));
  }

  function removeGenre(i: number) {
    setFormData((prev) => ({ ...prev, genres: prev.genres.filter((_, idx) => idx !== i) }));
  }

  function updateMedia(section: string, key: string, value: string) {
    setFormData((prev) => ({
      ...prev,
      media: (() => {
        const defaultMedia = {
          poster: { provider: "tmdb", path: "", url: "" },
          backdrop: { provider: "tmdb", path: "", url: "" },
          logo: { provider: "tmdb", path: "", url: "" },
          trailer: { provider: "tmdb", external_key: "", url: "" },
        };
        const media = (prev as any).media ?? defaultMedia;
        return {
          ...media,
          [section]: {
            ...(media[section] ?? {}),
            [key]: value,
          },
        };
      })(),
    }));
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setSubmitting(true);

    // build payload matching backend expected structure
    const payload = {
      title: formData.title,
      overview: formData.overview,
      release_date: formData.release_date || null,
      duration_minutes: formData.duration_minutes ? Number(formData.duration_minutes) : null,
      tmdb_id: formData.tmdb_id ? Number(formData.tmdb_id) : null,
      imdb_id: formData.imdb_id || null,
      homepage: formData.homepage || null,
      genres: formData.genres.filter(g => g.tmdb_id || g.name).map(g => ({
        tmdb_id: g.tmdb_id ? Number(g.tmdb_id) : null,
        name: g.name || null,
      })),
      media: {
        poster: {
          provider: formData.media.poster.provider,
          path: formData.media.poster.path || null,
          url: formData.media.poster.url || null,
        },
        backdrop: {
          provider: formData.media.backdrop.provider,
          path: formData.media.backdrop.path || null,
          url: formData.media.backdrop.url || null,
        },
        logo: {
          provider: formData.media.logo.provider,
          path: formData.media.logo.path || null,
          url: formData.media.logo.url || null,
        },
        trailer: {
          provider: formData.media.trailer.provider,
          external_key: formData.media.trailer.external_key || null,
          url: formData.media.trailer.url || null,
        },
      },
    };

    try {
      if (isEditing && movieId) {
        // Update existing movie
        await updateMovie(movieId, payload);
        alert("Filme atualizado com sucesso");
      } else {
        // Create new movie
        await createMovie(payload);
        alert("Filme criado com sucesso");
      }
      router.push("/dashboard");
    } catch (err: any) {
      console.error(err);
      alert("Erro: " + err.message);
    } finally {
      setSubmitting(false);
    }
  }

  // When `initial` changes (selected from search), populate the form with details
  useEffect(() => {
    if (!initial) return;

    // map incoming payload (TmdbController::details shape) to formData
    setFormData((prev) => ({
      ...prev,
      title: initial.title ?? prev.title,
      overview: initial.overview ?? prev.overview,
      release_date: initial.release_date ?? prev.release_date,
      duration_minutes: initial.duration_minutes ?? prev.duration_minutes,
      tmdb_id: initial.tmdb_id ?? prev.tmdb_id,
      imdb_id: initial.imdb_id ?? prev.imdb_id,
      homepage: initial.homepage ?? prev.homepage,
      // genres: array of objects { tmdb_id, name }
      genres: Array.isArray(initial.genres) && initial.genres.length > 0 ? initial.genres.map((g: any) => ({ tmdb_id: g.tmdb_id ?? g.id ?? "", name: g.name ?? "" })) : prev.genres,
      // media mapping - keep existing values when absent
      media: {
        poster: {
          provider: initial.media?.poster?.provider ?? prev.media.poster.provider,
          path: initial.media?.poster?.path ?? prev.media.poster.path,
          url: initial.media?.poster?.url ?? (initial.poster_url ?? prev.media.poster.url),
        },
        backdrop: {
          provider: initial.media?.backdrop?.provider ?? prev.media.backdrop.provider,
          path: initial.media?.backdrop?.path ?? prev.media.backdrop.path,
          url: initial.media?.backdrop?.url ?? (initial.backdrop_url ?? prev.media.backdrop.url),
        },
        logo: {
          provider: initial.media?.logo?.provider ?? prev.media.logo.provider,
          path: initial.media?.logo?.path ?? prev.media.logo.path,
          url: initial.media?.logo?.url ?? prev.media.logo.url,
        },
        trailer: {
          provider: initial.media?.trailer?.provider ?? prev.media.trailer.provider,
          external_key: initial.media?.trailer?.external_key ?? prev.media.trailer.external_key ?? null,
          url: initial.media?.trailer?.url ?? prev.media.trailer.url,
        },
      },
    }));
  }, [initial]);

  return (
    <Card>
      <CardHeader>
        <CardTitle>Dados do Filme</CardTitle>
      </CardHeader>

      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <Label htmlFor="title">Título</Label>
            <Input id="title" name="title" value={formData.title} onChange={handleChange} required />
          </div>

          <div>
            <Label htmlFor="overview">Sinopse</Label>
            <Textarea id="overview" name="overview" value={formData.overview} onChange={handleChange} rows={4} />
          </div>

          <div className="grid grid-cols-3 gap-4">
            <div>
              <Label htmlFor="release_date">Data de Lançamento</Label>
              <Input type="date" id="release_date" name="release_date" value={formData.release_date} onChange={handleChange} />
            </div>

            <div>
              <Label htmlFor="duration_minutes">Duração (min)</Label>
              <Input id="duration_minutes" name="duration_minutes" value={formData.duration_minutes} onChange={handleChange} />
            </div>

            <div>
              <Label htmlFor="tmdb_id">TMDB ID</Label>
              <Input id="tmdb_id" name="tmdb_id" value={formData.tmdb_id} onChange={handleChange} />
            </div>

          </div>

          <div>
            <Label htmlFor="trailer_url">Trailer URL</Label>
            <Input id="trailer_url" name="trailer_url" value={formData.media?.trailer?.url ?? ""} onChange={(e) => updateMedia('trailer', 'url', e.target.value)} />
          </div>

          <div>
            <Label htmlFor="poster_url">Poster URL</Label>
            <Input id="poster_url" name="poster_url" value={formData.media?.poster?.url ?? ""} onChange={(e) => updateMedia('poster', 'url', e.target.value)} />
          </div>

          <div>
            <Label htmlFor="backdrop_url">Backdrop URL</Label>
            <Input id="backdrop_url" name="backdrop_url" value={formData.media?.backdrop?.url ?? ""} onChange={(e) => updateMedia('backdrop', 'url', e.target.value)} />
          </div>

          <div>
            <Label htmlFor="homepage">Homepage</Label>
            <Input id="homepage" name="homepage" value={formData.homepage} onChange={handleChange} />
          </div>

          <div>
            <Label>Status</Label>
            <Select value={formData.status} onValueChange={handleStatusChange}>
              <SelectTrigger>
                <SelectValue placeholder="Selecione o status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="published">Publicado</SelectItem>
                <SelectItem value="archived">Arquivado</SelectItem>
                <SelectItem value="draft">Rascunho</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label>Gêneros</Label>
            <div className="space-y-2">
              {formData.genres.map((g, i) => (
                <div key={i} className="flex gap-2">
                  <Input placeholder="tmdb_id" value={g.tmdb_id} onChange={(e) => updateGenre(i, 'tmdb_id', e.target.value)} />
                  <Input placeholder="name" value={g.name} onChange={(e) => updateGenre(i, 'name', e.target.value)} />
                  <Button type="button" onClick={() => removeGenre(i)}>Remover</Button>
                </div>
              ))}
              <Button type="button" onClick={addGenre}>Adicionar Gênero</Button>
            </div>
          </div>

          <div>
            <Label>Mídia (poster/backdrop/logo)</Label>
            <div className="grid grid-cols-3 gap-2">
              <div>
                <Label>Poster - path</Label>
                <Input value={formData.media.poster.path} onChange={(e) => updateMedia('poster', 'path', e.target.value)} />
                <Label>provider</Label>
                <Select value={formData.media.poster.provider} onValueChange={(v) => updateMedia('poster', 'provider', v)}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="tmdb">tmdb</SelectItem>
                    <SelectItem value="local">local</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <Label>Backdrop - path</Label>
                <Input value={formData.media.backdrop.path} onChange={(e) => updateMedia('backdrop', 'path', e.target.value)} />
                <Label>provider</Label>
                <Select value={formData.media.backdrop.provider} onValueChange={(v) => updateMedia('backdrop', 'provider', v)}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="tmdb">tmdb</SelectItem>
                    <SelectItem value="local">local</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div>
                <Label>Logo - path</Label>
                <Input value={formData.media.logo.path} onChange={(e) => updateMedia('logo', 'path', e.target.value)} />
                <Label>provider</Label>
                <Select value={formData.media.logo.provider} onValueChange={(v) => updateMedia('logo', 'provider', v)}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="tmdb">tmdb</SelectItem>
                    <SelectItem value="local">local</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>

          <Button type="submit" className="w-full" disabled={submitting}>
            {submitting ? "Salvando..." : isEditing ? "Atualizar Filme" : "Salvar Filme"}
          </Button>
        </form>
      </CardContent>
    </Card>
  );
}
