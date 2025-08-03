@extends('layout.index')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-6 rounded-lg shadow-md border border-gray-300">
        <div class="text-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">🎤 Captcha de Voz</h3>
            <p class="text-sm text-gray-600">Clique no microfone e diga a palavra:</p>
            @if(isset($category))
                <p class="text-xs text-blue-600 font-medium mt-1">
                    📂 Categoria: {{ ucfirst($category) }}
                </p>
            @endif
        </div>

        <!-- Palavra/Número para falar -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4 mb-4 text-center">
            <div class="text-2xl font-bold text-blue-800 mb-2">{{ $challenge }}</div>
            <button onclick="speakWord('{{ $challenge }}')" class="text-sm text-blue-600 hover:text-blue-800">
                🔊 Ouvir palavra
            </button>
        </div>

        <div class="text-center mb-4">
            <button onclick="toggleRecording()" id="recordBtn" class="inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-blue-500 hover:bg-blue-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7 4a3 3 0 616 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 715 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"></path>
                </svg>
                <span id="btnText">Iniciar Gravação</span>
            </button>
        </div>

        <div class="text-center mb-4" style="min-height: 60px;" id="status"></div>

        <form method="POST" action="{{ route('captcha.voice.validate') }}" class="space-y-4" id="captchaForm">
            @csrf
            <input type="hidden" name="spoken_text" id="transcriptionInput">
            <input type="hidden" name="captcha_token" id="captchaTokenInput">

            <button type="submit" id="submitBtn" disabled class="w-full text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800">
                Verificar Captcha
            </button>
        </form>

        <!-- Token Display (apenas para debug - remover em produção) -->
        <div id="tokenDisplay" class="mt-4 p-3 bg-gray-50 rounded-md text-xs text-gray-600" style="display: none;">
            <strong>Token Gerado:</strong> <span id="tokenValue"></span>
        </div>

        @if (session('success'))
            <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
                @if(session('token'))
                    <div class="mt-2 text-sm overflow-x-auto overflow-y-hidden">
                        <strong>Token:</strong> <code class="bg-green-200 px-2 py-1 rounded">{{ session('token') }}</code>
                    </div>
                @endif
            </div>
        @elseif(session('error'))
            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ session('error') }}
                <button type="button" onclick="location.reload()" class="ml-2 text-sm underline">
                    Tentar novamente
                </button>
            </div>
        @endif

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

    <script>
    let isRecording = false;
    let isProcessing = false;
    let transcription = '';
    let recognition = null;
    let isCorrect = false;
    let isValidated = false; // Nova variável para controlar se já foi validado
    let captchaToken = null; // Token único gerado

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎤 Inicializando Voice Captcha...');
        
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert('Seu navegador não suporta reconhecimento de voz. Tente usar Chrome ou Edge.');
            return;
        }

        setupSpeechRecognition();
    });

    function setupSpeechRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();

        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'pt-BR';
        recognition.maxAlternatives = 1;

        recognition.onstart = () => {
            console.log('🔴 Gravação iniciada');
            isRecording = true;
            isProcessing = false;
            updateUI();
        };

        recognition.onresult = (event) => {
            console.log('📝 Resultado recebido:', event.results[0][0].transcript);
            transcription = event.results[0][0].transcript.toLowerCase().trim();
            isProcessing = false;
            isRecording = false;
            checkAnswer();
            updateUI();
        };

        recognition.onerror = (event) => {
            console.error('❌ Erro:', event.error);
            isRecording = false;
            isProcessing = false;
            updateUI();
            alert('Erro no reconhecimento: ' + event.error);
        };

        recognition.onend = () => {
            console.log('⏹️ Gravação finalizada');
            isRecording = false;
            updateUI();
        };
    }

    function toggleRecording() {
        // Se já foi validado, não permite mais gravação
        if (isValidated) {
            alert('Captcha já foi validado com sucesso! Token: ' + captchaToken);
            return;
        }

        if (isRecording) {
            recognition.stop();
        } else {
            isProcessing = true;
            transcription = '';
            isCorrect = false;
            updateUI();
            try {
                recognition.start();
            } catch (error) {
                console.error('Erro:', error);
                isProcessing = false;
                updateUI();
                alert('Erro ao iniciar gravação');
            }
        }
    }

    function speakWord(word) {
        // Se já foi validado, não permite mais usar o botão de falar
        if (isValidated) {
            return;
        }

        if ('speechSynthesis' in window) {
            speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(word);
            utterance.lang = 'pt-BR';
            utterance.rate = 0.8;
            speechSynthesis.speak(utterance);
        }
    }

    function checkAnswer() {
        const correct = '{{ $challenge }}'.toLowerCase().trim();
        const spoken = transcription.toLowerCase().trim();
        
        // Verifica equivalência (números vs palavras)
        if (areEquivalent(spoken, correct)) {
            isCorrect = true;
            console.log('✅ Resposta equivalente!');
            return;
        }
        
        // Verifica similaridade
        const similarity = calculateSimilarity(spoken, correct);
        isCorrect = similarity >= 0.7;
        console.log('Verificação:', spoken, 'vs', correct, '= correto:', isCorrect, 'similaridade:', similarity.toFixed(2));
    }

    function areEquivalent(text1, text2) {
        const numberMap = {
            '0': ['zero'],
            '1': ['um', 'uma'],
            '2': ['dois', 'duas'],
            '3': ['três', 'tres'],
            '4': ['quatro'],
            '5': ['cinco'],
            '6': ['seis'],
            '7': ['sete'],
            '8': ['oito'],
            '9': ['nove']
        };
        
        text1 = text1.toLowerCase().trim();
        text2 = text2.toLowerCase().trim();
        
        // Se são iguais
        if (text1 === text2) {
            return true;
        }
        
        // Verifica se um é número e outro é palavra equivalente
        for (const [number, words] of Object.entries(numberMap)) {
            if (text1 === number && words.includes(text2)) {
                return true;
            }
            if (text2 === number && words.includes(text1)) {
                return true;
            }
            if (words.includes(text1) && words.includes(text2)) {
                return true;
            }
        }
        
        return false;
    }

    function generateCaptchaToken() {
        // Gera token único baseado em timestamp + random + hash da resposta
        const timestamp = Date.now();
        const random = Math.random().toString(36).substring(2, 15);
        const challenge = '{{ $challenge }}';
        const tokenData = `${timestamp}-${random}-${challenge}-${transcription}`;
        
        // Simula hash (em produção, use uma biblioteca crypto)
        captchaToken = btoa(tokenData).replace(/[^a-zA-Z0-9]/g, '').substring(0, 32);
        
        console.log('🔑 Token gerado:', captchaToken);
        
        // Preenche o input hidden
        document.getElementById('captchaTokenInput').value = captchaToken;
        
        // Mostra token para debug (remover em produção)
        const tokenDisplay = document.getElementById('tokenDisplay');
        const tokenValue = document.getElementById('tokenValue');
        tokenValue.textContent = captchaToken;
        tokenDisplay.style.display = 'block';

        // Auto-submit após 3 segundos se correto
        setTimeout(() => {
            if (isCorrect && !isValidated) {
                console.log('🚀 Auto-submetendo formulário...');
                document.getElementById('captchaForm').submit();
            }
        }, 3000);
    }

    function calculateSimilarity(str1, str2) {
        const len1 = str1.length;
        const len2 = str2.length;
        const matrix = Array(len1 + 1).fill().map(() => Array(len2 + 1).fill(0));
        
        for (let i = 0; i <= len1; i++) matrix[i][0] = i;
        for (let j = 0; j <= len2; j++) matrix[0][j] = j;
        
        for (let i = 1; i <= len1; i++) {
            for (let j = 1; j <= len2; j++) {
                const cost = str1[i - 1] === str2[j - 1] ? 0 : 1;
                matrix[i][j] = Math.min(
                    matrix[i - 1][j] + 1,
                    matrix[i][j - 1] + 1,
                    matrix[i - 1][j - 1] + cost
                );
            }
        }
        
        return (Math.max(len1, len2) - matrix[len1][len2]) / Math.max(len1, len2);
    }

    function updateUI() {
        const recordBtn = document.getElementById('recordBtn');
        const speakBtn = document.getElementById('speakBtn');
        const btnText = document.getElementById('btnText');
        const status = document.getElementById('status');
        const submitBtn = document.getElementById('submitBtn');
        const transcriptionInput = document.getElementById('transcriptionInput');

        // Se já foi validado, desabilita tudo
        if (isValidated) {
            recordBtn.disabled = true;
            speakBtn.disabled = true;
            recordBtn.className = 'inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-gray-400 cursor-not-allowed opacity-50';
            btnText.textContent = 'Captcha Validado ✅';
            speakBtn.className = 'text-sm text-gray-400 cursor-not-allowed';
            status.innerHTML = '<div class="text-green-600 font-medium bg-green-50 p-3 rounded-md">🎉 Captcha validado com sucesso!<br><small>Token: ' + captchaToken + '</small></div>';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Validado ✅';
            submitBtn.className = 'w-full text-white font-semibold py-2 px-4 rounded-md shadow-md transition bg-green-600 opacity-75 cursor-not-allowed';
            return;
        }

        if (isRecording) {
            recordBtn.className = 'inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-red-500 hover:bg-red-600';
            btnText.textContent = 'Parar Gravação';
            status.innerHTML = '<div class="text-red-600 font-medium">🔴 Gravando... Diga: "{{ $challenge }}"</div>';
            submitBtn.disabled = true;
        } else if (isProcessing) {
            recordBtn.disabled = true;
            btnText.textContent = 'Processando...';
            status.innerHTML = '<div class="text-blue-600 font-medium">⏳ Processando sua fala...</div>';
            submitBtn.disabled = true;
        } else if (transcription) {
            recordBtn.className = 'inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-blue-500 hover:bg-blue-600';
            recordBtn.disabled = false;
            btnText.textContent = 'Gravar Novamente';
            
            if (isCorrect) {
                status.innerHTML = '<div class="text-green-600 font-medium bg-green-50 p-3 rounded-md">✅ Correto! Você disse: "' + transcription + '"<br><small>Auto-enviando em 3 segundos...</small></div>';
                submitBtn.className = 'w-full text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 animate-pulse';
                submitBtn.textContent = 'Finalizar ✅';
                
                // Desabilita nova gravação após resposta correta
                recordBtn.disabled = true;
                recordBtn.className = 'inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-gray-400 cursor-not-allowed opacity-50';
                btnText.textContent = 'Resposta Correta ✅';
            } else {
                status.innerHTML = '<div class="text-red-600 font-medium bg-red-50 p-3 rounded-md">❌ Incorreto. Você disse: "' + transcription + '"<br><small>Palavra esperada: "{{ $challenge }}"</small></div>';
                submitBtn.textContent = 'Tentar Novamente ❌';
            }
            
            submitBtn.disabled = false;
            transcriptionInput.value = transcription;
        } else {
            recordBtn.className = 'inline-flex items-center px-6 py-3 rounded-full text-white font-semibold shadow-lg transition-all duration-300 bg-blue-500 hover:bg-blue-600';
            recordBtn.disabled = false;
            btnText.textContent = 'Iniciar Gravação';
            status.innerHTML = '';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verificar Captcha';
        }
    }

    // Intercepta envio do formulário para marcar como validado
    document.getElementById('captchaForm').addEventListener('submit', function(e) {
        if (isCorrect && captchaToken) {
            isValidated = true;
            console.log('📤 Enviando formulário com token:', captchaToken);
        }
    });
    </script>
@endsection
