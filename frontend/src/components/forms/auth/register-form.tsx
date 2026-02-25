"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { toast } from "sonner";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

import { validateRegisterForm } from "@/utils/validateRegisterform";
import { useRegister } from "@/hooks/useRegister";

export function RegisterForm() {
  const router = useRouter();
  const { register } = useRegister();

  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(false);
  const [focusedField, setFocusedField] = useState<string | null>(null);

  async function handleRegister(e: React.FormEvent) {
    e.preventDefault();

    const validationErrors = validateRegisterForm({
      name,
      email,
      password,
      passwordConfirmation,
    });

    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors as Record<string, string>);
      toast.warning("Corrija os campos destacados.");
      return;
    }

    setErrors({});
    setLoading(true);

    try {
      await register({
        name,
        email,
        password,
        passwordConfirmation,
      });

      router.push("/login");
    } catch (error) {
      toast.error("Erro ao criar conta.");
    } finally {
      setLoading(false);
    }
  }

  function getInputClass(field: string) {
    if (errors[field] && focusedField !== field) {
      return "border-red-500";
    }
    return "";
  }

  function clearFieldError(field: string) {
    if (errors[field]) {
      setErrors((prev) => {
        const updated = { ...prev };
        delete updated[field];
        return updated;
      });
    }
  }

  return (
    <Card className="w-full max-w-md">
      <CardHeader>
        <CardTitle>Criar Conta</CardTitle>
      </CardHeader>

      <CardContent>
        <form onSubmit={handleRegister} className="space-y-4">
          <div className="space-y-2">
            <Label>Nome</Label>
            <Input
              value={name}
              placeholder="Nome"
              onChange={(e) => {
                setName(e.target.value);
                clearFieldError("name");
              }}
              onFocus={() => setFocusedField("name")}
              onBlur={() => setFocusedField(null)}
              className={getInputClass("name")}
            />
          </div>

          <div className="space-y-2">
            <Label>Email</Label>
            <Input
              type="email"
              value={email}
              placeholder="Email"
              onChange={(e) => {
                setEmail(e.target.value);
                clearFieldError("email");
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
              value={password}
              placeholder="********"
              onChange={(e) => {
                setPassword(e.target.value);
                clearFieldError("password");
              }}
              onFocus={() => setFocusedField("password")}
              onBlur={() => setFocusedField(null)}
              className={getInputClass("password")}
            />
          </div>

          <div className="space-y-2">
            <Label>Confirmar Senha</Label>
            <Input
              type="password"
              value={passwordConfirmation}
              placeholder="********"
              onChange={(e) => {
                setPasswordConfirmation(e.target.value);
                clearFieldError("passwordConfirmation");
              }}
              onFocus={() => setFocusedField("passwordConfirmation")}
              onBlur={() => setFocusedField(null)}
              className={getInputClass("passwordConfirmation")}
            />
          </div>

          <Button type="submit" className="w-full" disabled={loading}>
            {loading ? "Criando..." : "Criar Conta"}
          </Button>
        </form>
      </CardContent>
    </Card>
  );
}