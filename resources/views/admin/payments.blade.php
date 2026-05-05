<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Payment Approvals</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @php
        $appTimezone = config('app.timezone');
    @endphp
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black">Admin Payment Approvals</h1>
                <p class="text-sm text-white/70">Approve payments, review approval history, and monitor all users.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('subscription.plans') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">Subscription Page</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold hover:bg-white/20">Logout</button>
                </form>
            </div>
        </div>

        <div class="mb-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-300">Pending</p>
                <p class="mt-2 text-3xl font-black">{{ $pendingPayments->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-300">Approved</p>
                <p class="mt-2 text-3xl font-black text-emerald-400">{{ $approvedPayments->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-300">Users</p>
                <p class="mt-2 text-3xl font-black text-amber-300">{{ $users->count() }}</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-300">Password Reset Requests</p>
                <p class="mt-2 text-3xl font-black text-orange-300">{{ $pendingPasswordResetRequests->count() }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-400/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">{{ session('error') }}</div>
        @endif

        <div class="mb-6 rounded-2xl border border-white/15 bg-white/10 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-black">Password Reset Requests</h2>
                <p class="text-xs text-white/60 mt-1">Users submit username + email. Admin sets a new password here.</p>
            </div>

            @if($pendingPasswordResetRequests->isEmpty())
                <div class="px-6 py-10 text-center text-white/70">No pending password reset requests.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white/10">
                                <th class="px-4 py-3 text-left">Requested User</th>
                                <th class="px-4 py-3 text-left">Submitted With</th>
                                <th class="px-4 py-3 text-left">Requested At</th>
                                <th class="px-4 py-3 text-left">Reset Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($pendingPasswordResetRequests as $resetRequest)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold">{{ $resetRequest->user->name }}</p>
                                        <p class="text-xs text-white/60">{{ $resetRequest->user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-xs">Username: {{ $resetRequest->requested_username }}</p>
                                        <p class="text-xs text-white/70">Email: {{ $resetRequest->requested_email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-white/80">
                                        {{ $resetRequest->requested_at?->copy()->timezone($appTimezone)->format('d M Y, h:i A') ?? '--' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.password-resets.reset', $resetRequest) }}" class="grid gap-2 sm:grid-cols-3">
                                            @csrf
                                            <input type="password" name="new_password" required minlength="8" placeholder="New password"
                                                class="rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <input type="password" name="new_password_confirmation" required minlength="8" placeholder="Confirm password"
                                                class="rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                                                Reset Password
                                            </button>
                                            <input type="text" name="notes" placeholder="Optional note"
                                                class="sm:col-span-3 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mb-6 rounded-2xl border border-white/15 bg-white/10 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-black">Recent Password Resets</h2>
            </div>

            @if($recentPasswordResetRequests->isEmpty())
                <div class="px-6 py-8 text-center text-white/70">No password resets completed yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white/10">
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Processed By</th>
                                <th class="px-4 py-3 text-left">Processed At</th>
                                <th class="px-4 py-3 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($recentPasswordResetRequests as $resetRequest)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold">{{ $resetRequest->user->name }}</p>
                                        <p class="text-xs text-white/60">{{ $resetRequest->user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $resetRequest->processor?->name ?? 'System' }}</td>
                                    <td class="px-4 py-3 text-xs text-white/80">
                                        {{ $resetRequest->processed_at?->copy()->timezone($appTimezone)->format('d M Y, h:i A') ?? '--' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-white/70">{{ $resetRequest->notes ?: '--' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
        <div class="rounded-2xl border border-white/15 bg-white/10 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-black">Pending Payments</h2>
            </div>
            @if($pendingPayments->isEmpty())
                <div class="px-6 py-12 text-center text-white/70">No pending payments.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white/10">
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Plan</th>
                                <th class="px-4 py-3 text-left">Provider</th>
                                <th class="px-4 py-3 text-left">Invoice Code</th>
                                <th class="px-4 py-3 text-left">Phone</th>
                                <th class="px-4 py-3 text-left">Reference</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($pendingPayments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold">{{ $payment->user->name }}</p>
                                        <p class="text-xs text-white/60">{{ $payment->user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $payment->subscription->name }}</td>
                                    <td class="px-4 py-3 capitalize">{{ $payment->provider }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $payment->invoice->invoice_code }}</td>
                                    <td class="px-4 py-3">{{ $payment->phone_number }}</td>
                                    <td class="px-4 py-3">{{ $payment->payment_reference }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.payments.approve', $payment) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
            <h2 class="text-lg font-black mb-3">Recent Alerts</h2>
            <p class="mb-3 text-xs text-white/60">Unread opened this visit: {{ $unreadAlerts }}</p>
            @if($recentNotifications->isEmpty())
                <p class="text-sm text-white/70">No alerts yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentNotifications as $notification)
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-sm font-semibold">{{ $notification->data['message'] ?? 'New payment alert' }}</p>
                            <p class="mt-1 text-xs text-blue-300 uppercase tracking-[0.2em]">{{ $notification->created_at->diffForHumans() }}</p>
                            <div class="mt-2 text-xs text-white/70 grid grid-cols-2 gap-1">
                                <p>Invoice: <span class="font-mono">{{ $notification->data['invoice_code'] ?? '--' }}</span></p>
                                <p>Provider: <span class="capitalize">{{ $notification->data['provider'] ?? '--' }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        </div>

        <div class="mt-6 rounded-2xl border border-white/15 bg-white/10 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-black">Approved Payment History</h2>
                <p class="text-xs text-white/60 mt-1">Includes reference, approving admin, and approval date/time.</p>
            </div>

            @if($approvedPayments->isEmpty())
                <div class="px-6 py-10 text-center text-white/70">No approved payments yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white/10">
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Plan</th>
                                <th class="px-4 py-3 text-left">Invoice</th>
                                <th class="px-4 py-3 text-left">Phone</th>
                                <th class="px-4 py-3 text-left">Reference</th>
                                <th class="px-4 py-3 text-left">Approved By</th>
                                <th class="px-4 py-3 text-left">Approved At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($approvedPayments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold">{{ $payment->user->name }}</p>
                                        <p class="text-xs text-white/60">{{ $payment->user->email }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $payment->subscription->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $payment->invoice->invoice_code }}</td>
                                    <td class="px-4 py-3">{{ $payment->phone_number }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $payment->payment_reference }}</td>
                                    <td class="px-4 py-3">{{ $payment->reviewer?->name ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        @if($payment->reviewed_at)
                                            {{ $payment->reviewed_at->copy()->timezone($appTimezone)->format('d M Y, h:i A') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-2xl border border-white/15 bg-white/10 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-black">All Users</h2>
            </div>

            @if($users->isEmpty())
                <div class="px-6 py-10 text-center text-white/70">No users found.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-white/10">
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Username</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Subscription</th>
                                <th class="px-4 py-3 text-left">Expiry</th>
                                <th class="px-4 py-3 text-left">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-xs">{{ $user->username ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @if($user->is_admin)
                                            Admin
                                        @elseif($user->is_worker)
                                            Worker
                                        @else
                                            Owner
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $user->subscription?->name ?? 'Not subscribed' }}</td>
                                    <td class="px-4 py-3">
                                        {{ $user->subscription_expiry ? $user->subscription_expiry->copy()->timezone($appTimezone)->format('d M Y, h:i A') : '—' }}
                                    </td>
                                    <td class="px-4 py-3">{{ $user->created_at ? $user->created_at->copy()->timezone($appTimezone)->format('d M Y, h:i A') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
