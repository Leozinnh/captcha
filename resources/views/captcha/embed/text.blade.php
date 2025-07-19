<?php
@extends('captcha.embed.layout')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-4 rounded shadow-md border border-gray-300">
        <div class="flex flex-col items-center space-y-3">
            <div class="relative inline-block cursor-pointer">
                <img src="/captcha/generate-text?token={{ request('token') }}" alt="Captcha" id="captchaImage"
                    class="border border-gray-300 rounded-md transition hover:ring hover:ring-blue-300">
                <div class="absolute inset-0 bg-black bg-opacity-20 text-lg font-md text-white flex items-center justify-center rounded-md opacity-0 hover:opacity-70 transition-opacity backdrop-blur-sm"
                    onclick="refreshCaptcha()">
                    🔄 Trocar imagem
                </div>
            </div>

            <div class="flex flex-row items-center space-x-2 w-full">
                <input type="text" id="captchaInput" placeholder="Digite o código" minlength="4" maxlength="6"
                    class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-700 text-sm placeholder-gray-400">
                <button onclick="validateCaptcha()"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-sm text-white font-semibold py-2 rounded-md shadow-md hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                    Verificar
                </button>
            </div>
        </div>

        <div id="message" class="hidden mt-4 px-4 py-3 rounded-md shadow-md"></div>
    </div>
@endsection

@push('scripts')
<script>
    const CAPTCHA_TOKEN = '{{ request("token") }}';
    
    function refreshCaptcha() {
        document.getElementById('captchaImage').src = '/captcha/generate-text?token=' + CAPTCHA_TOKEN + '&t=' + Date.now();
    }
    
    async function validateCaptcha() {
        const captcha = document.getElementById('captchaInput').value;
        const messageDiv = document.getElementById('message');
        
        try {
            const response = await fetch('/embed/validate/text?token=' + CAPTCHA_TOKEN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Captcha-Token': CAPTCHA_TOKEN
                },
                body: JSON.stringify({ captcha: captcha })
            });
            
            const data = await response.json();
            
            messageDiv.className = `mt-4 px-4 py-3 rounded-md shadow-md ${data.success ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
            messageDiv.textContent = data.message;
            messageDiv.classList.remove('hidden');
            
            notifyValidation(data.success, data.message);
            
            if (!data.success) {
                refreshCaptcha();
                document.getElementById('captchaInput').value = '';
            }
        } catch (error) {
            console.error('Erro:', error);
        }
    }
    
    document.getElementById('captchaInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            validateCaptcha();
        }
    });
</script>
@endpush