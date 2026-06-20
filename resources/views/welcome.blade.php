@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
            Bienvenido a <span class="text-indigo-600">Invita App</span>
        </h1>
        <p class="text-xl text-slate-600">
            Crea invitaciones digitales e interactivas, gestiona tus listas en tiempo real y organiza cualquier evento en minutos.
        </p>
    </div>

    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold text-slate-800 mb-8 text-center sm:text-left">¿Qué vas a celebrar hoy?</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <div class="group relative bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🍼
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Baby Shower</h3>
                    <p class="text-slate-500 text-sm mb-6">Maneja la cuenta regresiva, revela el género y organiza la lista de regalos interactiva para el próximo miembro de la familia.</p>
                </div>
                <a href="{{ route('register', ['tipo' => 'baby_shower']) }}" class="w-full text-center bg-blue-500 text-white py-3 px-4 rounded-xl font-medium hover:bg-blue-600 transition-colors block">
                    Organizar Baby Shower
                </a>
            </div>

            <div class="group relative bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        💍
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Matrimonio / Boda</h3>
                    <p class="text-slate-500 text-sm mb-6">Confirmación de asistencia formal (RSVP), mapa de ubicación del altar/fiesta y buzón virtual para transferencias o regalos.</p>
                </div>
                <a href="{{ route('register', ['tipo' => 'matrimonio']) }}" class="w-full text-center bg-rose-500 text-white py-3 px-4 rounded-xl font-medium hover:bg-rose-600 transition-colors block">
                    Organizar Matrimonio
                </a>
            </div>

            <div class="group relative bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🎂
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Cumpleaños</h3>
                    <p class="text-slate-500 text-sm mb-6">Ideal para fiestas de cualquier edad. Envía recordatorios automáticos por correo a tus amigos e incluye la temática especial.</p>
                </div>
                <a href="{{ route('register', ['tipo' => 'cumpleanos']) }}" class="w-full text-center bg-amber-500 text-white py-3 px-4 rounded-xl font-medium hover:bg-amber-600 transition-colors block">
                    Organizar Cumpleaños
                </a>
            </div>

            <div class="group relative bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-orange-50 text-orange-700 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🥩
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Asado Familiar o de Amigos</h3>
                    <p class="text-slate-500 text-sm mb-6">Habilita la lista de cooperación dinámica: coordina quién trae la carne, quién lleva las bebidas, el carbón o las ensaladas.</p>
                </div>
                <a href="{{ route('register', ['tipo' => 'asado']) }}" class="w-full text-center bg-orange-600 text-white py-3 px-4 rounded-xl font-medium hover:bg-orange-700 transition-colors block">
                    Organizar Asado
                </a>
            </div>

            <div class="group relative bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:scale-110 transition-transform">
                        🎉
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Fiesta / Carrete</h3>
                    <p class="text-slate-500 text-sm mb-6">Perfecto para celebraciones nocturnas, fiestas de disfraces o año nuevo. Controla el aforo máximo y añade código de vestimenta.</p>
                </div>
                <a href="{{ route('register', ['tipo' => 'fiesta']) }}" class="w-full text-center bg-purple-500 text-white py-3 px-4 rounded-xl font-medium hover:bg-purple-600 transition-colors block">
                    Organizar Fiesta
                </a>
            </div>

        </div>
    </div>
</div>
@endsection