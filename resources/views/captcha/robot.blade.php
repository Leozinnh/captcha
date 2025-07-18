@extends('layout.index')

@section('content')
    <div
        class="max-w-sm mx-auto bg-white p-4 rounded shadow-md border border-gray-300 flex justify-between items-center space-x-4">
        <div class="flex flex-col space-y-1">
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 transition-all duration-100 ease-in-out">
                    <div id="checkboxWrapper" class="w-5 h-5 relative transition-all duration-100 ease-in-out scale-[1.5]">
                        <input type="checkbox" id="imnotrobot" class="peer hidden">
                        <label id="checkboxLabel" for="imnotrobot"
                            class="w-5 h-5 inline-flex justify-center items-center border-2 border-gray-400 rounded text-transparent transition-colors bg-white cursor-pointer select-none">
                            ✓
                        </label>
                    </div>

                    <img id="loading" src="/images/loading.gif" alt="Carregando..."
                        class="w-8 h-8 ml-2 hidden transition-all duration-100 ease-in-out">

                    <svg id="successIcon" xmlns="http://www.w3.org/2000/svg" class="hidden w-10 h-10 text-green-600 ml-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>

                    <span class="text-gray-700 text-md font-medium pl-2 select-none">Não sou um robô</span>
                </div>
            </div>

            <div id="errorMessage"
                class="hidden mt-2 text-red-700 text-sm rounded flex items-center gap-2 duration-300 ease-in-out opacity-0">
                <span id="errorText">Erro de conexão. Tente novamente.</span>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxWrapper = document.getElementById('checkboxWrapper');
            const checkboxLabel = document.getElementById('checkboxLabel');
            const loading = document.getElementById('loading');
            const successIcon = document.getElementById('successIcon');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = errorMessage.querySelector('#errorText');

            let locked = false;

            checkboxLabel.addEventListener('click', function() {
                if (locked) return;

                hideError();

                checkboxWrapper.classList.add('hidden');
                loading.classList.remove('hidden');

                fetch('/captcha/validate-robot', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');

                        if (data.success) {
                            successIcon.classList.remove('hidden');
                            locked = true;
                        } else {
                            checkboxWrapper.classList.remove('hidden');
                            showError(data.message || 'Falha na validação. Tente novamente.');
                        }
                    })
                    .catch(() => {
                        loading.classList.add('hidden');
                        checkboxWrapper.classList.remove('hidden');
                        showError('Erro de conexão. Tente novamente.');
                    });
            });

            function showError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden', 'opacity-0');
                errorMessage.classList.add('opacity-100');

                setTimeout(() => {
                    hideError();
                }, 5000);
            }

            function hideError() {
                errorMessage.classList.add('opacity-0');
                setTimeout(() => {
                    errorMessage.classList.add('hidden');
                }, 300);
            }
        });
    </script>
@endsection
