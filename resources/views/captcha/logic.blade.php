@extends('layout.index')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-300">
        <div class="text-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">🧠 Captcha Lógico</h3>
            <p class="text-sm text-gray-600">Responda à pergunta para continuar:</p>
        </div>

        <!-- Categoria da pergunta -->
        <div class="mb-4 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ ucfirst($category) }}
            </span>
        </div>

        <!-- Pergunta -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4 mb-6 text-center">
            <h4 class="text-lg font-bold text-blue-800 mb-2">{{ $question }}</h4>
        </div>

        <!-- Formulário de resposta -->
        <form method="POST" action="{{ route('captcha.logic.validate') }}" class="space-y-4">
            @csrf
            
            <!-- Opções de resposta -->
            <div class="space-y-2">
                @foreach($options as $option)
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" name="answer" value="{{ $option }}" 
                               class="mr-3 text-blue-600 focus:ring-blue-500 focus:ring-2" required>
                        <span class="text-gray-700 font-medium">{{ $option }}</span>
                    </label>
                @endforeach
            </div>

            <!-- Botão de validação -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-3 px-4 rounded-md shadow-md hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                🎯 Verificar Resposta
            </button>
        </form>

        <!-- Mensagens de feedback -->
        @if (session('success'))
            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ session('error') }}
                <button type="button" onclick="location.reload()" class="ml-2 text-sm underline">
                    Nova pergunta
                </button>
            </div>
        @endif

        <!-- Dica -->
        <div class="mt-6 p-3 bg-gray-50 rounded-md">
            <p class="text-xs text-gray-600 text-center">
                💡 <strong>Dica:</strong> Pense com calma e escolha a resposta mais lógica.
            </p>
        </div>

        <!-- Logo leCaptcha -->
        <div class="flex flex-col items-center text-xs text-gray-500 mt-6">
            <img src="/lg.png" alt="leCaptcha Logo" class="w-10 h-10 mb-1">
            <div class="flex items-center text-gray-600 font-bold">
                <b class="font-bold text-blue-600 leCaptcha">le</b><span>Captcha</span>
            </div>
            <div class="flex space-x-1 mt-1">
                <a href="#" class="hover:underline monospace">Privacidade</a>
                <span>&bull;</span>
                <a href="#" class="hover:underline monospace">Termos</a>
            </div>
        </div>
    </div>
@endsection