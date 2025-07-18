<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;

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
        $checkbox = $request->input('imnotrobot', false);

        // esperar 5segundos
        sleep(2);
        // retornar json de sucesso ou erro
        if ($checkbox) {
            return response()->json(['success' => true, 'message' => '✅ Captcha validado com sucesso!']);
        } else {
            return response()->json(['success' => false, 'message' => '❌ Captcha inválido, tente novamente.'], 400);
        }
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
}
