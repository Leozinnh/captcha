<?php
// filepath: app/Http/Middleware/ValidateCaptchaToken.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CaptchaToken;

class ValidateCaptchaToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Captcha-Token')
            ?? $request->get('token')
            ?? $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'Token de autenticação obrigatório',
                'code' => 'MISSING_TOKEN'
            ], 401);
        }

        $captchaToken = CaptchaToken::where('token', $token)
            ->where('active', true)
            ->first();

        if (!$captchaToken) {
            return response()->json([
                'error' => 'Token inválido ou inativo',
                'code' => 'INVALID_TOKEN'
            ], 401);
        }

        // Verificar limite diário
        if (!$captchaToken->canMakeRequest()) {
            return response()->json([
                'error' => 'Limite diário de requisições excedido',
                'code' => 'RATE_LIMIT_EXCEEDED'
            ], 429);
        }

        // Verificar domínio (opcional)
        $origin = $request->header('Origin') ?? $request->header('Referer');
        if ($origin && $captchaToken->domain !== '*') {
            $domain = parse_url($origin, PHP_URL_HOST);
            if ($domain !== $captchaToken->domain) {
                return response()->json([
                    'error' => 'Domínio não autorizado',
                    'code' => 'UNAUTHORIZED_DOMAIN'
                ], 403);
            }
        }

        // Verificar tipo de captcha permitido
        $type = $request->route('type') ?? $request->segment(2);
        if ($type && !$captchaToken->canUseType($type)) {
            return response()->json([
                'error' => 'Tipo de captcha não autorizado',
                'code' => 'UNAUTHORIZED_TYPE'
            ], 403);
        }

        // Incrementar uso
        $captchaToken->incrementUsage();

        // Adicionar token ao request para uso posterior
        $request->merge(['captcha_token' => $captchaToken]);

        return $next($request);
    }
}
