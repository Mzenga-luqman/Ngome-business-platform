<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Ngome POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 font-sans antialiased text-white">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-emerald-500/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto min-h-screen max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-blue-700 shadow-lg shadow-blue-500/40">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-black">Ngome POS</p>
                        <p class="text-sm text-blue-100">Modern POS System</p>
                    </div>
                </div>

                <h1 class="mt-6 text-4xl font-black sm:text-5xl">{{ __('ui.welcome_back') }}</h1>
                <p class="mt-3 max-w-2xl text-sm text-blue-100/80 sm:text-base">
                    {{ __('ui.login_intro') }}
                </p>
            </div>

            <div class="space-y-3">
                <div class="flex justify-end">
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

                <div class="rounded-3xl border border-white/10 bg-white/5 px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">{{ __('ui.live_time') }}</p>
                <p id="live-time" class="mt-2 text-2xl font-black">--:--:--</p>
                </div>
            </div>
        </div>

        @if (session('warning'))
            <div class="mb-4 rounded-2xl border border-amber-400/40 bg-amber-500/10 px-5 py-4 text-amber-100">
                <p class="font-black">Subscription required</p>
                <p class="mt-1 text-sm">{{ session('warning') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-400/40 bg-emerald-500/10 px-5 py-4 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 rounded-2xl border border-blue-400/40 bg-blue-500/10 px-5 py-4 text-blue-100">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-red-400/40 bg-red-500/10 px-5 py-4 text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                <h2 class="text-sm font-black uppercase tracking-widest text-blue-300 mb-4">{{ __('ui.existing_user_sign_in') }}</h2>
                <form method="POST" action="{{ route('login.store') }}" class="space-y-3">
                    @csrf

                    <div>
                           <input id="login" name="login" type="text" value="{{ old('login', old('email')) }}" required autofocus
                               placeholder="{{ __('ui.email_or_username') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-blue-400/60 focus:bg-white/20 focus:ring-4 focus:ring-blue-400/20">
                    </div>

                    <div class="relative">
                        <input id="password" name="password" type="password" required
                               placeholder="{{ __('ui.password') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 pr-20 text-sm text-white placeholder-white/50 outline-none transition focus:border-blue-400/60 focus:bg-white/20 focus:ring-4 focus:ring-blue-400/20">
                        <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-blue-300 hover:text-blue-200">
                            {{ __('ui.show') }}
                        </button>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-white px-4 py-3 text-sm font-black uppercase text-slate-900 transition hover:bg-slate-100">
                        {{ __('ui.sign_in') }}
                    </button>

                    <div class="pt-1 text-center">
                        <a href="{{ route('password.forgot') }}" class="text-xs font-semibold text-blue-200 hover:text-white transition-colors">
                            {{ __('ui.forgot_password') }}
                        </a>
                    </div>
                </form>
            </section>

            <section class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                <h2 class="text-sm font-black uppercase tracking-widest text-emerald-300 mb-4">{{ __('ui.create_new_account') }}</h2>
                <form method="POST" action="{{ route('register.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <input name="name" type="text" value="{{ old('name') }}" required
                               placeholder="{{ __('ui.full_name') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-emerald-400/60 focus:bg-white/20 focus:ring-4 focus:ring-emerald-400/20">
                    </div>

                    <div>
                        <input name="username" type="text" value="{{ old('username') }}"
                               placeholder="{{ __('ui.username_optional') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-emerald-400/60 focus:bg-white/20 focus:ring-4 focus:ring-emerald-400/20">
                    </div>

                    <div>
                        <input name="email" type="email" value="{{ old('email') }}" required
                               placeholder="{{ __('ui.email_address') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-emerald-400/60 focus:bg-white/20 focus:ring-4 focus:ring-emerald-400/20">
                    </div>

                    <div>
                        <input name="password" type="password" required
                               placeholder="{{ __('ui.password_min') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-emerald-400/60 focus:bg-white/20 focus:ring-4 focus:ring-emerald-400/20">
                    </div>

                    <div>
                        <input name="password_confirmation" type="password" required
                               placeholder="{{ __('ui.confirm_password') }}"
                               class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/50 outline-none transition focus:border-emerald-400/60 focus:bg-white/20 focus:ring-4 focus:ring-emerald-400/20">
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-emerald-400 px-4 py-3 text-sm font-black uppercase text-slate-900 transition hover:bg-emerald-300">
                        {{ __('ui.create_account') }}
                    </button>
                </form>

              <!--  <div class="mt-4 rounded-xl border border-white/10 bg-white/5 p-4 text-xs text-blue-100/70">
                    New users are redirected to subscription plans after account creation.
                </div> -->
            </section>
        
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('toggle-password');
        const timeEl = document.getElementById('live-time');

        if (passwordInput && togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                togglePasswordBtn.textContent = isHidden ? '{{ __('ui.hide') }}' : '{{ __('ui.show') }}';
            });
        }

        function updateLiveClock() {
            const now = new Date();
            const time = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
            if (timeEl) timeEl.textContent = time;
        }

        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    </script>
</body>

</html>
