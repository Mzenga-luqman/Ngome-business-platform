<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Finance extends Component
{
    // Expense form
    public string $expenseName   = '';
    public string $expenseAmount = '';
    public string $expenseDate   = '';
    public string $expenseNotes  = '';

    // Filter
    public string $period = 'this_month';

    // Edit mode
    public ?int $editingId = null;

    protected function rules(): array
    {
        return [
            'expenseName'   => 'required|string|min:2|max:200',
            'expenseAmount' => 'required|numeric|min:0.01|max:9999999',
            'expenseDate'   => 'required|date',
            'expenseNotes'  => 'nullable|string|max:500',
        ];
    }

    public function mount(): void
    {
        $this->expenseDate = now()->toDateString();
    }

    private function dateRange(): array
    {
        return match ($this->period) {
            'today'        => ['start' => now()->startOfDay(),      'end' => now()->endOfDay()],
            'last_7_days'  => ['start' => now()->subDays(7),        'end' => now()->endOfDay()],
            'last_month'   => ['start' => now()->subMonth()->startOfMonth(), 'end' => now()->subMonth()->endOfMonth()],
            'all_time'     => ['start' => Carbon::createFromDate(2000, 1, 1)->startOfDay(), 'end' => now()->endOfDay()],
            default        => ['start' => now()->startOfMonth(),     'end' => now()->endOfMonth()],
        };
    }

    public function getTotalSalesProperty(): float
    {
        $r = $this->dateRange();
        return (float) Sale::forAccount(auth()->user())
            ->whereBetween('sold_at', [$r['start'], $r['end']])
            ->sum('total');
    }

    public function getTotalExpensesProperty(): float
    {
        $r = $this->dateRange();
        return (float) Expense::forAccount(auth()->user())
            ->whereBetween('date', [
                $r['start']->toDateString(),
                $r['end']->toDateString(),
            ])
            ->sum('amount');
    }

    public function getNetProfitProperty(): float
    {
        return $this->totalSales - $this->totalExpenses;
    }

    public function getExpensesListProperty()
    {
        $r = $this->dateRange();
        return Expense::forAccount(auth()->user())
            ->whereBetween('date', [
                $r['start']->toDateString(),
                $r['end']->toDateString(),
            ])
            ->orderByDesc('date')
            ->get();
    }

    public function saveExpense(): void
    {
        $this->validate();

        $data = [
            'account_owner_id' => auth()->user()->accountOwnerId(),
            'name'   => trim(strip_tags($this->expenseName)),
            'amount' => (float) $this->expenseAmount,
            'date'   => $this->expenseDate,
            'notes'  => trim(strip_tags($this->expenseNotes ?: '')),
        ];

        if ($this->editingId) {
            Expense::forAccount(auth()->user())
                ->whereKey($this->editingId)
                ->update($data);
            $this->editingId = null;
        } else {
            Expense::create($data);
        }

        $this->resetForm();
    }

    public function editExpense(int $id): void
    {
        $expense = Expense::forAccount(auth()->user())->findOrFail($id);
        $this->editingId      = $id;
        $this->expenseName    = $expense->name;
        $this->expenseAmount  = (string) $expense->amount;
        $this->expenseDate    = $expense->date->toDateString();
        $this->expenseNotes   = $expense->notes ?? '';
    }

    public function deleteExpense(int $id): void
    {
        Expense::forAccount(auth()->user())->whereKey($id)->delete();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->expenseName   = '';
        $this->expenseAmount = '';
        $this->expenseDate   = now()->toDateString();
        $this->expenseNotes  = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.finance', [
            'expensesList'   => $this->expensesList,
            'totalSales'     => $this->totalSales,
            'totalExpenses'  => $this->totalExpenses,
            'netProfit'      => $this->netProfit,
        ])->layout('layouts.app', ['title' => 'Finance & Profit', 'active' => 'finance']);
    }
}
