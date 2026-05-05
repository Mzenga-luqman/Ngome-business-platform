<div class="space-y-6">

    {{-- Account access summary --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow border border-slate-100 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-500">{{ __('Account Isolation') }}</p>
                    <h2 class="text-lg font-bold text-slate-800 mt-1">{{ __(':name workspace', ['name' => $accountUser->name]) }}</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ __('Only this account and its workers can access these products, sales, reports, and expenses.') }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:min-w-[260px]">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-widest text-slate-400 font-bold">{{ __('Plan limit') }}</p>
                        <p class="text-xl font-black text-slate-900 mt-1">{{ $workerLimit }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ __('worker accounts') }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-widest text-emerald-500 font-bold">{{ __('Available') }}</p>
                        <p class="text-xl font-black text-emerald-700 mt-1">{{ $remainingWorkerSlots }}</p>
                        <p class="text-xs text-emerald-600 mt-1">{{ __('slots left') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-600/20">
            <p class="text-xs uppercase tracking-[0.25em] text-blue-200 font-bold">{{ __('Access Role') }}</p>
            <h3 class="text-xl font-black mt-2">
                @if(auth()->user()->is_worker)
                    {{ __('Worker Account') }}
                @elseif(auth()->user()->is_admin)
                    {{ __('Platform Admin') }}
                @else
                    {{ __('Main Account') }}
                @endif
            </h3>
            <p class="text-sm text-blue-100 mt-2 leading-relaxed">
                @if(auth()->user()->is_worker)
                    You share the same business data as your main account, but cannot create extra workers.
                @elseif(auth()->user()->is_admin)
                    Admin users can review subscription payments, but business data remains isolated per account.
                @else
                    Create worker logins for your team without exposing data to other businesses.
                @endif
            </p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Staff Tracking') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Sales performance per team member.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live="range"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all_time">{{ __('All Time') }}</option>
                <option value="this_month">{{ __('This Month') }}</option>
                <option value="today">{{ __('Today') }}</option>
            </select>
        </div>
    </div>

    {{-- Worker management --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow border border-slate-100 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Worker Accounts') }}</h2>
                    <p class="text-sm text-slate-500 mt-1">Create login accounts that share this account’s inventory and sales data.</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                    {{ $workers->count() }}/{{ $workerLimit }} used
                </span>
            </div>

            @if(auth()->user()->canManageWorkers())
                <form wire:submit="createWorker" class="space-y-4 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Worker name') }}</label>
                        <input type="text" wire:model.defer="workerName" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Cashier One">
                        @error('workerName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Worker email') }}</label>
                        <input type="email" wire:model.defer="workerEmail" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="worker@example.com">
                        @error('workerEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Temporary password') }}</label>
                        <input type="text" wire:model.defer="workerPassword" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="At least 8 characters">
                        @error('workerPassword') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($remainingWorkerSlots > 0)
                        <button type="submit" class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 transition-colors">
                            {{ __('Create Worker Account') }}
                        </button>
                    @else
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            Your current subscription has reached its worker limit. Upgrade the plan to add more workers.
                        </div>
                    @endif
                </form>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                    @if(auth()->user()->is_worker)
                        Worker accounts cannot create more worker logins.
                    @else
                        Platform admins do not create business worker accounts from this page.
                    @endif
                </div>
            @endif
        </div>

        <div class="xl:col-span-3 bg-white rounded-2xl shadow border border-slate-100 p-6">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">{{ __('Current Worker List') }}</h2>
                    <p class="text-sm text-slate-500 mt-1">Each worker below sees the same data as the main account.</p>
                </div>
            </div>

            @if($workers->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center">
                    <p class="font-semibold text-slate-700">No worker accounts yet.</p>
                    <p class="text-sm text-slate-500 mt-1">Create your first worker login to let staff sell using the same shared business data.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($workers as $worker)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($worker->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $worker->name }}</p>
                                    <p class="text-sm text-slate-500 truncate">{{ $worker->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="rounded-full px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-700">{{ __('Worker') }}</span>
                                @if(auth()->user()->canManageWorkers())
                                    <button wire:click="deleteWorker({{ $worker->id }})"
                                            wire:confirm="Remove this worker account?"
                                            class="rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                        {{ __('Remove') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Top Performer Banner --}}
    @if(!empty($staffStats))
    @php $top = $staffStats[0]; @endphp
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 flex items-center gap-5 shadow-lg shadow-blue-600/25">
        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-extrabold text-white shadow">
            {{ strtoupper(substr($top['name'], 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest">{{ __('Top Performer') }}</p>
            <p class="text-white text-xl font-extrabold mt-0.5 truncate">{{ $top['name'] }}</p>
            <p class="text-blue-200 text-sm mt-1">
                TZS {{ number_format($top['total_revenue'], 2) }} revenue &nbsp;·&nbsp;
                {{ $top['total_transactions'] }} transactions
            </p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10">
            <svg class="w-8 h-8 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>
    </div>
    @endif

    {{-- Staff Table --}}
    @if(empty($staffStats))
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-10 text-center">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="font-semibold text-slate-700">{{ __('No staff sales data yet.') }}</p>
            <p class="text-sm text-slate-400 mt-1">Sales will be tracked per staff member once logins are configured.</p>
        </div>
    @else
    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Staff Name') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Transactions') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Items Sold') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Total Revenue') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Performance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($staffStats as $i => $staff)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            @if($i === 0)
                                <span class="w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold flex items-center justify-center">🥇</span>
                            @elseif($i === 1)
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center">🥈</span>
                            @elseif($i === 2)
                                <span class="w-7 h-7 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center">🥉</span>
                            @else
                                <span class="text-slate-400 font-semibold text-xs pl-2">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 flex-shrink-0">
                                    {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $staff['name'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $staff['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-700 font-semibold">{{ number_format($staff['total_transactions']) }}</td>
                        <td class="px-6 py-4 text-slate-700 font-semibold">{{ number_format($staff['total_items']) }}</td>
                        <td class="px-6 py-4 font-bold text-blue-700">TZS {{ number_format($staff['total_revenue'], 2) }}</td>
                        <td class="px-6 py-4">
                            @if($i === 0)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">{{ __('Top Performer') }}</span>
                            @elseif($staff['total_transactions'] > 10)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">{{ __('Active') }}</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">{{ __('Regular') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Info box about staff tracking --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-blue-800">{{ __('Staff tracking is ready') }}</p>
            <p class="text-xs text-blue-600 mt-0.5">
                Each sale is automatically linked to the logged-in account or worker.
                Existing sales without a user are shown as "System / Admin" only inside the same account.
                Other businesses cannot access this data.
            </p>
        </div>
    </div>
</div>
