<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($school?->name ?? config('app.name')) }}</title>
    <meta name="description" content="{{ $description ?? $school?->short_description }}">
    <meta name="theme-color" content="#052e16">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-slate-900 antialiased">
    <header x-data="{open:false}" class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-emerald-950/90 text-white backdrop-blur-xl">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="grid size-11 place-items-center rounded-2xl bg-amber-400 font-black text-emerald-950">S</span><span class="font-bold leading-tight">{{ $school?->name ?? 'Smart School' }}<small class="block text-xs font-medium text-emerald-200">School Information System</small></span></a>
            <nav class="hidden items-center gap-7 text-sm font-semibold lg:flex">@foreach($menus ?? [] as $menu)<a class="transition hover:text-amber-300" href="{{ $menu->url }}">{{ $menu->label }}</a>@endforeach <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-2.5 text-emerald-950">Portal Sekolah</a></nav>
            <button @click="open=!open" class="rounded-xl border border-white/20 p-2 lg:hidden" aria-label="Buka navigasi">☰</button>
        </div>
        <nav x-show="open" x-transition class="space-y-2 border-t border-white/10 px-5 py-5 lg:hidden">@foreach($menus ?? [] as $menu)<a class="block rounded-xl px-3 py-2 hover:bg-white/10" href="{{ $menu->url }}">{{ $menu->label }}</a>@endforeach<a class="block rounded-xl bg-amber-400 px-3 py-2 font-bold text-emerald-950" href="{{ route('login') }}">Portal Sekolah</a></nav>
    </header>
    <main>{{ $slot ?? '' }}@yield('content')</main>
    <footer id="kontak" class="bg-slate-950 px-5 py-14 text-slate-300"><div class="mx-auto grid max-w-7xl gap-8 md:grid-cols-2"><div><div class="text-xl font-bold text-white">{{ $school?->name }}</div><p class="mt-3 max-w-md text-sm leading-6">{{ $school?->short_description }}</p></div><div class="md:text-right"><p>{{ $school?->address }}, {{ $school?->city }}</p><p class="mt-2">{{ $school?->email }} · {{ $school?->phone }}</p></div></div></footer>
</body></html>
