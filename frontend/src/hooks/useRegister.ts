import { toast } from "sonner"
import { RegisterFormData } from "@/utils/validateRegisterform"

export function useRegister() {
  async function register(data: RegisterFormData) {
    const baseUrl = process.env.NEXT_PUBLIC_API_URL

    return toast.promise(
      async () => {
        const response = await fetch(`${baseUrl}/api/register`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name: data.name,
            email: data.email,
            password: data.password,
            password_confirmation: data.passwordConfirmation,
          }),
        })

        const result = await response.json()

        if (!response.ok) {
          throw new Error(result?.message || "Erro ao registrar.")
        }

        return result
      },
      {
        loading: "Criando conta...",
        success: "Conta criada com sucesso!",
        error: (err) => err.message,
      }
    )
  }

  return { register }
}