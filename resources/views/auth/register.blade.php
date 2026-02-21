<x-layouts.guest-main title="ZionTaskBrain - Register">
    <div class="relative flex min-h-screen w-full flex-col items-center justify-center p-4">
        <div class="w-full max-w-[460px]">
            <div class="mb-8 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10 text-primary text-2xl font-black">ZT</div>
                <h1 class="text-2xl font-bold tracking-tight">Create Your Account</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Start learning Task Tools, Agents, and MCP.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-700 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register.perform') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm dark:border-slate-700 dark:bg-slate-800" placeholder="Your full name">
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm dark:border-slate-700 dark:bg-slate-800" placeholder="name@example.com">
                    </div>
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium">Password</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm dark:border-slate-700 dark:bg-slate-800" placeholder="At least 8 characters">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm dark:border-slate-700 dark:bg-slate-800" placeholder="Repeat password">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-primary py-3 text-sm font-semibold text-white">Create Account</button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-400">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-primary">Login</a></p>
            <p class="mt-2 text-center text-xs"><a href="{{ route('home') }}" class="text-slate-500 hover:text-primary">Back to landing page</a></p>
        </div>
    </div>
</x-layouts.guest-main>
