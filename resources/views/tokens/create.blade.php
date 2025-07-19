@extends('layout.index')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="mb-6">
            <h1 class="text-3xl font-extrabold text-gray-800">Criar Token</h1>
            <p class="text-gray-500">Crie um novo token para permitir acesso aos captchas</p>
        </div>

        <form action="{{ route('tokens.store') }}" method="POST" class="bg-white shadow-lg rounded-2xl p-8 space-y-6">
            @csrf

            <!-- Nome do Projeto -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700">Nome do Projeto</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="mt-2 block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 focus:border-blue-500 focus:ring focus:ring-blue-100 shadow-sm"
                    required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Domínio Permitido -->
            <div>
                <label for="domain" class="block text-sm font-semibold text-gray-700">Domínio Permitido</label>
                <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
                    placeholder="exemplo.com (opcional)"
                    class="mt-2 block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 focus:border-blue-500 focus:ring focus:ring-blue-100 shadow-sm">
                @error('domain')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipos de Captcha Permitidos -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">Tipos de Captcha Permitidos</label>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($availableTypes as $type => $label)
                        <label
                            class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 shadow-sm hover:bg-gray-100 transition">
                            <input type="checkbox" name="allowed_types[]" value="{{ $type }}"
                                {{ in_array($type, old('allowed_types', [])) ? 'checked' : '' }}
                                class="text-blue-500 focus:ring focus:ring-blue-200 rounded">
                            <span class="ml-3 text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">Deixe vazio para permitir todos os tipos</p>
                @error('allowed_types')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Limite Diário -->
            <div>
                <label for="daily_limit" class="block text-sm font-semibold text-gray-700">Limite Diário</label>
                <input type="number" name="daily_limit" id="daily_limit" value="{{ old('daily_limit', 1000) }}"
                    min="1" max="100000"
                    class="mt-2 block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 focus:border-blue-500 focus:ring focus:ring-blue-100 shadow-sm"
                    required>
                @error('daily_limit')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descrição -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700">Descrição</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-2 block w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 focus:border-blue-500 focus:ring focus:ring-blue-100 shadow-sm"
                    placeholder="Descrição opcional do projeto...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botões -->
            <div class="flex justify-between items-center">
                <a href="{{ route('tokens.index') }}"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-500 text-white font-semibold shadow hover:bg-gray-600 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">
                    Criar Token
                </button>
            </div>
        </form>
    </div>
@endsection
