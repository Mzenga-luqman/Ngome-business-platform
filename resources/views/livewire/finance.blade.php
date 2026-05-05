<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Finance & Profit') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('Track expenses, compare against sales, and see real profit.') }}</p>
        </div>
        <select wire:model.live="period"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="this_month">{{ __('This Month') }}</option>
            <option value="today">{{ __('Today') }}</option>
            <option value="last_7_days">{{ __('Last 7 Days') }}</option>
            <option value="last_month">{{ __('Last Month') }}</option>
            <option value="all_time">{{ __('All Time') }}</option>
        </select>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Total Sales --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/25 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Sales') }}</p>
                <p class="text-xl font-extrabold text-emerald-700 mt-0.5">TZS {{ number_format($totalSales, 2) }}</p>
            </div>
        </div>

        {{-- Total Expenses --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center shadow-lg shadow-red-500/25 flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Total Expenses') }}</p>
                <p class="text-xl font-extrabold text-red-600 mt-0.5">TZS {{ number_format($totalExpenses, 2) }}</p>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="bg-white rounded-2xl shadow border border-slate-100 p-6 flex items-center gap-4
                    {{ $netProfit >= 0 ? 'ring-2 ring-emerald-200' : 'ring-2 ring-red-200' }}">
            <div class="w-12 h-12 rounded-xl {{ $netProfit >= 0 ? 'bg-blue-600' : 'bg-red-600' }} flex items-center justify-center shadow-lg flex-shrink-0
                        {{ $netProfit >= 0 ? 'shadow-blue-600/25' : 'shadow-red-600/25' }}">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('Net Profit') }}</p>
                <p class="text-xl font-extrabold mt-0.5 {{ $netProfit >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                    {{ $netProfit >= 0 ? '+' : '-' }}TZS {{ number_format(abs($netProfit), 2) }}
                </p>
                <p class="text-xs mt-0.5 {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-red-400' }}">
                    {{ $netProfit >= 0 ? __('Profitable') : __('Running at a loss') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Add / Edit Expense Form --}}
    <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
        <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            {{ $editingId ? __('Edit Expense') : __('Record New Expense') }}
        </h2>

        <form wire:submit="saveExpense" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Name --}}
            <div class="xl:col-span-1">
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Expense Name') }}</label>
                <input wire:model="expenseName" type="text" placeholder="e.g. Electricity, Rent"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expenseName') border-red-400 @enderror"/>
                @error('expenseName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Amount (TZS)') }}</label>
                <input wire:model="expenseAmount" type="number" step="0.01" min="0" placeholder="0.00"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expenseAmount') border-red-400 @enderror"/>
                @error('expenseAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Date') }}</label>
                <input wire:model="expenseDate" type="date"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expenseDate') border-red-400 @enderror"/>
                @error('expenseDate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ __('Notes (optional)') }}</label>
                <input wire:model="expenseNotes" type="text" placeholder="Optional note"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            {{-- Actions --}}
            <div class="sm:col-span-2 xl:col-span-4 flex items-center gap-3">
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
                    {{ $editingId ? __('Update Expense') : __('Save Expense') }}
                </button>
                @if($editingId)
                <button type="button" wire:click="cancelEdit"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                    {{ __('Cancel') }}
                </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Expenses List --}}
    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-700">{{ __('Expense Records') }}</h2>
            <span class="ml-auto text-xs text-slate-400">{{ __(':count record(s)', ['count' => $expensesList->count()]) }}</span>
        </div>

        @if($expensesList->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-400">
                <p class="font-semibold text-slate-600">{{ __('No expenses recorded for this period.') }}</p>
                <p class="mt-1">{{ __('Use the form above to add your first expense.') }}</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Expense') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Notes') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($expensesList as $expense)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $editingId === $expense->id ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-3.5 text-slate-500 tabular-nums">
                            {{ $expense->date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $expense->name }}</td>
                        <td class="px-6 py-3.5 text-slate-400 text-xs">{{ $expense->notes ?: '—' }}</td>
                        <td class="px-6 py-3.5 font-bold text-red-600">TZS {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <button wire:click="editExpense({{ $expense->id }})"
                                        class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="deleteExpense({{ $expense->id }})"
                                        wire:confirm="Delete this expense record?"
                                        class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200">
                        <td colspan="3" class="px-6 py-3.5 text-sm font-bold text-slate-700">{{ __('Total') }}</td>
                        <td class="px-6 py-3.5 font-extrabold text-red-700">TZS {{ number_format($totalExpenses, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
