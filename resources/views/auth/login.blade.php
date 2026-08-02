@extends('layouts.app')

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-xl border border-slate-100 relative">
        
        {{-- Encabezado Neutro Invita App --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 text-2xl font-bold mb-3 shadow-inner">
                ✨
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bienvenido a Invita App</h1>
            <p class="text-xs text-slate-500 mt-1">Ingresa a tu cuenta para gestionar tus celebraciones</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold rounded-2xl text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulario de Acceso --}}
        <form action="/login" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="tu@correo.com"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                <input type="password" name="contrasena" required placeholder="••••••••"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-medium">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-200">
                    <span>Recordarme</span>
                </label>
                <a href="/recuperar" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="w-full mt-2 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-100 pt-6">
            <p class="text-xs text-slate-500">
                ¿Aún no tienes una cuenta? 
                <a href="/registro" class="font-bold text-indigo-600 hover:text-indigo-700 transition-colors">Regístrate gratis</a>
            </p>
        </div>

    </div>
</div>
@endsection