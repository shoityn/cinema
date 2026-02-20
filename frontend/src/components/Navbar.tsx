"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

export default function Navbar() {
  const path = usePathname();

  return (
    <nav className="w-full bg-white/80 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800">
      <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <div className="flex items-center gap-6">
          <Link href="/" className={`font-medium ${path === "/" ? "text-sky-600" : "text-slate-900 dark:text-slate-100"}`}>
            Home
          </Link>

          <Link href="/dashboard" className={`font-medium ${path?.startsWith("/dashboard") ? "text-sky-600" : "text-slate-900 dark:text-slate-100"}`}>
            Dashboard
          </Link>
        </div>

        <div className="flex items-center gap-3">
          <Link href="/login" className="text-sm text-slate-800 dark:text-slate-200">
            Entrar
          </Link>
          <Link href="/register" className="text-sm text-slate-800 dark:text-slate-200">
            Registrar
          </Link>
        </div>
      </div>
    </nav>
  );
}
