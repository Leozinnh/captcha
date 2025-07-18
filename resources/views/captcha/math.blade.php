@extends('layout.index')

@section('content')
    <form method="POST" action="{{ route('captcha.math.validate') }}"
        class="max-w-sm mx-auto bg-white p-4 rounded shadow-md border border-gray-300 flex flex-col justify-between items-center space-x-4">
        @csrf
        <div class="flex flex-row items-center justify-between space-x-4 w-full">
            <div class="flex flex-col items-center space-y-3">
                <div class="relative inline-block cursor-pointer">
                    <img src="{{ route('captcha.math.image') }}" alt="Math Captcha" id="captchaImage"
                        class="border border-gray-300 rounded-md transition hover:ring hover:ring-blue-300 w-40 h-14 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-20 text-sm font-medium text-white flex items-center justify-center rounded-md opacity-0 hover:opacity-70 transition-opacity backdrop-blur-sm"
                        onclick="refreshCaptcha()">
                        🔄 Atualizar
                    </div>
                </div>

                <div class="flex flex-row items-center space-x-2 w-full">
                    <input type="text" name="answer" placeholder="Digite o resultado" minlength="1" maxlength="3"
                        style="min-width: 130px;"
                        class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-700 text-sm placeholder-gray-400">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-sm text-white font-semibold py-2 rounded-md shadow-md hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                        Verificar
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-center text-xs text-gray-500">
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

        @if (session('success'))
            <div class="flex items-center space-x-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md mt-4 w-full"
                role="alert">
                ✅ <span class="font-medium">{{ session('success') }}</span>
            </div>
        @elseif(session('error'))
            <div class="flex items-center space-x-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md shadow-md mt-4 w-full"
                role="alert">
                ❌ <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
    </form>

    <script>
        function refreshCaptcha() {
            const img = document.getElementById('captchaImage');
            img.src = "{{ route('captcha.math.image') }}?" + Date.now();
        }
    </script>
@endsection
