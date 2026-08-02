<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="theme-color" content="#052e2b"><meta name="mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-capable" content="yes">
    <title>Masuk · Smart School</title><link rel="manifest" href="/manifest.webmanifest"><link rel="icon" href="/icons/smart-school.svg" type="image/svg+xml"><link rel="apple-touch-icon" href="/icons/smart-school.svg">@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased" x-data="pwaShell">
<main class="grid min-h-screen lg:grid-cols-[1.15fr_.85fr]">
    <section class="relative hidden overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-800 to-cyan-800 p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-16"><div class="absolute -left-32 top-1/3 size-96 rounded-full bg-emerald-300/15 blur-3xl"></div><div class="absolute -right-24 -top-24 size-96 rounded-full bg-cyan-300/20 blur-3xl"></div><div class="absolute bottom-12 right-12 grid grid-cols-3 gap-3 opacity-20">@foreach(range(1,9) as $i)<span class="size-14 rounded-2xl border border-white/40 bg-white/10"></span>@endforeach</div>
        <a href="{{ route('home') }}" class="relative flex items-center gap-4"><img src="/icons/smart-school.svg" class="size-14 rounded-2xl shadow-xl" alt="Smart School"><span><b class="text-xl font-black">Smart School</b><small class="block text-emerald-200">Information System</small></span></a>
        <div class="relative max-w-2xl"><span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[.2em] backdrop-blur">Ekosistem pendidikan digital</span><h1 class="mt-6 text-5xl font-black leading-[1.08] xl:text-6xl">Satu portal untuk seluruh perjalanan belajar.</h1><p class="mt-6 max-w-xl text-lg leading-relaxed text-emerald-100">Akses jadwal, nilai, rapor, pengumuman, dan insight sekolah melalui pengalaman digital yang aman dan terintegrasi.</p><div class="mt-10 grid max-w-xl grid-cols-3 gap-3">@foreach([['Guru','Pembelajaran'],['Siswa','Perkembangan'],['Orang Tua','Pemantauan']] as [$role,$feature])<article class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur"><b class="block">{{ $role }}</b><small class="text-emerald-200">{{ $feature }}</small></article>@endforeach</div></div>
        <p class="relative text-xs text-emerald-200">© {{ date('Y') }} Smart School · Aman, responsif, dan mudah digunakan.</p>
    </section>
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#f4f8f7] px-5 py-10 sm:px-10"><div class="absolute inset-x-0 top-0 h-56 bg-gradient-to-br from-emerald-950 to-cyan-800 lg:hidden"></div><div class="absolute -right-20 top-1/3 size-64 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="relative w-full max-w-[470px]"><div class="mb-7 flex items-center justify-between lg:hidden"><a href="{{ route('home') }}" class="flex items-center gap-3 text-white"><img src="/icons/smart-school.svg" class="size-11 rounded-xl" alt=""><b>Smart School</b></a><button x-show="installable" x-cloak @click="installApp" class="rounded-xl bg-white/15 px-3 py-2 text-xs font-bold text-white backdrop-blur">Instal aplikasi</button></div>
            <div class="rounded-[2rem] border border-white/80 bg-white/95 p-6 shadow-[0_25px_80px_-30px_rgba(15,23,42,.35)] backdrop-blur-xl sm:p-9">{{ $slot }}</div>
            <div class="mt-5 flex items-center justify-center gap-3 text-xs text-slate-500"><span class="inline-flex items-center gap-1" :class="online?'text-emerald-700':'text-amber-700'"><span x-text="online?'● Online':'● Offline'"></span></span><span>•</span><a href="{{ route('home') }}" class="font-semibold hover:text-emerald-700">Kembali ke website sekolah</a></div>
        </div>
    </section>
</main>
</body></html>
