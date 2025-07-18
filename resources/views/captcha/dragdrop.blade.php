@extends('layout.index')

@section('content')
    <div class="max-w-sm mx-auto bg-white p-6 rounded-md shadow-md border border-gray-300" x-data="dragDrop()">
        <p class="text-gray-600 mb-5 text-center">Arraste a peça para o local correto:</p>

        <div class="relative mx-auto mb-6 w-64 h-64 rounded-md shadow-md border border-gray-200 overflow-hidden select-none"
            style="max-width:250px; max-height:250px;">
            <img src="{{ asset('captcha/puzzle_base.png') }}" alt="Base" class="w-full h-full object-cover rounded-md">

            @php
                $slotSize = session('slot_size', 50);
                $bgWidth = session('bg_width', 200);
                // $pieceWidthPercent = $bgWidth > 0 ? ($slotSize / $bgWidth) * 100 : 20; // Tamanho fixo em porcentagem %
                $pieceWidthPercent = $slotSize; // Tamanho fixo em pixels
            @endphp
            <img src="{{ asset('captcha/puzzle_piece.png') }}" alt="Peça" class="absolute cursor-move shadow-lg"
                :style="`width: {{ $pieceWidthPercent }}px; left: ${posX}px; top: ${posY}px; touch-action: none; transition: left 0.1s, top 0.1s;`"
                @mousedown="startDrag($event)" @touchstart="startDrag($event)" @mouseup="stopDrag()" @touchend="stopDrag()"
                @mousemove.window="onDrag($event)" @touchmove.window="onDrag($event)">
        </div>

        <form method="POST" action="/captcha/validate-dragdrop" @submit.prevent="$el.submit()" class="space-y-3">
            @csrf
            <input type="hidden" name="posX" :value="posX">
            <input type="hidden" name="posY" :value="posY">
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold py-2 rounded-md shadow-md hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition">
                Verificar
            </button>
        </form>

        @if (session('success'))
            <div class="flex items-center space-x-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md mt-5"
                role="alert">
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @elseif(session('error'))
            <div class="flex items-center space-x-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md shadow-md mt-5"
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
    </div>


    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function dragDrop() {
            return {
                posX: 0,
                posY: 0,
                dragging: false,
                offsetX: 0,
                offsetY: 0,

                startDrag(event) {
                    this.dragging = true;
                    const e = event.type.startsWith('touch') ? event.touches[0] : event;
                    this.offsetX = e.clientX - this.posX;
                    this.offsetY = e.clientY - this.posY;
                    event.preventDefault();
                },

                stopDrag() {
                    this.dragging = false;
                },

                onDrag(event) {
                    if (!this.dragging) return;
                    const e = event.type.startsWith('touch') ? event.touches[0] : event;
                    this.posX = e.clientX - this.offsetX;
                    this.posY = e.clientY - this.offsetY;
                }
            }
        }
    </script>
@endsection
