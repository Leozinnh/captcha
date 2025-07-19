@extends('layout.index')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- Captchas Gerados -->
        <div class="bg-white shadow-lg rounded-xl p-5 border-t-4 border-blue-500 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-700">Captchas Gerados</h3>
                <span class="text-blue-500 text-2xl">🧩</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-900 mt-4">45.678</p>
            <span class="text-sm text-green-600">⬆ +12% este mês</span>
        </div>

        <!-- Taxa de Sucesso -->
        <div class="bg-white shadow-lg rounded-xl p-5 border-t-4 border-green-500 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-700">Taxa de Sucesso</h3>
                <span class="text-green-500 text-2xl">✅</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-900 mt-4">87%</p>
            <span class="text-sm text-green-600">⬆ +3% desde último mês</span>
        </div>

        <!-- Captchas Bloqueados -->
        <div class="bg-white shadow-lg rounded-xl p-5 border-t-4 border-red-500 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-700">Captchas Bloqueados</h3>
                <span class="text-red-500 text-2xl">⛔</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-900 mt-4">1.234</p>
            <span class="text-sm text-red-600">⬆ +15% este mês</span>
        </div>

    </div>


    <!-- Table -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Últimos Captchas</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Tipo</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Data</th>
                        <th class="py-3 px-6 text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-6 font-mono">#9876</td>
                        <td class="py-3 px-6">Texto</td>
                        <td class="py-3 px-6">
                            <span
                                class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Válido</span>
                        </td>
                        <td class="py-3 px-6">17/07/2025 14:32</td>
                        <td class="py-3 px-6 text-center">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">Ver</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-6 font-mono">#9875</td>
                        <td class="py-3 px-6">Grid</td>
                        <td class="py-3 px-6">
                            <span
                                class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Bloqueado</span>
                        </td>
                        <td class="py-3 px-6">17/07/2025 14:12</td>
                        <td class="py-3 px-6 text-center">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">Ver</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-6 font-mono">#9874</td>
                        <td class="py-3 px-6">Drag & Drop</td>
                        <td class="py-3 px-6">
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inválido</span>
                        </td>
                        <td class="py-3 px-6">17/07/2025 13:55</td>
                        <td class="py-3 px-6 text-center">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">Ver</button>
                        </td>
                    </tr>
                    <!-- mais linhas -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
