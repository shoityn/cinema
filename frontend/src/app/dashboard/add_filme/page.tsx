

import { AddFilmeForm } from "@/components/forms/AddFilmeForm"

export default function AddFilmePage() {
  return (
    <div className="container mx-auto py-10">
      <div className="mb-6">
        <h1 className="text-2xl font-semibold">Adicionar Filme</h1>
        <p className="text-muted-foreground">
          Preencha os dados para cadastrar um novo filme.
        </p>
      </div>

      <AddFilmeForm />
    </div>
  )
}

