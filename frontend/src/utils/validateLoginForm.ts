export interface LoginFormData {
  email: string
  password: string
}

export function validateLoginForm(data: LoginFormData) {
  const errors: Record<string, string> = {}

  if (!data.email.trim()) {
    errors.email = "Email obrigatório"
  } else if (!/\S+@\S+\.\S+/.test(data.email)) {
    errors.email = "Email inválido"
  }

  if (!data.password.trim()) {
    errors.password = "Senha obrigatória"
  }

  return errors
}