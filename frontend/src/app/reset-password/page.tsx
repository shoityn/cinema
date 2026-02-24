"use client";

import { useSearchParams } from "next/navigation";
import { useState } from "react";
import axios from "axios";

export default function ResetPassword() {
  const params = useSearchParams();
  const token = params.get("token");
  const email = params.get("email");

  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [message, setMessage] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    await axios.get("http://localhost:8000/sanctum/csrf-cookie", {
      withCredentials: true,
    });

    try {
      await axios.post(
        "http://localhost:8000/reset-password",
        {
          token,
          email,
          password,
          password_confirmation: passwordConfirmation,
        },
        { withCredentials: true }
      );

      setMessage("Senha redefinida com sucesso.");
    } catch {
      setMessage("Token inválido ou expirado.");
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h1>Redefinir senha</h1>
      <input type="email" value={email || ""} disabled />
      <input
        type="password"
        placeholder="Nova senha"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        required
      />
      <input
        type="password"
        placeholder="Confirmar senha"
        value={passwordConfirmation}
        onChange={(e) => setPasswordConfirmation(e.target.value)}
        required
      />
      <button type="submit">Redefinir</button>
      {message && <p>{message}</p>}
    </form>
  );
}