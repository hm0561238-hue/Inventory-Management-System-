<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Inventory POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f5f7ff',
                            100: '#e8edff',
                            500: '#4f46e5',
                            600: '#4338ca',
                        },
                    },
                    boxShadow: {
                        soft: '0 20px 70px rgba(15, 23, 42, 0.08)',
                    },
                },
            },
        };
    </script>
    <style>
        .gradient-bg {
            background: radial-gradient(circle at top left, rgba(79, 70, 229, 0.18), transparent 28%),
                        radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.15), transparent 22%),
                        linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .dark .gradient-bg {
            background: radial-gradient(circle at top left, rgba(99, 102, 241, 0.22), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.12), transparent 24%),
                        linear-gradient(180deg, #0f172a 0%, #020617 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="gradient-bg min-h-screen flex items-center justify-center px-4 py-10">
        <div class="relative w-full max-w-2xl overflow-hidden rounded-[32px] border border-slate-200/80 bg-white/95 shadow-soft backdrop-blur-lg dark:border-slate-700/60 dark:bg-slate-900/90">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 lg:gap-8">
                <div class="hidden lg:flex flex-col justify-between bg-gradient-to-b from-brand-500 via-indigo-500 to-cyan-400 p-10 text-white">
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight">Welcome back.</h1>
                        <p class="mt-4 text-slate-100/85 leading-7">Sign in to manage inventory, process sales, and review performance with a modern POS dashboard.</p>
                    </div>
                    <div class="mt-8 rounded-3xl bg-white/10 p-6 backdrop-blur-sm">
                        <p class="text-sm uppercase tracking-[0.24em] text-slate-200/85">Quick access</p>
                        <p class="mt-4 text-lg font-medium">Cashiers and admins both log in here.</p>
                        <div class="mt-6 space-y-3 text-sm text-slate-200/90">
                            <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-white/80"></span>Fast checkout flow</div>
                            <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-white/80"></span>Secure account access</div>
                            <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-white/80"></span>Clear reporting tools</div>
                        </div>
                    </div>
                </div>
                <div class="p-8 sm:p-10 lg:p-12">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.28em] text-brand-600 dark:text-brand-300">Sign in</p>
                            <h2 class="mt-4 text-3xl font-semibold">Log in to your account</h2>
                        </div>
                        <button id="theme-toggle" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-slate-100 px-4 py-2 text-sm text-slate-700 shadow-sm transition hover:bg-slate-200 dark:border-slate-700/80 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            <span id="theme-label">Dark</span>
                            <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.7.7M5.34 6.34l-.7.7m15.32 6.32h-1M4 12H3m16.66 4.66l-.7-.7M5.34 17.66l-.7-.7" />
                            </svg>
                        </button>
                    </div>

                    <form id="login-form" method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-6">
                        @csrf
                        @if($errors->any())
                            <div class="rounded-3xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-950/20 dark:text-rose-200">
                                <ul class="mb-0 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="space-y-4 rounded-3xl border border-slate-200/80 bg-slate-50 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-950/80">
                            <div>
                                <label for="login-identifier" class="text-sm font-medium text-slate-700 dark:text-slate-200">Email or username</label>
                                <input id="login-identifier" name="email" type="text" autocomplete="username" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-brand-400 dark:focus:ring-brand-500/20" placeholder="you@example.com or username" value="{{ old('email') }}">
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="relative">
                                <label for="login-password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Password</label>
                                <div class="mt-2 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <input id="login-password" name="password" type="password" autocomplete="current-password" required class="w-full bg-transparent text-slate-900 outline-none placeholder:text-slate-400 dark:text-slate-100">
                                    <button type="button" id="login-password-toggle" aria-label="Show password" class="text-slate-500 transition hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                                        Show
                                    </button>
                                </div>
                                <p id="login-password-error" class="mt-2 text-sm text-rose-600 hidden" role="alert"></p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-300">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                Remember me
                            </label>
                            <a href="#" class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300 dark:hover:text-brand-200">Forgot password?</a>
                        </div>

                        <button id="login-submit" type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-brand-600 px-4 py-3 text-base font-semibold text-white shadow-lg shadow-brand-500/15 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400/30" data-loading-text="Logging in...">
                            Log In
                        </button>

                        <div class="relative text-center text-sm text-slate-500 dark:text-slate-400">
                            <span class="bg-white px-3 dark:bg-slate-900">OR</span>
                            <div class="absolute inset-x-0 top-1/2 h-px bg-slate-200 dark:bg-slate-700"></div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" class="inline-flex items-center justify-center rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:border-slate-600">
                                <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google logo" class="mr-2 h-5 w-5"> Google
                            </button>
                            <button type="button" class="inline-flex items-center justify-center rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:border-slate-600">
                                <img src="https://www.svgrepo.com/show/349702/apple.svg" alt="Apple logo" class="mr-2 h-5 w-5"> Apple
                            </button>
                        </div>

                        <p class="text-center text-sm text-slate-500 dark:text-slate-400">New here? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300">Create an account</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeLabel = document.getElementById('theme-label');
        const themeIcon = document.getElementById('theme-icon');

        const updateTheme = (isDark) => {
            document.documentElement.classList.toggle('dark', isDark);
            themeLabel.textContent = isDark ? 'Light' : 'Dark';
            themeIcon.innerHTML = isDark ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.7.7M5.34 6.34l-.7.7m15.32 6.32h-1M4 12H3m16.66 4.66l-.7-.7M5.34 17.66l-.7-.7" />' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a9 9 0 100 18 9 9 0 000-18z" />';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        };

        const savedTheme = localStorage.getItem('theme');
        updateTheme(savedTheme === 'dark');

        themeToggle.addEventListener('click', () => {
            updateTheme(!document.documentElement.classList.contains('dark'));
        });

        const passwordToggle = document.getElementById('login-password-toggle');
        const passwordInput = document.getElementById('login-password');

        passwordToggle.addEventListener('click', () => {
            const visible = passwordInput.type === 'text';
            passwordInput.type = visible ? 'password' : 'text';
            passwordToggle.textContent = visible ? 'Show' : 'Hide';
            passwordToggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });

        const form = document.getElementById('login-form');
        const submitButton = document.getElementById('login-submit');
        const identifierError = document.getElementById('login-identifier-error');
        const passwordError = document.getElementById('login-password-error');

        form.addEventListener('submit', (event) => {
            let valid = true;
            identifierError.classList.add('hidden');
            passwordError.classList.add('hidden');

            if (document.getElementById('login-identifier').value.trim().length < 3) {
                identifierError.textContent = 'Please enter a valid email or username.';
                identifierError.classList.remove('hidden');
                valid = false;
            }

            if (passwordInput.value.trim().length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters.';
                passwordError.classList.remove('hidden');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = submitButton.dataset.loadingText;
        });
    </script>
</body>
</html>
