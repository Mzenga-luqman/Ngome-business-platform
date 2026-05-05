<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.forgot_password_title') }} - Ngome POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 font-sans antialiased text-white">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto min-h-screen max-w-3xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="mb-8 flex items-center justify-between gap-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold text-blue-100 hover:bg-white/20 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('ui.back_to_login') }}
            </a>

            <div class="flex items-center overflow-hidden rounded-lg border border-white/20 bg-white/10 text-xs font-semibold">
                <a href="{{ route('language.switch', 'en') }}"
                   class="px-2.5 py-1.5 transition-colors {{ app()->getLocale() === 'en' ? 'bg-white text-blue-700' : 'text-blue-100 hover:bg-white/20' }}">
                    EN
                </a>
                <a href="{{ route('language.switch', 'sw') }}"
                   class="px-2.5 py-1.5 transition-colors {{ app()->getLocale() === 'sw' ? 'bg-white text-blue-700' : 'text-blue-100 hover:bg-white/20' }}">
                    SW
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-400/40 bg-emerald-500/10 px-5 py-4 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-400/40 bg-red-500/10 px-5 py-4 text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6 sm:p-8">
            <h1 class="text-2xl font-black text-white sm:text-3xl">{{ __('ui.forgot_password_title') }}</h1>
            <p class="mt-2 text-sm text-blue-100/80">{{ __('ui.forgot_password_subtitle') }}</p>

            <form method="POST" action="{{ route('password.request-reset') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-blue-200">{{ __('ui.username') }}</label>
                    <input name="username" type="text" value="{{ old('username') }}" required
                           placeholder="{{ __('ui.username_optional') }}"
                           class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-blue-400/60 focus:bg-white/20 focus:ring-4 focus:ring-blue-400/20">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-blue-200">{{ __('ui.email_address') }}</label>
                    <input name="email" type="email" value="{{ old('email') }}" required
                           placeholder="{{ __('ui.email_address') }}"
                           class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-blue-400/60 focus:bg-white/20 focus:ring-4 focus:ring-blue-400/20">
                </div>

                <button type="submit" class="w-full rounded-xl bg-emerald-400 px-4 py-3 text-sm font-black uppercase text-slate-900 transition hover:bg-emerald-300">
                    {{ __('ui.request_password_reset') }}
                </button>
            </form>
        </section>
    </div>
</body>
</html>
