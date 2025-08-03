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
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
</head>

<body class="bg-gradient-to-r from-gray-100 via-gray-50 to-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside
            class="bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-gray-100 w-64 space-y-6 py-8 px-6 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-300 ease-in-out"
            id="sidebar">

            <!-- Logo -->
            <a href="/" class="flex items-center space-x-3 px-2">
                <img src="/lg.png" alt="Logo" class="w-10 h-10 rounded-full shadow">
                <span class="text-2xl font-extrabold tracking-wide">leCaptcha</span>
            </a>

            <hr class="border-gray-700 my-6">

            <!-- Navigation -->
            <nav class="flex flex-col space-y-2">
                <a href="/"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-blue-500 transition duration-300 ease-in-out">
                    Dashboard
                </a>

                <a href="/admin/tokens"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-blue-500 transition duration-300 ease-in-out">
                    Tokens
                </a>

                <a href="/captcha/text"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-green-500 transition duration-300 ease-in-out">
                    Text Captcha
                </a>

                <a href="/captcha/grid"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-purple-500 transition duration-300 ease-in-out">
                    Grid Captcha
                </a>

                <a href="/captcha/dragdrop"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-yellow-500 transition duration-300 ease-in-out">
                    Drag & Drop
                </a>

                <a href="/captcha/robot"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-pink-500 transition duration-300 ease-in-out">
                    Robot Captcha
                </a>

                <a href="/captcha/math"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-red-500 transition duration-300 ease-in-out">
                    Math Captcha
                </a>

                <a href="/captcha/voice"
                    class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-700 hover:bg-opacity-50 hover:border-l-4 hover:border-orange-500 transition duration-300 ease-in-out">
                    Voice Captcha
                </a>
            </nav>
        </aside>



        <div class="flex-1 flex flex-col">
            <!-- Topbar -->
            <header class="flex justify-between items-center bg-white/70 backdrop-blur-md shadow-md px-6 py-3">
                <!-- Left: Title + Menu Button -->
                <div class="flex items-center space-x-4">
                    <button class="md:hidden text-gray-600 focus:outline-none"
                        onclick="$('#sidebar').toggleClass('-translate-x-full')">
                        <span class="block w-6 h-0.5 bg-gray-800 mb-1"></span>
                        <span class="block w-6 h-0.5 bg-gray-800 mb-1"></span>
                        <span class="block w-6 h-0.5 bg-gray-800"></span>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-wide">Painel Admin</h1>
                </div>

                <!-- Right: User Profile -->
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                        <span class="hidden sm:inline text-gray-700 font-semibold">Olá,
                            {{ Auth::user()->name ?? 'Admin' }}</span>
                        <div class="flex items-center space-x-1">
                            <img src="https://i.pravatar.cc/40?img=3" alt="Profile"
                                class="w-10 h-10 rounded-full border-2 border-gray-300 shadow cursor-pointer">
                            <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.584l3.71-3.35a.75.75 0 111.02 1.1l-4.25 3.84a.75.75 0 01-1.02 0l-4.25-3.84a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div id="userDropdown"
                        class="hidden absolute right-0 mt-2 w-52 bg-white text-gray-900 rounded-lg shadow-lg z-50">
                        <nav class="flex flex-col space-y-2 p-2">
                            <a href="/profile"
                                class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-100 hover:border-l-4 hover:border-blue-500 transition duration-300 ease-in-out">
                                Perfil
                            </a>
                            <a href="/settings"
                                class="block py-3 px-5 rounded-lg font-semibold tracking-wide hover:bg-gray-100 hover:border-l-4 hover:border-green-500 transition duration-300 ease-in-out">
                                Configurações
                            </a>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit"
                                    class="cursor-pointer w-full text-left py-3 px-5 rounded-lg font-semibold tracking-wide text-red-600 hover:bg-red-50 hover:border-l-4 hover:border-red-600 transition duration-300 ease-in-out">
                                    Sair
                                </button>
                            </form>
                        </nav>
                    </div>

                </div>
            </header>


            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Yield Content -->
                <div class="mt-8">
                    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8">
                        @yield('content')
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="text-center text-gray-500 py-4 shadow-md">
                &copy; {{ date('Y') }} leCaptcha - Laravel & Tailwind
            </footer>
        </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#userMenuButton').on('click', function(e) {
            e.stopPropagation();
            $('#userDropdown').fadeToggle(150);
        });

        $(document).on('click', function() {
            $('#userDropdown').fadeOut(150);
        });

        $('#userDropdown').on('click', function(e) {
            e.stopPropagation();
        });
    });
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
