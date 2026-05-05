<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Ngome Shop' }} — Ngome Technology</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Custom scroll-bar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0b1f3a; }
        ::-webkit-scrollbar-thumb { background: #1e40af; border-radius: 3px; }

        /* Sidebar transition */
        #sidebar { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }

        /* Page-enter animation */
        .page-content { animation: fadeSlideIn .35s ease both; }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="h-full bg-slate-50 font-sans antialiased">

{{-- ============================================================ --}}
{{-- MOBILE OVERLAY --}}
{{-- ============================================================ --}}
<div id="overlay" onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

{{-- ============================================================ --}}
{{-- SIDEBAR --}}
{{-- ============================================================ --}}
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 z-40
              bg-[#0B1F3A] flex flex-col shadow-2xl
              -translate-x-full lg:translate-x-0">

    {{-- Brand --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-blue-800/50">
        <div class="w-9 h-9 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/40">
            {{-- Shop bag icon --}}
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <div>
            <p class="text-white font-bold text-sm leading-none">{{ __('Ngome Shop') }}</p>
            <p class="text-blue-400 text-xs mt-0.5">{{ __('POS & Inventory') }}</p>
        </div>
    </div>

    {{-- Nav links --}}
    <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">

        {{-- MAIN --}}
        <p class="px-4 pt-2 pb-1 text-[10px] font-bold text-blue-500/60 uppercase tracking-widest">{{ __('Main') }}</p>

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'dashboard'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'dashboard' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            {{ __('Dashboard') }}
            @if(($active ?? '') === 'dashboard')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Products --}}
        <a href="{{ route('products') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'products'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'products' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            {{ __('Products') }}
            @if(($active ?? '') === 'products')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- POS --}}
        <a href="{{ route('pos') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'pos'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'pos' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{ __('POS - Sell') }}
            @if(($active ?? '') === 'pos')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Sales --}}
        <a href="{{ route('sales') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'sales'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'sales' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            {{ __('Sales') }}
            @if(($active ?? '') === 'sales')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- INTELLIGENCE --}}
        <p class="px-4 pt-4 pb-1 text-[10px] font-bold text-blue-500/60 uppercase tracking-widest">{{ __('Intelligence') }}</p>

        {{-- Reports --}}
        <a href="{{ route('reports') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'reports'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'reports' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            {{ __('Reports') }}
            @if(($active ?? '') === 'reports')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Stock Insights --}}
        <a href="{{ route('stock-insights') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'stock-insights'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'stock-insights' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            {{ __('Stock Insights') }}
            @if(($active ?? '') === 'stock-insights')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Predictions --}}
        <a href="{{ route('predictions') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'predictions'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'predictions' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            {{ __('Predictions') }}
            @if(($active ?? '') === 'predictions')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- BUSINESS --}}
        <p class="px-4 pt-4 pb-1 text-[10px] font-bold text-blue-500/60 uppercase tracking-widest">{{ __('Business') }}</p>

        {{-- Staff --}}
        <a href="{{ route('staff') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'staff'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'staff' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ __('Staff') }}
            @if(($active ?? '') === 'staff')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Finance --}}
        <a href="{{ route('finance') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'finance'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'finance' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __('Finance') }}
            @if(($active ?? '') === 'finance')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        {{-- Creditors --}}
        <a href="{{ route('creditors') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'creditors'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'creditors' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 9V7a5 5 0 00-10 0v2M5 9h14l1 10a2 2 0 01-2 2H6a2 2 0 01-2-2L5 9zm7 4v4"/>
            </svg>
            {{ __('Creditors') }}
            @if(($active ?? '') === 'creditors')
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-300"></span>
            @endif
        </a>

        <p class="px-4 pt-4 pb-1 text-[10px] font-bold text-blue-500/60 uppercase tracking-widest">{{ __('Subscription') }}</p>

        <a href="{{ route('subscription.plans') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'subscription'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'subscription' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __('Subscription') }}
        </a>

        <a href="{{ route('profile.edit') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'profile'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'profile' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A7.97 7.97 0 0112 14a7.97 7.97 0 016.879 3.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ __('Profile Settings') }}
        </a>

        @if(auth()->user()->is_admin)
        <a href="{{ route('admin.payments') }}"
           class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                  {{ ($active ?? '') === 'admin-payments'
                       ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                       : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ ($active ?? '') === 'admin-payments' ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ __('Admin Payments') }}
            @php($adminAlertCount = auth()->user()->unreadNotifications()->where('type', \App\Notifications\NewSubscriptionPaymentNotification::class)->count())
            @if($adminAlertCount > 0)
                <span class="ml-auto inline-flex min-w-6 items-center justify-center rounded-full bg-emerald-400 px-1.5 py-0.5 text-[10px] font-black text-slate-950">{{ $adminAlertCount }}</span>
            @endif
        </a>
        @endif
    </nav>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-blue-800/50">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile photo" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-700/40">
                @else
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-400 text-xs truncate">{{ auth()->user()->username ?: auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-blue-700 px-3 py-2 text-xs font-semibold text-blue-200 hover:bg-blue-800/60 hover:text-white transition-colors">
                    {{ __('ui.logout') }}
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ============================================================ --}}
{{-- MAIN WRAPPER --}}
{{-- ============================================================ --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- TOP NAVBAR --}}
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-4 sm:px-6 h-16">

            {{-- Mobile hamburger --}}
            <button onclick="openSidebar()"
                    class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Brand title (desktop) --}}
            <div class="hidden lg:flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                <span class="text-slate-800 font-semibold text-sm">{{ __('Ngome Technology') }}</span>
            </div>

            {{-- Right section --}}
            <div class="flex items-center gap-3">
                {{-- Date --}}
                <span class="hidden sm:block text-xs text-slate-400 tabular-nums">
                    {{ now()->format('D, d M Y') }}
                </span>

                <div class="flex items-center gap-2">
                    <span class="hidden sm:block text-xs text-slate-500">{{ __('ui.language') }}</span>
                    <div class="flex items-center overflow-hidden rounded-lg border border-slate-200 bg-white text-xs font-semibold">
                        <a href="{{ route('language.switch', 'en') }}"
                           class="px-2.5 py-1.5 transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            EN
                        </a>
                        <a href="{{ route('language.switch', 'sw') }}"
                           class="px-2.5 py-1.5 transition-colors {{ app()->getLocale() === 'sw' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            SW
                        </a>
                    </div>
                </div>

                @if(auth()->user()->hasActiveSubscription())
                    <span class="hidden sm:inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                        {{ __('ui.days_left', ['count' => auth()->user()->remainingSubscriptionDays()]) }}
                    </span>
                @endif

                {{-- Notification bell --}}
                <button class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>

                {{-- User avatar --}}
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile photo" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-100">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white shadow">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-slate-700 leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ auth()->user()->username ?: auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="hidden sm:inline-flex rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                        {{ __('ui.profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                            {{ __('ui.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- BREADCRUMB / PAGE TITLE --}}
    <div class="bg-white border-b border-slate-100 px-4 sm:px-6 py-3">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ __('Ngome Shop') }}</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-blue-600 font-medium">{{ $title ?? __('Page') }}</span>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-4 sm:p-6 page-content">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="py-4 px-6 border-t border-slate-200 bg-white">
        <p class="text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Ngome Technology — {{ __('ui.copyright') }}
        </p>
    </footer>
</div>

@livewireScripts

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('-translate-x-full');
        document.getElementById('overlay').classList.remove('hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('overlay').classList.add('hidden');
    }
</script>

@stack('scripts')

</body>
</html>
