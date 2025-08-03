<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaptchaToken;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = CaptchaToken::latest()->paginate(10);
        return view('tokens.index', compact('tokens'));
    }

    public function create()
    {
        $availableTypes = [
            'text' => 'Captcha de Texto',
            'robot' => 'Captcha Robot',
            'math' => 'Captcha Matemático',
            'grid' => 'Captcha de Grade',
            'dragdrop' => 'Captcha Drag & Drop',
            'voice' => 'Captcha de Voz'
        ];
        
        return view('tokens.create', compact('availableTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'allowed_types' => 'nullable|array',
            'allowed_types.*' => 'in:text,robot,math,grid,dragdrop',
            'daily_limit' => 'required|integer|min:1|max:100000',
            'description' => 'nullable|string|max:1000'
        ]);

        CaptchaToken::create($request->all());

        return redirect()->route('tokens.index')
            ->with('success', 'Token criado com sucesso!');
    }

    public function show(CaptchaToken $token)
    {
        return view('tokens.show', compact('token'));
    }

    public function edit(CaptchaToken $token)
    {
        $availableTypes = [
            'text' => 'Captcha de Texto',
            'robot' => 'Captcha Robot',
            'math' => 'Captcha Matemático',
            'grid' => 'Captcha de Grade',
            'dragdrop' => 'Captcha Drag & Drop',
            'voice' => 'Captcha de Voz'
        ];
        
        return view('tokens.edit', compact('token', 'availableTypes'));
    }

    public function update(Request $request, CaptchaToken $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255',
            'allowed_types' => 'nullable|array',
            'allowed_types.*' => 'in:text,robot,math,grid,dragdrop',
            'daily_limit' => 'required|integer|min:1|max:100000',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        $token->update($request->all());

        return redirect()->route('tokens.index')
            ->with('success', 'Token atualizado com sucesso!');
    }

    public function destroy(CaptchaToken $token)
    {
        $token->delete();
        
        return redirect()->route('tokens.index')
            ->with('success', 'Token excluído com sucesso!');
    }

    public function regenerate(CaptchaToken $token)
    {
        $token->update([
            'token' => CaptchaToken::generateUniqueToken()
        ]);

        return redirect()->route('tokens.show', $token)
            ->with('success', 'Token regenerado com sucesso!');
    }

    public function toggleStatus(CaptchaToken $token)
    {
        $token->update([
            'is_active' => !$token->is_active
        ]);

        $status = $token->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
            ->with('success', "Token {$status} com sucesso!");
    }
}