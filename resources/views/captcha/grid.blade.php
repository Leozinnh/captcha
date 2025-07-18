@extends('layout.index')

@section('content')
    <div class="max-w-xs m-auto bg-white p-6 rounded shadow" x-data="{ selected: [] }">
        <p class="mb-2 text-center">Clique nas imagens com os seguintes pares na ordem correta:</p>

        <div class="flex items-center justify-center space-x-2 mb-4">
            @foreach (session('captcha_grid_sequence', []) as $pair)
                <div
                    class="px-4 py-2 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 font-mono font-semibold rounded-lg
                    shadow-sm border border-blue-300 hover:from-blue-200 hover:to-blue-300 cursor-default
                    transition duration-300 select-none">
                    {{ $pair }}
                </div>
            @endforeach
        </div>

        <form method="POST" action="/captcha/validate-grid"
            @submit.prevent="
        if (selected.length !== 3) {
            alert('Selecione exatamente 3 imagens na sequência correta.');
            return;
        }
        // Remove inputs antigos antes de criar novos
        while ($refs.form.querySelector('input[name=selected\\[\\]]')) {
            $refs.form.querySelector('input[name=selected\\[\\]]').remove();
        }
        selected.forEach((pair) => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected[]';
            input.value = pair;
            $refs.form.appendChild(input);
        });
        $refs.form.submit();
    "
            x-ref="form">
            @csrf

            <div class="grid grid-cols-3 gap-3">
                @foreach ($images as $index => $img)
                    <div class="cursor-pointer select-none rounded-lg border border-gray-300 p-2 shadow-sm transition 
                hover:shadow-md hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500
                relative"
                        :class="selected.includes('{{ $pairs[$index] }}') ? 'ring-4 ring-blue-600 scale-105' :
                            'ring-0 scale-100'"
                        @click="
        if (selected.includes('{{ $pairs[$index] }}')) {
            selected = selected.filter(i => i !== '{{ $pairs[$index] }}');
            } else if (selected.length < 3) {
            selected.push('{{ $pairs[$index] }}');
            }
        "
                        tabindex="0">
                        {{-- <div class="absolute top-0 left-1 text-xs">{{ $pairs[$index] }}</div> --}}
                        <img src="{{ $img }}" alt="Captcha image {{ $index + 1 }}"
                            class="w-full h-full object-cover rounded-lg aspect-square" draggable="false" />
                        <template x-if="selected.includes('{{ $pairs[$index] }}')">
                            <div class="absolute top-1 right-1 bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold"
                                aria-label="Selecionado">
                                ✓
                            </div>
                        </template>
                    </div>
                @endforeach
            </div>


            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-sm text-white font-semibold py-2 rounded-md shadow-md hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition mt-4">Verificar</button>

            @if (session('success'))
                <div class="flex items-center space-x-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md mt-4 w-full"
                    role="alert">
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @elseif(session('error'))
                <div class="flex items-center space-x-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md shadow-md mt-4 w-full"
                    role="alert">
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-col items-center text-xs text-gray-500 mt-4">
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
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
