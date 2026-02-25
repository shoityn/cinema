"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { toast } from "sonner"

import { validateLoginForm } from "@/utils/validateLoginForm"
import { useLogin } from "@/hooks/useLogin"

export function LoginForm() {
  const router = useRouter()
  const { login, loading } = useLogin()

  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [focusedField, setFocusedField] = useState<string | null>(null)

  function getInputClass(field: string) {
    if (errors[field] && focusedField !== field) {
      return "border-red-500"
    }
    return ""
  }

  function clearFieldError(field: string) {
    if (errors[field]) {
      setErrors((prev) => {
        const updated = { ...prev }
        delete updated[field]
        return updated
      })
    }
  }

  async function handleLogin(e: React.FormEvent) {
    e.preventDefault()

    const validationErrors = validateLoginForm({ email, password })

    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors)
      toast.warning("Corrija os campos destacados.")
      return
    }

    try {
      await login({ email, password })
      router.push("/dashboard")
    } catch (error) {
      toast.error("Credenciais inválidas")
    }
  }

  return (
    <Card className="w-full max-w-md">
      <CardHeader>
        <CardTitle>Login</CardTitle>
      </CardHeader>

      <CardContent>
        <form onSubmit={handleLogin} className="space-y-4">
          <div className="space-y-2">
            <Label>Email</Label>
            <Input
              type="email"
              placeholder="email@email.com"
              value={email}
              onChange={(e) => {
                setEmail(e.target.value)
                clearFieldError("email")
              }}
              onFocus={() => setFocusedField("email")}
              onBlur={() => setFocusedField(null)}
              className={getInputClass("email")}
            />
          </div>

          <div className="space-y-2">
            <Label>Senha</Label>
            <Input
              type="password"
              placeholder="********"
              value={password}
              onChange={(e) => {
                setPassword(e.target.value)
                clearFieldError("password")
              }}
              onFocus={() => setFocusedField("password")}
              onBlur={() => setFocusedField(null)}
              className={getInputClass("password")}
            />
          </div>

          <Button type="submit" className="w-full" disabled={loading}>
            {loading ? "Entrando..." : "Entrar"}
          </Button>
        </form>
      </CardContent>
    </Card>
  )
}