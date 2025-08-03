@extends('captcha.embed.layout')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-4 rounded shadow-md border border-gray-300" x-data="voiceCaptchaEmbed()">
        <div class="text-center mb-4">
            <h3 class="text-md font-semibold text-gray-800 mb-1">🎤 Captcha de Voz</h3>
            <p class="text-xs text-gray-600">Diga a palavra:</p>
        </div>

        <!-- Palavra para falar -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3 text-center">
            <div class="text-xl font-bold text-blue-800 mb-1">{{ $challenge }}</div>
            <button onclick="speakWord('{{ $challenge }}')" 
                    class="text-xs text-blue-600 hover:text-blue-800">
                🔊 Ouvir
            </button>
        </div>

        <!-- Controles -->
        <div class="text-center mb-3">
            <button @click="toggleRecording()" 
                    :class="isRecording ? 'bg-red-500' : 'bg-blue-500'"
                    class="inline-flex items-center px-4 py-2 rounded-full text-white text-sm font-semibold shadow-md transition-all">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="isRecording ? 'Parar' : 'Gravar'"></span>
            </button>
        </div>

        <!-- Status -->
        <div class="text-center mb-3 text-xs">
            <div x-show="isRecording" class="text-red-600">🔴 Gravando...</div>
            <div x-show="isProcessing" class="text-blue-600">⏳ Processando...</div>
            <div x-show="transcription && !isRecording" class="text-gray-600">
                Você disse: "<span x-text="transcription" class="font-semibold"></span>"
            </div>
        </div>

        <!-- Botão validar -->
        <button @click="validateVoice()" :disabled="!transcription || isRecording"
                class="w-full bg-green-500 hover:bg-green-600 text-white text-sm font-semibold py-2 rounded-md shadow transition disabled:opacity-50">
            Verificar
        </button>

        <div id="message" class="hidden mt-3 px-3 py-2 rounded text-sm"></div>
    </div>
@endsection

@push('scripts')
<script>
    const CAPTCHA_TOKEN = '{{ request("token") }}';
    
    function voiceCaptchaEmbed() {
        return {
            isRecording: false,
            isProcessing: false,
            transcription: '',
            recognition: null,

            init() {
                if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                    this.showMessage('Seu navegador não suporta reconhecimento de voz.', false);
                    return;
                }

                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                this.recognition = new SpeechRecognition();
                
                this.recognition.continuous = false;
                this.recognition.interimResults = false;
                this.recognition.lang = 'pt-BR';

                this.recognition.onstart = () => {
                    this.isRecording = true;
                    this.isProcessing = false;
                };

                this.recognition.onresult = (event) => {
                    this.transcription = event.results[0][0].transcript.toLowerCase().trim();
                    this.isProcessing = false;
                };

                this.recognition.onerror = (event) => {
                    this.isRecording = false;
                    this.isProcessing = false;
                    this.showMessage('Erro no reconhecimento de voz.', false);
                };

                this.recognition.onend = () => {
                    this.isRecording = false;
                };
            },

            toggleRecording() {
                if (this.isRecording) {
                    this.recognition.stop();
                } else {
                    this.isProcessing = true;
                    this.transcription = '';
                    this.recognition.start();
                }
            },

            async validateVoice() {
                if (!this.transcription) return;

                try {
                    const response = await fetch('/embed/validate/voice?token=' + CAPTCHA_TOKEN, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Captcha-Token': CAPTCHA_TOKEN
                        },
                        body: JSON.stringify({ spoken_text: this.transcription })
                    });
                    
                    const data = await response.json();
                    this.showMessage(data.message, data.success);
                    notifyValidation(data.success, data.message);
                    
                } catch (error) {
                    this.showMessage('Erro de conexão.', false);
                }
            },

            showMessage(message, success) {
                const messageDiv = document.getElementById('message');
                messageDiv.className = `mt-3 px-3 py-2 rounded text-sm ${success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
                messageDiv.textContent = message;
                messageDiv.classList.remove('hidden');
            }
        }
    }

    function speakWord(word) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(word);
            utterance.lang = 'pt-BR';
            utterance.rate = 0.8;
            speechSynthesis.speak(utterance);
        }
    }
</script>
@endpush