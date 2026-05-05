@component('layouts.app', ['title' => 'Profile Settings', 'active' => 'profile'])
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-black text-amber-800">Security Settings</p>
                    <p class="text-xs text-amber-700">Use the dedicated change-password button below to update your password.</p>
                </div>
                <a href="#change-password" class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-700">
                    Go to Change Password
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('success_password'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success_password') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-800">Profile Information</h2>
                <p class="mt-1 text-sm text-slate-500">Update your name, username, email, and profile picture.</p>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf

                    <div class="flex items-center gap-4">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-slate-100">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div class="text-xs text-slate-500">JPG, PNG, WEBP. Max 5MB.</div>
                    </div>

                    <div>
                        <label for="profile_picture" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Profile picture</label>
                        <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="username" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" placeholder="e.g. ngome_owner"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700">
                        Save profile changes
                    </button>
                </form>
            </section>

            <section id="change-password" class="rounded-3xl border border-amber-200 bg-amber-50/40 p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-black text-slate-800">Change Password</h2>
                    <p class="mt-1 text-sm text-slate-500">This action only changes your password in the database.</p>
                </div>

                <form id="password-form" method="POST" action="{{ route('profile.password.update') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="current_password" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Current password</label>
                        <input id="current_password" name="current_password" type="password" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">New password</label>
                        <input id="new_password" name="new_password" type="password" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Confirm new password</label>
                        <input id="new_password_confirmation" name="new_password_confirmation" type="password" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl border-2 border-slate-900 bg-slate-900 px-4 py-3 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg transition hover:bg-slate-700"
                        style="display:flex;width:100%;background:#0f172a;color:#ffffff;border:2px solid #0f172a;min-height:48px;"
                    >
                        Update Password
                    </button>
                </form>
            </section>
        </div>
    </div>
@endcomponent
