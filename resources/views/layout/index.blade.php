<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'leCaptcha' }}</title>
    <link rel="icon" href="/lg.png" type="image/png">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <nav class="bg-gray-500 text-white px-6 py-3">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="font-bold text-lg flex flex-row"><img src="/lg.png" width="30px"
                    alt="">&nbsp;leCaptcha</a>
            <div class="space-x-4">
                <a href="/captcha/text" class="hover:underline">Text</a>
                <a href="/captcha/grid" class="hover:underline">Grid</a>
                <a href="/captcha/dragdrop" class="hover:underline">Drag&Drop</a>
                <a href="/captcha/robot" class="hover:underline">Robot</a>
                <a href="/captcha/math" class="hover:underline">Math</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 px-4">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 mt-10 mb-4 monospace">
        &copy; {{ date('Y') }} leCaptcha - Laravel & Tailwind
    </footer>

</body>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const leCaptcha = document.querySelector('.leCaptcha');
        const text = leCaptcha.textContent.trim();
        const colorSets = [
            ['rgb(233, 1, 1)', 'rgb(2, 212, 20)'], // ciclo 1: l=red, e=green
            ['rgb(2, 212, 20)', 'rgb(211, 211, 2)'], // ciclo 2: l=green, e=yellow
            ['rgb(211, 211, 2)', 'rgb(1, 76, 238)'], // ciclo 3: l=yellow, e=blue
            ['rgb(1, 76, 238)', 'rgb(233, 1, 1)'] // ciclo 4: l=blue, e=red
        ];
        let cycle = 0;
        leCaptcha.innerHTML = text.split('').map(char => `<span>${char}</span>`).join('');

        const updateColors = () => {
            const spans = leCaptcha.querySelectorAll('span');
            spans.forEach((span, index) => {
                span.style.color = colorSets[cycle][index % colorSets[cycle].length];
            });
            cycle = (cycle + 1) % colorSets.length;
        };

        updateColors();
        setInterval(updateColors, 2000);
    });
</script>

</html>
