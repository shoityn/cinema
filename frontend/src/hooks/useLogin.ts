import { useState } from "react"

interface LoginData {
  email: string
  password: string
}

export function useLogin() {
  const [loading, setLoading] = useState(false)

  async function login(data: LoginData) {
    setLoading(true)

    try {
      const baseUrl = process.env.NEXT_PUBLIC_API_URL

      const response = await fetch(`${baseUrl}/api/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })

      if (!response.ok) {
        throw new Error("Credenciais inválidas")
      }

      const result = await response.json()

      // Se sua API retornar token:
      if (result.token) {
        localStorage.setItem("token", result.token)
      }

      return result
    } finally {
      setLoading(false)
    }
  }

  return { login, loading }
}