export interface RegisterFormData {
  name: string
  email: string
  password: string
  passwordConfirmation: string
}

export function validateRegisterForm(data: RegisterFormData) {
  const errors: Partial<Record<keyof RegisterFormData, string>> = {}

  if (!data.name.trim()) {
    errors.name = "Nome é obrigatório"
  }

  if (!data.email.trim()) {
    errors.email = "Email é obrigatório"
  } else if (!/\S+@\S+\.\S+/.test(data.email)) {
    errors.email = "Email inválido"
  }

  if (!data.password) {
    errors.password = "Senha é obrigatória"
  } else if (data.password.length < 8) {
    errors.password = "Senha deve ter no mínimo 8 caracteres"
  }

  if (!data.passwordConfirmation) {
    errors.passwordConfirmation = "Confirme a senha"
  } else if (data.password !== data.passwordConfirmation) {
    errors.passwordConfirmation = "As senhas não coincidem"
  }

  return errors
}