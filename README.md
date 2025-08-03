# 🤖 leCaptcha - Multi Captcha System

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue?style=flat-square&logo=tailwindcss)
![PHP](https://img.shields.io/badge/PHP-8.x-777bb4?style=flat-square&logo=php)
![MIT License](https://img.shields.io/badge/license-MIT-green?style=flat-square)

Um sistema de **CAPTCHA moderno e elegante** com múltiplos desafios para validar usuários humanos em sua aplicação Laravel. Totalmente responsivo, acessível e fácil de integrar.

---

## 🚀 Recursos disponíveis

O **leCaptcha** oferece 6 tipos de validação para proteger seus formulários:

| Tipo          | Descrição                                                      | Demonstração                           |
| ------------- | -------------------------------------------------------------- | -------------------------------------- |
| **Text**      | Captcha tradicional com texto distorcido e linhas aleatórias  | ![Text Captcha](docs/text.png)        |
| **Robot**     | Checkbox "Não sou um robô" com animações e validação AJAX     | ![Robot Captcha](docs/robot.png)      |
| **Math**      | Resolva operações matemáticas simples para passar no desafio  | ![Math Captcha](docs/math.png)        |

---

## 🎯 Características especiais

### 🎤 **Voice Captcha** (Novidade)
- **Reconhecimento de Voz**: Utilize a API Web Speech Recognition
- **10 Categorias**: Animais, cores, frutas, objetos, natureza, corpo, comidas, transportes, família, profissões
- **100+ Palavras**: Vocabulário diversificado e familiar
- **Síntese de Voz**: Ouça a palavra antes de falar
- **Token Único**: Sistema de validação seguro
- **Acessibilidade**: Perfeito para usuários com deficiência visual

### 🔐 **Sistema de Segurança**
- **Tokens únicos** para cada validação
- **Sessões protegidas** contra ataques
- **Validação dupla** (cliente e servidor)
- **Logs detalhados** para auditoria

### 🎨 **Interface Moderna**
- **Design responsivo** com Tailwind CSS
- **Animações suaves** e feedback visual
- **Modo embed** para integração em outros sites
- **Compatibilidade** com todos os navegadores modernos

---

## 🛠️ Instalação

1. **Clone o repositório:**

    ```bash
    git clone https://github.com/Leozinnh/leCaptcha.git
    cd leCaptcha
    ```

2. **Instale as dependências:**

    ```bash
    composer install
    npm install && npm run build
    ```

3. **Configure o ambiente:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Configure o banco de dados no `.env`:**

    ```env
    DB_DATABASE=captcha
    DB_USERNAME=root
    DB_PASSWORD=sua_senha
    ```

5. **Crie o banco de dados:**

    ```sql
    CREATE DATABASE `captcha` 
    DEFAULT CHARACTER SET utf8 
    DEFAULT COLLATE utf8_general_ci;
    ```

6. **Execute as migrations:**

    ```bash
    php artisan migrate
    ```

7. **Inicie o servidor:**

    ```bash
    php artisan serve
    ```

8. **Acesse:** http://localhost:8000

---

## 🌐 Rotas disponíveis

### 📱 **Rotas principais**
| Rota                | Tipo | Descrição                          |
| ------------------- | ---- | ---------------------------------- |
| `/`                 | GET  | Dashboard com todos os captchas    |
| `/captcha/text`     | GET  | Captcha de texto tradicional       |
| `/captcha/grid`     | GET  | Captcha com grade de imagens       |
| `/captcha/dragdrop` | GET  | Captcha Drag & Drop                |
| `/captcha/robot`    | GET  | Checkbox "Não sou um robô"         |
| `/captcha/math`     | GET  | Captcha matemático                 |
| `/captcha/voice`    | GET  | **Captcha de voz** 🆕              |

### 🔗 **Rotas embed** (para integração)
| Rota                      | Tipo | Descrição                    |
| ------------------------- | ---- | ---------------------------- |
| `/embed/text`             | GET  | Embed do captcha de texto    |
| `/embed/grid`             | GET  | Embed do captcha de imagens  |
| `/embed/dragdrop`         | GET  | Embed do drag & drop         |
| `/embed/robot`            | GET  | Embed do captcha robot       |
| `/embed/math`             | GET  | Embed do captcha matemático  |
| `/embed/voice`            | GET  | **Embed do captcha de voz**  |

### 🔧 **Rotas de validação**
| Rota                           | Tipo | Descrição                    |
| ------------------------------ | ---- | ---------------------------- |
| `/captcha/text/validate`       | POST | Valida captcha de texto      |
| `/captcha/grid/validate`       | POST | Valida captcha de imagens    |
| `/captcha/dragdrop/validate`   | POST | Valida drag & drop           |
| `/captcha/robot/validate`      | POST | Valida captcha robot         |
| `/captcha/math/validate`       | POST | Valida captcha matemático    |
| `/captcha/voice/validate`      | POST | **Valida captcha de voz**    |
| `/captcha/validate`            | POST | Validação embed (genérica)   |

---

## 💻 Como usar (Exemplo de integração)

### 🎤 **Voice Captcha**
```html
<!-- Integração via iframe -->
<iframe src="http://localhost:8000/embed/voice" 
        width="400" height="600" 
        frameborder="0">
</iframe>
```

```php
// Verificação no backend
if (session('voice_captcha_token')) {
    echo "✅ Captcha validado! Token: " . session('voice_captcha_token');
} else {
    echo "❌ Complete o captcha de voz";
}
```

### 🤖 **Robot Captcha**
```html
<!-- Integração direta -->
<div id="robot-captcha"></div>
<script src="http://localhost:8000/embed/robot"></script>
```

---

## 🎯 Compatibilidade

### 🎤 **Voice Captcha - Requisitos**
- **Navegadores suportados:**
  - ✅ Chrome 25+
  - ✅ Edge 79+
  - ✅ Firefox 24+
  - ✅ Safari 14.1+
  - ❌ Internet Explorer (não suportado)

- **Permissões necessárias:**
  - 🎤 Acesso ao microfone
  - 🔊 Reprodução de áudio

### 🌐 **Outros Captchas**
- ✅ Todos os navegadores modernos
- ✅ Dispositivos móveis
- ✅ Tablets e desktops
- ✅ Suporte a touch screen

---

## ✨ Tecnologias utilizadas

- **🔴 Laravel 10** - Framework PHP robusto
- **🎨 Tailwind CSS 3** - Framework CSS utilitário
- **⚡ Alpine.js** - Framework JavaScript reativo
- **🎤 Web Speech API** - Reconhecimento e síntese de voz
- **🖼️ GD Library** - Geração de imagens
- **🔧 PHP 8.2+** - Linguagem de programação
- **🗄️ MySQL/SQLite** - Banco de dados

---

## 📁 Estrutura do projeto

```
leCaptcha/
├── app/
│   └── Http/Controllers/
│       └── CaptchaController.php    # Controlador principal
├── resources/
│   └── views/
│       ├── captcha/                 # Views dos captchas
│       │   ├── voice.blade.php      # 🆕 Voice Captcha
│       │   ├── text.blade.php
│       │   ├── grid.blade.php
│       │   ├── dragdrop.blade.php
│       │   ├── robot.blade.php
│       │   └── math.blade.php
│       └── embed/                   # Views para integração
├── routes/
│   └── web.php                      # Definição das rotas
├── public/
│   ├── lg.png                       # Logo do leCaptcha
│   └── assets/                      # Assets compilados
└── database/
    └── migrations/                  # Migrações do banco
```

---

## 🔧 Configurações avançadas

### 🎤 **Voice Captcha Settings**
```php
// No CaptchaController.php, você pode customizar:

$challenges = [
    'frutas' => ['maçã', 'banana', 'laranja', 'uva'],
    'cores' => ['azul', 'verde', 'vermelho', 'amarelo'],
    'animais' => ['gato', 'cão', 'pássaro', 'peixe'],
    // Adicione suas próprias categorias...
];

// Ajustar similaridade de reconhecimento (70% - 90%)
$similarity_threshold = 80; // Padrão: 80%
```

### 🔐 **Segurança**
```php
// Tempo de expiração dos tokens (em minutos)
$token_expiry = 30; // Padrão: 30 minutos

// Máximo de tentativas por IP
$max_attempts = 5; // Padrão: ilimitado
```

---

## 🐛 Troubleshooting

### 🎤 **Voice Captcha não funciona?**

1. **Verifique o navegador:**
   ```javascript
   console.log('Speech Recognition:', 'webkitSpeechRecognition' in window);
   console.log('Speech Synthesis:', 'speechSynthesis' in window);
   ```

2. **Permissões do microfone:**
   - Clique no ícone do microfone na barra de endereços
   - Selecione "Sempre permitir"

3. **HTTPS obrigatório:**
   - Em produção, use HTTPS
   - Localhost funciona sem HTTPS

### 🔧 **Problemas gerais:**

1. **Erro 500:**
   ```bash
   php artisan log:clear
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Assets não carregam:**
   ```bash
   npm run build
   php artisan storage:link
   ```

---

## 📸 Screenshots

| ![Dashboard](docs/dashboard.png)    | ![Voice Captcha](docs/voice.png)     |
| ----------------------------------- | ------------------------------------- |
| ![Text Captcha](docs/text.png)     | ![Grid Captcha](docs/grid.png)       |
| ![DragDrop](docs/dragdrop.png)     | ![Robot Captcha](docs/robot.png)     |
| ![Math Captcha](docs/math.png)     |                                      |

---

## 🚀 Roadmap

- [ ] **Captcha de Puzzle** - Monte quebra-cabeças
- [ ] **Captcha de Movimento** - Siga o padrão com o mouse
- [ ] **Captcha de Tempo** - Aguarde o tempo correto
- [ ] **API REST** - Integração via API
- [ ] **Dashboard Admin** - Painel de controle
- [ ] **Análise de Tentativas** - Relatórios e estatísticas

---

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 💬 Contato

📧 **Leonardo Alves** - [leonardoaf65572005@gmail.com](mailto:leonardoaf65572005@gmail.com)  
🔗 [github.com/Leozinnh](https://github.com/Leozinnh)  
🌟 [Dê uma estrela no projeto!](https://github.com/Leozinnh/leCaptcha)

---

<div align="center">
  <p>⭐ Se este projeto te ajudou, considere dar uma estrela!</p>
  <p>🤖 <strong>leCaptcha</strong> - Protegendo aplicações com estilo desde 2024</p>
</div>
