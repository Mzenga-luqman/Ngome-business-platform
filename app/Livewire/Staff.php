<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Staff extends Component
{
    public string $range = 'all_time'; // all_time | this_month | today
    public string $workerName = '';
    public string $workerEmail = '';
    public string $workerPassword = '';

    protected function rules(): array
    {
        return [
            'workerName' => 'required|string|min:2|max:120',
            'workerEmail' => 'required|email|max:255|unique:users,email',
            'workerPassword' => 'required|string|min:8|max:255',
        ];
    }

    public function getAccountUserProperty(): User
    {
        return auth()->user()->subscriptionAccount();
    }

    public function getWorkersProperty()
    {
        return $this->accountUser->workerAccounts()->get();
    }

    public function getWorkerLimitProperty(): int
    {
        return $this->accountUser->workerLimit();
    }

    public function getRemainingWorkerSlotsProperty(): int
    {
        return $this->accountUser->remainingWorkerSlots();
    }

    public function createWorker(): void
    {
        $user = auth()->user();

        if (! $user->canManageWorkers()) {
            $this->addError('workerName', 'Only the main account can create worker accounts.');
            return;
        }

        if ($this->remainingWorkerSlots <= 0) {
            $this->addError('workerName', 'Your current plan has reached the worker account limit.');
            return;
        }

        $validated = $this->validate();

        User::create([
            'name' => trim($validated['workerName']),
            'email' => strtolower(trim($validated['workerEmail'])),
            'password' => Hash::make($validated['workerPassword']),
            'account_owner_id' => $user->id,
            'is_worker' => true,
            'is_admin' => false,
            'subscription_id' => null,
            'subscription_expiry' => null,
        ]);

        $this->reset(['workerName', 'workerEmail', 'workerPassword']);
        $this->resetValidation();

        session()->flash('success', 'Worker account created successfully.');
    }

    public function getStaffStatsProperty(): array
    {
        $query = Sale::forAccount($this->accountUser)
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(quantity) as total_items'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->groupBy('user_id');

        $range = $this->range;
        if ($range === 'today') {
            $query->whereDate('sold_at', Carbon::today());
        } elseif ($range === 'this_month') {
            $query->whereMonth('sold_at', now()->month)
                  ->whereYear('sold_at', now()->year);
        }

        $salesByUser = $query->get()->keyBy('user_id');

        // Sales with user
        $withUser = $salesByUser->filter(fn ($_, $uid) => $uid !== null);
        // Sales without user (anonymous/legacy)
        $anonymous = $salesByUser->get(null);

        $users = User::withinAccount($this->accountUser)
            ->whereIn('id', $withUser->keys())
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($withUser as $userId => $stat) {
            $user = $users->get($userId);
            $result[] = [
                'name'               => $user?->name ?? 'Unknown',
                'email'              => $user?->email ?? '—',
                'total_transactions' => (int) $stat->total_transactions,
                'total_items'        => (int) $stat->total_items,
                'total_revenue'      => (float) $stat->total_revenue,
            ];
        }

        // Add anonymous/system sales
        if ($anonymous) {
            $result[] = [
                'name'               => 'System / Admin',
                'email'              => 'Pre-staff tracking',
                'total_transactions' => (int) $anonymous->total_transactions,
                'total_items'        => (int) $anonymous->total_items,
                'total_revenue'      => (float) $anonymous->total_revenue,
            ];
        }

        usort($result, fn ($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        return $result;
    }

    public function deleteWorker(int $workerId): void
    {
        $user = auth()->user();

        if (! $user->canManageWorkers()) {
            return;
        }

        $user->workerAccounts()->whereKey($workerId)->delete();

        session()->flash('success', 'Worker account removed.');
    }

    public function render()
    {
        return view('livewire.staff', [
            'accountUser' => $this->accountUser,
            'staffStats' => $this->staffStats,
            'workers' => $this->workers,
            'workerLimit' => $this->workerLimit,
            'remainingWorkerSlots' => $this->remainingWorkerSlots,
        ])->layout('layouts.app', ['title' => 'Staff Tracking', 'active' => 'staff']);
    }
}
