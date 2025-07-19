<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;
use App\Models\CaptchaToken;

class CaptchaController extends Controller
{
    public function showText()
    {
        return view('captcha.text');
    }

    public function generateText()
    {
        $builder = new CaptchaBuilder;

        $builder->setBackgroundColor(243, 244, 246);  // Fundo
        $builder->setMaxBehindLines(7);               // Linhas atrás do texto
        $builder->setMaxFrontLines(5);                // Linhas na frente do texto
        $builder->setInterpolation(true);             // Distorção

        $fontPath = public_path('fonts/captcha.ttf');
        $builder->build(200, 60, $fontPath);

        Session::put('captcha_code', $builder->getPhrase());

        // dd($builder->getPhrase()); // Debug: Verifica o código gerado

        return response($builder->get())->header('Content-Type', 'image/jpeg');
    }

    public function validateText(Request $request)
    {
        $request->validate(['captcha' => 'required|string']);
        if (strtoupper($request->captcha) === strtoupper(session('captcha_code'))) {
            return back()->with('success', '✅ Captcha válido!');
        }
        return back()->with('error', '❌ Captcha inválido.');
    }

    // === GRID CAPTCHA ===
    public function showGrid()
    {
        $fontPath = public_path('fonts/captcha.ttf');

        $correctPairs = [];
        while (count($correctPairs) < 3) {
            $pair = $this->randomPair();
            if (!in_array($pair, $correctPairs)) {
                $correctPairs[] = $pair;
            }
        }

        $allPairs = $correctPairs;
        while (count($allPairs) < 9) {
            $pair = $this->randomPair();
            if (!in_array($pair, $allPairs)) {
                $allPairs[] = $pair;
            }
        }

        shuffle($allPairs);
        Session::put('captcha_grid_sequence', $correctPairs);

        $images = [];
        foreach ($allPairs as $pair) {
            $images[] = $this->generateImageForText($pair, $fontPath);
        }

        return view('captcha.grid', [
            'images' => $images,
            'pairs' => $allPairs,
        ]);
    }

    private function generateImageForText(string $text, string $fontPath): string
    {
        $builder = new CaptchaBuilder($text);
        $builder->setBackgroundColor(243, 244, 246);
        $builder->setMaxBehindLines(19);
        $builder->setMaxFrontLines(7);
        $builder->setInterpolation(true);
        $builder->build(100, 75, $fontPath);

        return 'data:image/jpeg;base64,' . base64_encode($builder->get());
    }

    private function randomPair()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return $chars[rand(0, strlen($chars) - 1)] . $chars[rand(0, strlen($chars) - 1)];
    }

    public function validateGrid(Request $request)
    {
        $selected = $request->input('selected', []); // array dos pares clicados
        $correct = Session::get('captcha_grid_sequence', []);

        if ($selected === $correct) {
            return back()->with('success', '✅ Captcha válido!');
        }
        return back()->with('error', '❌ Captcha inválido.');
    }




    // === DRAG & DROP CAPTCHA ===
    public function showDragDrop()
    {
        $bgPath = public_path('images/bg.jpg');
        if (!file_exists($bgPath)) {
            throw new \Exception('Imagem de fundo não encontrada: ' . $bgPath);
        }
        $captchaPath = public_path('captcha');
        if (!is_dir($captchaPath)) {
            mkdir($captchaPath, 0755, true);
        }

        $bg = imagecreatefromjpeg($bgPath);

        // $width = imagesx($bg);
        // $height = imagesy($bg);
        $width = 250; // Largura fixa para o exemplo
        $height = 250; // Altura fixa para o exemplo

        // Tamanho e posição do slot
        $slotSize = 50;
        $slotX = rand(60, $width - $slotSize - 60);
        $slotY = rand(60, $height - $slotSize - 60);

        // Cria a peça recortada
        $piece = imagecreatetruecolor($slotSize, $slotSize);
        imagecopy($piece, $bg, 0, 0, $slotX, $slotY, $slotSize, $slotSize);

        // Aplica um efeito visual no slot (área de destino)
        $grey = imagecolorallocatealpha($bg, 200, 200, 200, 60);
        imagefilledrectangle($bg, $slotX, $slotY, $slotX + $slotSize, $slotY + $slotSize, $grey);

        $bgPathOut = $captchaPath . '/puzzle_base.png';
        $piecePathOut = $captchaPath . '/puzzle_piece.png';

        if (!imagepng($bg, $bgPathOut)) {
            throw new \Exception('Falha ao salvar a imagem base em ' . $bgPathOut);
        }

        if (!imagepng($piece, $piecePathOut)) {
            throw new \Exception('Falha ao salvar a peça em ' . $piecePathOut);
        }

        imagedestroy($bg);
        imagedestroy($piece);

        session([
            'puzzle_x' => $slotX,
            'puzzle_y' => $slotY,
            'slot_size' => $slotSize,
            'bg_width' => $width,
            'bg_height' => $height,
        ]);

        return view('captcha.dragdrop');
    }


    public function validateDragDrop(Request $request)
    {
        $posX = (float) $request->input('posX');
        $posY = (float) $request->input('posY');

        $puzzleX = session('puzzle_x');
        $puzzleY = session('puzzle_y');
        $bgWidth = session('bg_width');
        $bgHeight = session('bg_height');

        $displayWidth = 300;
        $displayHeight = $bgHeight * ($displayWidth / $bgWidth);

        $scaleX = $bgWidth / $displayWidth;
        $scaleY = $bgHeight / $displayHeight;

        $posXReal = $posX * $scaleX;
        $posYReal = $posY * $scaleY;

        $toleranceX = 30;
        $toleranceY = 25;

        if (
            abs($posXReal - $puzzleX) <= $toleranceX &&
            abs($posYReal - $puzzleY) <= $toleranceY
        ) {
            return back()->with('success', '✅ Captcha validado com sucesso!');
        } else {
            return back()->with('error', '❌ Captcha inválido, tente novamente.');
        }
    }



    // === ROBOT CAPTCHA ===
    public function showRobot()
    {
        return view('captcha.robot');
    }
    public function validateRobot(Request $request)
    {
        // 1. Verifica método POST
        if (!$request->isMethod('post')) {
            return response()->json(['success' => false, 'message' => 'Método inválido'], 405);
        }

        // 2. Pega o payload codificado em base64
        $encodedPayload = $request->input('payload');
        if (!$encodedPayload) {
            return response()->json(['success' => false, 'message' => 'Payload ausente'], 400);
        }

        // 3. Decodifica base64 e converte para array
        $json = base64_decode($encodedPayload);
        if (!$json) {
            return response()->json(['success' => false, 'message' => 'Payload inválido (base64)'], 400);
        }

        $data = json_decode($json, true);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Payload inválido (JSON)'], 400);
        }

        // 4. Extrai os dados do JSON decodificado
        $ip = $data['ip'] ?? null;
        $userAgent = $data['userAgent'] ?? null;
        $referer = $data['referer'] ?? null;
        $url = $data['url'] ?? null;
        $token = $data['token'] ?? null;

        // 5. Valida token CSRF (importante, pois você envia manualmente)
        if ($token !== csrf_token()) {
            return response()->json(['success' => false, 'message' => 'Token CSRF inválido'], 403);
        }

        // 6. Valida User-Agent
        if (!$userAgent) {
            return response()->json(['success' => false, 'message' => 'User-Agent ausente'], 400);
        }
        $botSignatures = ['bot', 'crawler', 'spider', 'curl', 'facebookexternalhit', 'wget', 'python-requests', 'scraper'];
        foreach ($botSignatures as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return response()->json(['success' => false, 'message' => 'Bot detectado pelo User-Agent'], 400);
            }
        }

        // 7. Valida IP
        if (!$ip) {
            return response()->json(['success' => false, 'message' => 'IP ausente'], 400);
        }
        // Permite localhost para dev/teste, bloqueia IP privado/reservado em produção
        if ($ip === '127.0.0.1' || $ip === '::1') {
            // localhost, permita para teste
        } else {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return response()->json(['success' => false, 'message' => 'IP inválido ou privado'], 400);
            }
        }

        // 8. Valida URL
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['success' => false, 'message' => 'URL inválida'], 400);
        }

        // 9. Confere se IP enviado bate com IP real da requisição
        if ($ip !== $request->ip()) {
            return response()->json(['success' => false, 'message' => 'IP da requisição não corresponde ao IP enviado'], 400);
        }

        return response()->json(['success' => true, 'message' => '✅ Captcha validado com sucesso!']);
    }






    // === MATH CAPTCHA ===
    public function showMath()
    {
        return view('captcha.math');
    }

    public function generateMathImage()
    {
        // Gera expressão matemática
        $a = rand(1, 9);
        $b = rand(1, 9);
        $operator = ['+', '-'][rand(0, 1)];
        $expression = "$a $operator $b";
        $result = eval("return $a $operator $b;");

        Session::put('captcha_math_result', $result);

        $builder = new CaptchaBuilder("$expression = ?");
        $builder->setBackgroundColor(243, 244, 246);
        $builder->setMaxBehindLines(9);
        $builder->setMaxFrontLines(7);
        $builder->setInterpolation(true);

        $fontPath = public_path('fonts/captcha2.otf');
        $builder->build(200, 60, $fontPath);

        return response($builder->get())
            ->header('Content-Type', 'image/jpeg');
    }

    public function validateMath(Request $request)
    {
        $correct = Session::get('captcha_math_result');
        if ($request->input('answer') == $correct) {
            return back()->with('success', 'Resposta correta!');
        }

        return back()->with('error', 'Resposta errada, tente novamente.');
    }

































    // === MÉTODOS PARA EMBEDS ===
    public function embedText()
    {
        return view('captcha.embed.text', ['isEmbed' => true]);
    }

    public function embedRobot()
    {
        return view('captcha.embed.robot', ['isEmbed' => true]);
    }

    public function embedMath()
    {
        return view('captcha.embed.math', ['isEmbed' => true]);
    }

    public function embedGrid()
    {
        $fontPath = public_path('fonts/captcha.ttf');
        $correctPairs = [];
        while (count($correctPairs) < 3) {
            $pair = $this->randomPair();
            if (!in_array($pair, $correctPairs)) {
                $correctPairs[] = $pair;
            }
        }

        $allPairs = $correctPairs;
        while (count($allPairs) < 9) {
            $pair = $this->randomPair();
            if (!in_array($pair, $allPairs)) {
                $allPairs[] = $pair;
            }
        }

        shuffle($allPairs);
        Session::put('captcha_grid_sequence', $correctPairs);

        $images = [];
        foreach ($allPairs as $pair) {
            $images[] = $this->generateImageForText($pair, $fontPath);
        }

        return view('captcha.embed.grid', [
            'images' => $images,
            'pairs' => $allPairs,
            'isEmbed' => true
        ]);
    }

    public function embedDragDrop()
    {
        $bgPath = public_path('images/bg.jpg');
        if (!file_exists($bgPath)) {
            throw new \Exception('Imagem de fundo não encontrada: ' . $bgPath);
        }

        $captchaPath = public_path('captcha');
        if (!is_dir($captchaPath)) {
            mkdir($captchaPath, 0755, true);
        }

        $bg = imagecreatefromjpeg($bgPath);
        $width = 250;
        $height = 250;
        $slotSize = 50;
        $slotX = rand(60, $width - $slotSize - 60);
        $slotY = rand(60, $height - $slotSize - 60);

        $piece = imagecreatetruecolor($slotSize, $slotSize);
        imagecopy($piece, $bg, 0, 0, $slotX, $slotY, $slotSize, $slotSize);

        $grey = imagecolorallocatealpha($bg, 200, 200, 200, 60);
        imagefilledrectangle($bg, $slotX, $slotY, $slotX + $slotSize, $slotY + $slotSize, $grey);

        $bgPathOut = $captchaPath . '/puzzle_base.png';
        $piecePathOut = $captchaPath . '/puzzle_piece.png';

        imagepng($bg, $bgPathOut);
        imagepng($piece, $piecePathOut);
        imagedestroy($bg);
        imagedestroy($piece);

        session([
            'puzzle_x' => $slotX,
            'puzzle_y' => $slotY,
            'slot_size' => $slotSize,
            'bg_width' => $width,
            'bg_height' => $height,
        ]);

        return view('captcha.embed.dragdrop', ['isEmbed' => true]);
    }

    // Middleware para validar token
    private function validateToken(Request $request, $captchaType)
    {
        $token = $request->header('X-Captcha-Token') ?: $request->input('token');
        
        if (!$token) {
            return response()->json(['error' => 'Token requerido'], 401);
        }

        $captchaToken = CaptchaToken::where('token', $token)->where('is_active', true)->first();
        
        if (!$captchaToken) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        if ($captchaToken->hasReachedDailyLimit()) {
            return response()->json(['error' => 'Limite diário excedido'], 429);
        }

        if (!$captchaToken->canUseCaptchaType($captchaType)) {
            return response()->json(['error' => 'Tipo de captcha não permitido'], 403);
        }

        // Incrementa contador de uso
        $captchaToken->incrementUsage();
        
        return $captchaToken;
    }

    // Atualizar método validateEmbed
    public function validateEmbed(Request $request, $type)
    {
        // Validar token
        $tokenValidation = $this->validateToken($request, $type);
        if ($tokenValidation instanceof \Illuminate\Http\JsonResponse) {
            return $tokenValidation;
        }

        $result = false;
        $message = '';

        switch ($type) {
            case 'text':
                $request->validate(['captcha' => 'required|string']);
                $result = strtoupper($request->captcha) === strtoupper(session('captcha_code'));
                $message = $result ? '✅ Captcha válido!' : '❌ Captcha inválido.';
                break;

            case 'robot':
                sleep(2);
                $result = true;
                $message = '✅ Captcha validado com sucesso!';
                break;

            case 'math':
                $correct = Session::get('captcha_math_result');
                $result = $request->input('answer') == $correct;
                $message = $result ? 'Resposta correta!' : 'Resposta errada, tente novamente.';
                break;

            case 'grid':
                $selected = $request->input('selected', []);
                $correct = Session::get('captcha_grid_sequence', []);
                $result = $selected === $correct;
                $message = $result ? '✅ Captcha válido!' : '❌ Captcha inválido.';
                break;

            case 'dragdrop':
                $posX = (float) $request->input('posX');
                $posY = (float) $request->input('posY');
                $puzzleX = session('puzzle_x');
                $puzzleY = session('puzzle_y');
                $bgWidth = session('bg_width');
                $bgHeight = session('bg_height');

                $displayWidth = 300;
                $displayHeight = $bgHeight * ($displayWidth / $bgWidth);
                $scaleX = $bgWidth / $displayWidth;
                $scaleY = $bgHeight / $displayHeight;
                $posXReal = $posX * $scaleX;
                $posYReal = $posY * $scaleY;
                $toleranceX = 30;
                $toleranceY = 25;

                $result = abs($posXReal - $puzzleX) <= $toleranceX && abs($posYReal - $puzzleY) <= $toleranceY;
                $message = $result ? '✅ Captcha validado com sucesso!' : '❌ Captcha inválido, tente novamente.';
                break;
        }

        return response()->json([
            'success' => $result,
            'message' => $message
        ]);
    }

    public function generateWidget(Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            return response('console.error("leCaptcha: Token obrigatório");', 200)
                ->header('Content-Type', 'application/javascript');
        }

        $baseUrl = request()->getSchemeAndHttpHost();

        $js = "
(function() {
    const LECAPTCHA_TOKEN = '{$token}';
    const BASE_URL = '{$baseUrl}';
    
    function loadLeCaptcha() {
        const containers = document.querySelectorAll('[data-lecaptcha]');
        
        containers.forEach(container => {
            const type = container.getAttribute('data-lecaptcha');
            const width = container.getAttribute('data-width') || '400';
            const height = container.getAttribute('data-height') || '300';
            
            const iframe = document.createElement('iframe');
            iframe.src = BASE_URL + '/embed/' + type + '?token=' + LECAPTCHA_TOKEN;
            iframe.width = width;
            iframe.height = height;
            iframe.style.border = 'none';
            iframe.style.borderRadius = '8px';
            iframe.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            
            container.appendChild(iframe);
            
            // Listener para mensagens do iframe
            window.addEventListener('message', function(event) {
                if (event.origin !== BASE_URL) return;
                
                if (event.data.type === 'captcha_validated') {
                    const customEvent = new CustomEvent('leCaptchaValidated', {
                        detail: {
                            success: event.data.success,
                            message: event.data.message,
                            captchaType: type,
                            token: LECAPTCHA_TOKEN
                        }
                    });
                    container.dispatchEvent(customEvent);
                }
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadLeCaptcha);
    } else {
        loadLeCaptcha();
    }
})();
        ";

        return response($js)->header('Content-Type', 'application/javascript');
    }

    // Métodos para gerenciar tokens
    public function listTokens()
    {
        $tokens = CaptchaToken::orderBy('created_at', 'desc')->get();
        return view('admin.tokens.index', compact('tokens'));
    }

    public function createToken(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
            'name' => 'nullable|string|max:255',
            'daily_limit' => 'integer|min:1',
            'allowed_types' => 'nullable|array',
            'allowed_types.*' => 'in:text,robot,math,grid,dragdrop'
        ]);

        $token = CaptchaToken::create([
            'token' => CaptchaToken::generateToken(),
            'domain' => $request->domain,
            'name' => $request->name,
            'daily_limit' => $request->daily_limit ?? 1000,
            'allowed_types' => $request->allowed_types
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Token criado com sucesso'
        ]);
    }

    public function deleteToken($id)
    {
        $token = CaptchaToken::findOrFail($id);
        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token excluído com sucesso'
        ]);
    }
}
