@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Fondo de imagen -->
    <img src="/gif/video.gif" alt="Fondo animado" class="fixed inset-0 w-full h-full object-cover z-0" />
    <!-- Recuadro de recuperación -->
    <div class="relative z-20 flex items-center justify-center w-full min-h-screen px-4">
        <div class="relative bg-white rounded-[48px] shadow-2xl w-full max-w-xl mx-auto flex flex-col items-center pt-20 pb-10 px-8">
            <!-- Icono usuario grande y flotante -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 bg-blue-700 rounded-full p-6 flex items-center justify-center shadow-lg">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4" stroke="white" stroke-width="2" fill="none"/>
                    <path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="white" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-black mb-8 text-center mt-2">¿Olvidaste tu contraseña?</h2>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="w-full flex flex-col gap-5">
                @csrf

                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-1">Email *</label>
                    <input id="email" type="email" name="email" placeholder="Ingresa tu email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full px-4 py-3 bg-gray-100 border-none rounded-md focus:ring-2 focus:ring-blue-400 text-base" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button type="submit" class="w-full bg-blue-700 text-white py-3 rounded-md font-semibold hover:bg-blue-800 transition text-lg mt-2">
                    Enviar enlace de recuperación
                </button>
            </form>

            <div class="w-full flex flex-col items-center mt-8 gap-2">
                <a href="{{ route('login') }}" class="text-gray-400 hover:text-blue-700 text-base">Volver al login</a>
            </div>
        </div>
    </div>
</div>
@endsection