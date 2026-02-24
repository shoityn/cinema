"use client";

import { useState } from "react";
import axios from "axios";

export default function ForgotPassword() {
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    // 🔹 Garante CSRF cookie
    await axios.get("http://localhost:8000/sanctum/csrf-cookie", {
      withCredentials: true,
    });

    try {
      await axios.post(
        "http://localhost:8000/forgot-password",
        { email },
        { withCredentials: true }
      );

      setMessage("Se o email existir, enviaremos instruções.");
    } catch (err) {
      setMessage("Erro ao processar.");
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h1>Esqueci minha senha</h1>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Seu email"
        required
      />
      <button type="submit">Enviar</button>
      {message && <p>{message}</p>}
    </form>
  );
}