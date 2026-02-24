<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {

            // Revoga todos os tokens
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Usuário desativado.'
            ], 403);
        }

        return $next($request);
    }
}