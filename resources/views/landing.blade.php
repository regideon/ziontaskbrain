<x-layouts.guest-main title="ZionTaskBrain - Home">
    <nav class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/60">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-white">
                    <span class="text-lg font-black">ZT</span>
                </div>
                <span class="text-xl font-bold tracking-tight">ZionTaskBrain</span>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('documentation') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Documentation</a>
                    <a href="{{ route('task-board') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Open App</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Logout</button>
                    </form>
                @else
                    <a href="{{ route('documentation') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Documentation</a>
                    <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-700">Login</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        <section class="px-6 py-8 sm:pt-16 sm:pb-8 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-6xl">Plan Smarter. Finish Faster. <span class="text-primary">Feel In Control.</span></h1>
                <p class="mt-6 text-lg text-slate-600 dark:text-slate-300">ZionTaskBrain helps you organize your day, focus on what matters, and turn overwhelming to-do lists into clear daily wins. Start free and see results in minutes.</p>
                <div class="mt-10 flex items-center justify-center gap-4">
                    <a href="{{ auth()->check() ? route('task-board') : route('login') }}" class="rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white">Start with Task Board</a>
                    <a href="#agent-examples" class="rounded-lg border border-slate-300 px-6 py-3 text-base font-semibold dark:border-slate-700">See /agents examples</a>
                </div>
            </div>
        </section>

        <section class="bg-white py-16 dark:bg-slate-900/30">
            <div class="mx-auto grid max-w-7xl gap-6 px-6 lg:grid-cols-3 lg:px-8">
                <article class="rounded-xl border border-slate-200 p-6 shadow-sm dark:border-slate-700">
                    <h3 class="text-lg font-bold text-primary">TaskCreatorAgent</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 font-bold">Think of this like a smart helper that turns your sentence into a real to-do.</p>
                    <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800">You type: “Tomorrow 9am call investor, high priority.”<br>It creates the task for you, so you don’t fill every box manually.</p>
                </article>

                <article class="rounded-xl border border-slate-200 p-6 shadow-sm dark:border-slate-700">
                    <h3 class="text-lg font-bold text-primary">DailySummaryAgent</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 font-bold">This is like a coach that checks your task numbers and gives advice.</p>
                    <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800">It looks at things like: how many tasks are done, pending, overdue.<br>Then it gives a short “what to focus on today” summary.</p>
                </article>

                <article class="rounded-xl border border-slate-200 p-6 shadow-sm dark:border-slate-700">
                    <h3 class="text-lg font-bold text-primary">Run Prioritizer</h3>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 font-bold">This is like a teacher sorting homework by what is most important first.</p>
                    <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-800">It scores tasks (95, 90, 65) based on urgency, due date, priority.<br>So you do the right task first, not just the easiest one.</p>
                </article>
            </div>
        </section>

        <section id="agent-examples" class="py-16">
            <div class="mx-auto max-w-5xl px-6 lg:px-8">
                <h2 class="text-2xl font-bold">Beginner Examples for <code>/agents</code></h2>
                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    <article class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-sm font-semibold">Example 1: Create via Agent</p>
                        <pre class="mt-2 rounded-lg bg-slate-50 p-3 text-xs dark:bg-slate-800">Prompt:
Tomorrow at 9am call investor, high priority, tag finance

Expected:
Task saved with due date + priority + tag.</pre>
                    </article>
                    <article class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-sm font-semibold">Example 2: Daily Summary</p>
                        <pre class="mt-2 rounded-lg bg-slate-50 p-3 text-xs dark:bg-slate-800">Prompt:
Give me a short summary and top 3 priorities for today.

Expected:
Counts + tactical next actions.</pre>
                    </article>
                    <article class="rounded-xl border border-slate-200 p-5 dark:border-slate-700 lg:col-span-2">
                        <p class="text-sm font-semibold">Example 3: Run Prioritizer</p>
                        <pre class="mt-2 rounded-lg bg-slate-50 p-3 text-xs dark:bg-slate-800">Click "Run Prioritizer"

Expected:
Tasks scored and sorted by urgency/impact.</pre>
                    </article>
                </div>
                <div class="mt-8">
                    @auth
                        <a href="{{ route('agents-lab') }}" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Open Agents Module</a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white">Create account to try /agents</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>
</x-layouts.guest-main>
