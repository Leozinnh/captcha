@extends('layout.index')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-800">Tokens de Captcha</h1>
            <a href="{{ route('tokens.create') }}"
                class="inline-flex items-center px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition">
                + Criar Token
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Domínio</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Uso Diário
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tokens as $token)
                        <tr class="hover:bg-gray-50 transition duration-200">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $token->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($token->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $token->domain ?: 'Qualquer domínio' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="font-medium">{{ $token->usage_count }}</span> / <span
                                    class="text-gray-500">{{ $token->daily_limit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                {{ $token->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $token->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ route('tokens.show', $token) }}"
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-800 transition text-sm font-medium shadow-sm">
                                        Ver
                                    </a>
                                    <a href="{{ route('tokens.edit', $token) }}"
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-yellow-600 hover:bg-yellow-50 hover:text-yellow-800 transition text-sm font-medium shadow-sm">
                                        Editar
                                    </a>
                                    <form action="{{ route('tokens.destroy', $token) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este token?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-800 transition text-sm font-medium shadow-sm">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">
                                Nenhum token encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $tokens->links() }}
        </div>
    </div>
@endsection
