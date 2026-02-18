"use client";

import { useState } from "react";
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

export function AddFilmeForm() {
  const [formData, setFormData] = useState({
    id: "",
    tmdb_id: "",
    titulo: "",
    sinopse: "",
    data_lancamento: "",
    trailer_url: "",
    poster_url: "",
    backdrop_url: "",
    status: "ativo",
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

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    console.log("Dados enviados:", formData);

    // Aqui futuramente você fará:
    // fetch("http://localhost:8000/api/movies", { ... })
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Dados do Filme</CardTitle>
      </CardHeader>

      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <Label htmlFor="titulo">Título</Label>
            <Input
              id="titulo"
              name="titulo"
              value={formData.titulo}
              onChange={handleChange}
              required
            />
          </div>

          <div>
            <Label htmlFor="sinopse">Sinopse</Label>
            <Textarea
              id="sinopse"
              name="sinopse"
              value={formData.sinopse}
              onChange={handleChange}
              rows={4}
            />
          </div>

          <div className="grid grid-cols-3 gap-4">
            <div>
              <Label htmlFor="data_lancamento">Data de Lançamento</Label>
              <Input
                type="date"
                id="data_lancamento"
                name="data_lancamento"
                value={formData.data_lancamento}
                onChange={handleChange}
              />
            </div>

            <div>
              <Label htmlFor="id">ID</Label>
              <Input
                id="id"
                name="id"
                value={formData.id}
                disabled
                onChange={handleChange}
              />
            </div>

            <div>
              <Label htmlFor="tmdb_id">TMDB ID</Label>
              <Input
                id="tmdb_id"
                name="tmdb_id"
                value={formData.tmdb_id}
                disabled
                onChange={handleChange}
              />
            </div>

          </div>

          <div>
            <Label htmlFor="trailer_url">Trailer URL</Label>
            <Input
              id="trailer_url"
              name="trailer_url"
              value={formData.trailer_url}
              onChange={handleChange}
            />
          </div>

          <div>
            <Label htmlFor="poster_url">Poster URL</Label>
            <Input
              id="poster_url"
              name="poster_url"
              value={formData.poster_url}
              onChange={handleChange}
            />
          </div>

          <div>
            <Label htmlFor="backdrop_url">Backdrop URL</Label>
            <Input
              id="backdrop_url"
              name="backdrop_url"
              value={formData.backdrop_url}
              onChange={handleChange}
            />
          </div>

          <div>
            <Label>Status</Label>
            <Select value={formData.status} onValueChange={handleStatusChange}>
              <SelectTrigger>
                <SelectValue placeholder="Selecione o status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="ativo">Ativo</SelectItem>
                <SelectItem value="inativo">Inativo</SelectItem>
                <SelectItem value="rascunho">Rascunho</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Button type="submit" className="w-full">
            Salvar Filme
          </Button>
        </form>
      </CardContent>
    </Card>
  );
}
