<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - Sistem POS Minimarket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" x-data="{
        fill(email, password) {
            $refs.email.value = email;
            $refs.password.value = password;
        }
    }">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600 text-white font-black text-2xl shadow-xl shadow-emerald-500/20 mb-3">
                M
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Sistem POS Minimarket</h1>
            <p class="text-sm text-slate-400 mt-1">Point of Sale & Manajemen Inventori Terintegrasi</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-6 sm:p-8 shadow-2xl">
            @if(session('success'))
                <div class="mb-5 p-3 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-rose-500/20 border border-rose-500/40 text-rose-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Pengguna</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        x-ref="email"
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="nama@minimarket.com"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900/70 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                    >
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        x-ref="password"
                        required 
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-900/70 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                    >
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-800 text-emerald-600 focus:ring-emerald-500">
                        <span>Ingat Sesi Saya</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 px-4 rounded-xl font-semibold text-white bg-emerald-600 hover:bg-emerald-500 focus:ring-4 focus:ring-emerald-500/30 transition-all shadow-lg shadow-emerald-600/30 text-sm mt-2"
                >
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Demo Login Presets -->
            <div class="mt-6 pt-5 border-t border-slate-700/60">
                <p class="text-xs text-slate-400 text-center mb-3">Pintasan Akun Demo:</p>
                <div class="grid grid-cols-3 gap-2">
                    <button 
                        type="button" 
                        @click="fill('admin@minimarket.com', 'password')"
                        class="px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 text-center transition-colors"
                    >
                        Owner / Admin
                    </button>
                    <button 
                        type="button" 
                        @click="fill('spv@minimarket.com', 'password')"
                        class="px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium border border-slate-700 text-center transition-colors"
                    >
                        Supervisor
                    </button>
                    <button 
                        type="button" 
                        @click="fill('kasir@minimarket.com', 'password')"
                        class="px-2 py-1.5 rounded-lg bg-emerald-950/60 hover:bg-emerald-900/60 text-emerald-300 text-xs font-medium border border-emerald-800 text-center transition-colors"
                    >
                        Kasir
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            D4 Teknik Informatika - Universitas Logistik & Bisnis Internasional
        </div>
    </div>
</body>
</html>
