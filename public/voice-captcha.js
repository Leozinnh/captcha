(function() {
    'use strict';
    
    class VoiceCaptcha {
        constructor(container, options = {}) {
            this.container = container;
            this.options = {
                token: options.token || '',
                baseUrl: options.baseUrl || '',
                language: options.language || 'pt-BR',
                ...options
            };
            
            this.isRecording = false;
            this.isProcessing = false;
            this.transcription = '';
            this.recognition = null;
            this.currentChallenge = '';
            
            this.init();
        }
        
        init() {
            this.checkBrowserSupport();
            this.setupSpeechRecognition();
            this.generateChallenge();
            this.render();
        }
        
        checkBrowserSupport() {
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                this.container.innerHTML = '<div class="error">Seu navegador não suporta reconhecimento de voz.</div>';
                return false;
            }
            return true;
        }
        
        setupSpeechRecognition() {
            // Só configura se o navegador suporta
            if (!this.checkBrowserSupport()) {
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = this.options.language;
            this.recognition.maxAlternatives = 1;
            
            this.recognition.onstart = () => {
                console.log('Reconhecimento iniciado');
                this.isRecording = true;
                this.isProcessing = false; // Reset processing quando começar
                this.updateUI();
            };
            
            this.recognition.onresult = (event) => {
                console.log('Resultado recebido:', event.results[0][0].transcript);
                this.transcription = event.results[0][0].transcript.toLowerCase().trim();
                this.isProcessing = false;
                this.isRecording = false;
                
                // Verifica automaticamente se a resposta está correta
                this.checkAnswer();
                this.updateUI();
            };
            
            this.recognition.onerror = (event) => {
                console.error('Erro no reconhecimento:', event.error);
                this.isRecording = false;
                this.isProcessing = false;
                
                let errorMessage = 'Erro no reconhecimento de voz.';
                switch(event.error) {
                    case 'no-speech':
                        errorMessage = 'Nenhuma fala detectada. Tente falar mais alto.';
                        break;
                    case 'audio-capture':
                        errorMessage = 'Erro ao acessar o microfone.';
                        break;
                    case 'not-allowed':
                        errorMessage = 'Permissão do microfone negada.';
                        break;
                    case 'network':
                        errorMessage = 'Erro de rede. Verifique sua conexão.';
                        break;
                }
                
                this.showError(errorMessage);
                this.updateUI();
            };
            
            this.recognition.onend = () => {
                console.log('Reconhecimento finalizado');
                this.isRecording = false;
                this.isProcessing = false;
                this.updateUI();
            };
        }
        
        generateChallenge() {
            const challenges = [
                'casa', 'livro', 'gato', 'água', 'sol', 'lua', 
                'verde', 'azul', 'branco', 'preto', 'um', 'dois', 
                'três', 'quatro', 'cinco', 'seis', 'sete', 'oito'
            ];
            this.currentChallenge = challenges[Math.floor(Math.random() * challenges.length)];
        }
        
        render() {
            // Só renderiza se suportar reconhecimento de voz
            if (!this.checkBrowserSupport()) {
                return;
            }

            this.container.innerHTML = `
                <div class="voice-captcha-widget">
                    <style>
                        .voice-captcha-widget {
                            max-width: 300px;
                            background: white;
                            border: 1px solid #d1d5db;
                            border-radius: 8px;
                            padding: 16px;
                            font-family: system-ui, -apple-system, sans-serif;
                        }
                        .challenge-word {
                            background: linear-gradient(to right, #dbeafe, #e0e7ff);
                            border: 2px solid #3b82f6;
                            border-radius: 8px;
                            padding: 12px;
                            text-align: center;
                            margin-bottom: 12px;
                        }
                        .challenge-word h3 {
                            font-size: 18px;
                            font-weight: bold;
                            color: #1e40af;
                            margin: 0 0 8px 0;
                        }
                        .speak-btn {
                            background: none;
                            border: none;
                            color: #2563eb;
                            cursor: pointer;
                            font-size: 12px;
                        }
                        .record-btn {
                            display: inline-flex;
                            align-items: center;
                            padding: 8px 16px;
                            border: none;
                            border-radius: 20px;
                            color: white;
                            font-size: 14px;
                            font-weight: 600;
                            cursor: pointer;
                            transition: all 0.3s;
                            margin-bottom: 12px;
                        }
                        .record-btn:disabled {
                            opacity: 0.6;
                            cursor: not-allowed;
                        }
                        .record-btn.recording {
                            background-color: #dc2626;
                        }
                        .record-btn.idle {
                            background-color: #3b82f6;
                        }
                        .validate-btn {
                            width: 100%;
                            padding: 8px;
                            background-color: #10b981;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 600;
                            cursor: pointer;
                            transition: all 0.3s;
                        }
                        .validate-btn:disabled {
                            opacity: 0.5;
                            cursor: not-allowed;
                        }
                        .validate-btn-correct {
                            background-color: #10b981 !important;
                            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
                            animation: pulse 2s infinite;
                        }
                        .validate-btn-incorrect {
                            background-color: #f59e0b !important;
                        }
                        .validate-btn-success {
                            background-color: #059669 !important;
                        }
                        .validate-btn-error {
                            background-color: #dc2626 !important;
                        }
                        @keyframes pulse {
                            0%, 100% { transform: scale(1); }
                            50% { transform: scale(1.02); }
                        }
                        .status {
                            text-align: center;
                            font-size: 12px;
                            margin-bottom: 12px;
                            min-height: 16px;
                        }
                        .transcription {
                            color: #374151;
                        }
                        .recording {
                            color: #dc2626;
                        }
                        .processing {
                            color: #3b82f6;
                        }
                        .correct-answer {
                            color: #065f46;
                            font-weight: 600;
                            background-color: #d1fae5;
                            padding: 6px;
                            border-radius: 4px;
                            display: inline-block;
                        }
                        .incorrect-answer {
                            color: #991b1b;
                            font-weight: 600;
                            background-color: #fee2e2;
                            padding: 6px;
                            border-radius: 4px;
                            display: inline-block;
                        }
                        .message {
                            margin-top: 12px;
                            padding: 8px;
                            border-radius: 4px;
                            font-size: 12px;
                            text-align: center;
                        }
                        .message.success {
                            background-color: #d1fae5;
                            color: #065f46;
                        }
                        .message.error {
                            background-color: #fee2e2;
                            color: #991b1b;
                        }
                        .error {
                            background-color: #fee2e2;
                            color: #991b1b;
                            padding: 12px;
                            border-radius: 8px;
                            text-align: center;
                            font-size: 14px;
                        }
                    </style>
                    
                    <div class="text-center" style="margin-bottom: 12px;">
                        <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 4px 0;">🎤 Voice Captcha</h3>
                        <p style="font-size: 12px; color: #6b7280; margin: 0;">Diga a palavra:</p>
                    </div>
                    
                    <div class="challenge-word">
                        <h3>${this.currentChallenge}</h3>
                        <button class="speak-btn" onclick="this.parentNode.parentNode.voiceCaptcha.speakWord()">
                            🔊 Ouvir palavra
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <button class="record-btn idle" onclick="this.parentNode.parentNode.voiceCaptcha.toggleRecording()">
                            🎤 <span class="btn-text">Iniciar Gravação</span>
                        </button>
                    </div>
                    
                    <div class="status"></div>
                    
                    <button class="validate-btn" onclick="this.parentNode.voiceCaptcha.validateVoice()" disabled>
                        Verificar Captcha
                    </button>
                    
                    <div class="message" style="display: none;"></div>
                </div>
            `;
            
            // Referência para acessar métodos
            this.container.querySelector('.voice-captcha-widget').voiceCaptcha = this;
        }
        
        toggleRecording() {
            // Verifica se o reconhecimento está disponível
            if (!this.recognition) {
                this.showError('Reconhecimento de voz não disponível.');
                return;
            }

            if (this.isRecording) {
                console.log('Parando gravação...');
                this.recognition.stop();
            } else {
                console.log('Iniciando gravação...');
                this.isProcessing = true;
                this.transcription = '';
                this.updateUI();
                
                try {
                    this.recognition.start();
                } catch (error) {
                    console.error('Erro ao iniciar reconhecimento:', error);
                    this.isProcessing = false;
                    this.isRecording = false;
                    this.showError('Não foi possível iniciar o reconhecimento de voz.');
                    this.updateUI();
                }
            }
        }
        
        speakWord() {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(this.currentChallenge);
                utterance.lang = this.options.language;
                utterance.rate = 0.8;
                speechSynthesis.speak(utterance);
            }
        }
        
        checkAnswer() {
            if (!this.transcription || !this.currentChallenge) return;
            
            // Normaliza o texto (remove acentos e caracteres especiais)
            const spokenText = this.normalizeText(this.transcription);
            const correctAnswer = this.normalizeText(this.currentChallenge);
            
            // Calcula similaridade
            const similarity = this.calculateSimilarity(spokenText, correctAnswer);
            
            console.log('Comparando:', spokenText, 'vs', correctAnswer, '- Similaridade:', similarity);
            
            // Define se está correto (80% de similaridade ou mais)
            this.isCorrect = similarity >= 0.8 || spokenText === correctAnswer;
            
            // Mostra feedback visual imediato
            this.showInstantFeedback();
        }
        
        normalizeText(text) {
            // Remove acentos
            let normalized = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            // Remove caracteres especiais e converte para minúsculas
            normalized = normalized.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase().trim();
            // Remove espaços extras
            normalized = normalized.replace(/\s+/g, ' ');
            
            return normalized;
        }
        
        calculateSimilarity(str1, str2) {
            // Algoritmo de Levenshtein para calcular similaridade
            const matrix = [];
            const len1 = str1.length;
            const len2 = str2.length;
            
            if (len1 === 0) return len2 === 0 ? 1 : 0;
            if (len2 === 0) return 0;
            
            // Inicializa matriz
            for (let i = 0; i <= len1; i++) {
                matrix[i] = [i];
            }
            for (let j = 0; j <= len2; j++) {
                matrix[0][j] = j;
            }
            
            // Preenche matriz
            for (let i = 1; i <= len1; i++) {
                for (let j = 1; j <= len2; j++) {
                    const cost = str1[i - 1] === str2[j - 1] ? 0 : 1;
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j] + 1,      // deletion
                        matrix[i][j - 1] + 1,      // insertion
                        matrix[i - 1][j - 1] + cost // substitution
                    );
                }
            }
            
            // Calcula similaridade (0 a 1)
            const maxLen = Math.max(len1, len2);
            return (maxLen - matrix[len1][len2]) / maxLen;
        }
        
        showInstantFeedback() {
            const status = this.container.querySelector('.status');
            if (!status) return;
            
            if (this.isCorrect) {
                status.innerHTML = `
                    <span class="correct-answer">
                        ✅ Correto! Você disse: "${this.transcription}"
                        <br><small>Clique em "Finalizar" para confirmar</small>
                    </span>
                `;
                
                // Auto-validar após 2 segundos se correto
                setTimeout(() => {
                    if (this.isCorrect && this.transcription && !this.isValidated) {
                        this.validateVoice();
                    }
                }, 2000);
                
            } else {
                status.innerHTML = `
                    <span class="incorrect-answer">
                        ❌ Incorreto. Você disse: "${this.transcription}"<br>
                        <small>Palavra esperada: "${this.currentChallenge}" - Tente novamente</small>
                    </span>
                `;
            }
        }
        
        updateUI() {
            const recordBtn = this.container.querySelector('.record-btn');
            const btnText = this.container.querySelector('.btn-text');
            const status = this.container.querySelector('.status');
            const validateBtn = this.container.querySelector('.validate-btn');
            
            // Adiciona proteção para elementos não encontrados
            if (!recordBtn || !btnText || !status || !validateBtn) {
                console.error('Elementos da UI não encontrados');
                return;
            }
            
            if (this.isRecording) {
                recordBtn.className = 'record-btn recording';
                btnText.textContent = 'Parar Gravação';
                status.innerHTML = '<span class="recording">🔴 Gravando... Diga: "' + this.currentChallenge + '"</span>';
                validateBtn.disabled = true;
                validateBtn.textContent = 'Verificar Captcha';
                
            } else if (this.isProcessing) {
                recordBtn.className = 'record-btn idle';
                recordBtn.disabled = true;
                btnText.textContent = 'Processando...';
                status.innerHTML = '<span class="processing">⏳ Processando sua fala...</span>';
                validateBtn.disabled = true;
                validateBtn.textContent = 'Aguarde...';
                
                // Timeout de segurança para resetar se ficar travado
                setTimeout(() => {
                    if (this.isProcessing) {
                        console.log('Timeout: resetando estado de processamento');
                        this.isProcessing = false;
                        this.showError('Timeout no processamento. Tente novamente.');
                        this.updateUI();
                    }
                }, 10000); // 10 segundos de timeout
                
            } else if (this.transcription) {
                recordBtn.className = 'record-btn idle';
                recordBtn.disabled = false;
                btnText.textContent = 'Gravar Novamente';
                
                // Se já verificou a resposta, não sobrescreve o feedback
                if (!this.hasOwnProperty('isCorrect')) {
                    status.innerHTML = '<span class="transcription">Você disse: "' + this.transcription + '"</span>';
                }
                
                // Configura botão baseado na resposta
                if (this.hasOwnProperty('isCorrect')) {
                    if (this.isCorrect) {
                        validateBtn.className = 'validate-btn validate-btn-correct';
                        validateBtn.textContent = 'Finalizar ✅';
                        validateBtn.disabled = false;
                    } else {
                        validateBtn.className = 'validate-btn validate-btn-incorrect';
                        validateBtn.textContent = 'Tentar Novamente ❌';
                        validateBtn.disabled = true; // Força gravar novamente
                    }
                } else {
                    validateBtn.disabled = false;
                    validateBtn.textContent = 'Verificar Captcha';
                }
                
            } else {
                recordBtn.className = 'record-btn idle';
                recordBtn.disabled = false;
                btnText.textContent = 'Iniciar Gravação';
                status.innerHTML = '';
                validateBtn.disabled = true;
                validateBtn.className = 'validate-btn';
                validateBtn.textContent = 'Verificar Captcha';
            }
        }
        
        async validateVoice() {
            if (!this.transcription) return;
            
            // Se resposta incorreta, força nova gravação
            if (this.hasOwnProperty('isCorrect') && !this.isCorrect) {
                this.transcription = '';
                delete this.isCorrect;
                this.updateUI();
                return;
            }
            
            // Marca como sendo validado para evitar dupla validação
            this.isValidated = true;
            
            // Atualiza UI para mostrar que está validando
            const validateBtn = this.container.querySelector('.validate-btn');
            validateBtn.textContent = 'Validando...';
            validateBtn.disabled = true;
            
            try {
                const response = await fetch(`${this.options.baseUrl}/embed/validate/voice?token=${this.options.token}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Captcha-Token': this.options.token
                    },
                    body: JSON.stringify({ spoken_text: this.transcription })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Sucesso - mostra feedback final
                    validateBtn.textContent = 'Validado ✅';
                    validateBtn.className = 'validate-btn validate-btn-success';
                    this.showMessage(data.message, true);
                    
                    // Desabilita toda interação após sucesso
                    this.container.querySelector('.record-btn').disabled = true;
                    
                } else {
                    // Erro do servidor - permite tentar novamente
                    validateBtn.textContent = 'Erro - Tente Novamente';
                    validateBtn.className = 'validate-btn validate-btn-error';
                    validateBtn.disabled = false;
                    this.isValidated = false;
                    this.showMessage(data.message, false);
                }
                
                // Dispara evento customizado
                const event = new CustomEvent('voiceCaptchaValidated', {
                    detail: {
                        success: data.success,
                        message: data.message,
                        transcription: this.transcription,
                        challenge: this.currentChallenge,
                        localCheck: this.isCorrect
                    }
                });
                this.container.dispatchEvent(event);
                
            } catch (error) {
                console.error('Erro na validação:', error);
                validateBtn.textContent = 'Erro de Conexão';
                validateBtn.className = 'validate-btn validate-btn-error';
                validateBtn.disabled = false;
                this.isValidated = false;
                this.showMessage('Erro de conexão. Tente novamente.', false);
            }
        }
        
        showMessage(message, success) {
            const messageDiv = this.container.querySelector('.message');
            messageDiv.className = `message ${success ? 'success' : 'error'}`;
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
        
        showError(message) {
            this.showMessage(message, false);
        }
    }
    
    // API pública
    window.VoiceCaptcha = VoiceCaptcha;
    
    // Auto-inicialização para elementos com data-voice-captcha
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll('[data-voice-captcha]');
        containers.forEach(container => {
            const token = container.getAttribute('data-token') || '';
            const baseUrl = container.getAttribute('data-base-url') || '';
            
            new VoiceCaptcha(container, { token, baseUrl });
        });
    });
})();