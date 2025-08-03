@extends('captcha.embed.layout')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-4 rounded shadow-md border border-gray-300">
        <div class="text-center mb-4">
            <h3 class="text-md font-semibold text-gray-800 mb-1">🧠 Logic Captcha</h3>
            <p class="text-xs text-gray-600">Responda à pergunta:</p>
        </div>

        <!-- Categoria -->
        <div class="mb-3 text-center">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ ucfirst($category) }}
            </span>
        </div>

        <!-- Pergunta -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-center">
            <h4 class="text-sm font-bold text-blue-800">{{ $question }}</h4>
        </div>

        <!-- Opções -->
        <div class="space-y-2 mb-4" id="options">
            @foreach($options as $index => $option)
                <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="{{ $option }}" 
                           class="mr-2 text-blue-600" onchange="selectAnswer('{{ $option }}')">
                    <span class="text-xs text-gray-700">{{ $option }}</span>
                </label>
            @endforeach
        </div>

        <!-- Botão -->
        <button onclick="validateLogic()" id="validateBtn" disabled
                class="w-full bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-2 rounded shadow transition disabled:opacity-50">
            Verificar
        </button>

        <div id="message" class="hidden mt-3 px-3 py-2 rounded text-sm"></div>
    </div>
@endsection

@push('scripts')
<script>
    const CAPTCHA_TOKEN = '{{ request("token") }}';
    let selectedAnswer = '';
    
    function selectAnswer(answer) {
        selectedAnswer = answer;
        document.getElementById('validateBtn').disabled = false;
    }
    
    async function validateLogic() {
        if (!selectedAnswer) return;
        
        try {
            const response = await fetch('/embed/validate/logic?token=' + CAPTCHA_TOKEN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Captcha-Token': CAPTCHA_TOKEN
                },
                body: JSON.stringify({ 
                    answer: selectedAnswer,
                    type: 'logic'
                })
            });
            
            const data = await response.json();
            showMessage(data.message, data.success);
            notifyValidation(data.success, data.message);
            
        } catch (error) {
            showMessage('Erro de conexão.', false);
        }
    }
    
    function showMessage(message, success) {
        const messageDiv = document.getElementById('message');
        messageDiv.className = `mt-3 px-3 py-2 rounded text-sm ${success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
        messageDiv.textContent = message;
        messageDiv.classList.remove('hidden');
    }
</script>
@endpush