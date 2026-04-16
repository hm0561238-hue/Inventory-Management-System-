<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Inventory POS</title>
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
                        radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.15), transparent 22%),
                        linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .dark .gradient-bg {
            background: radial-gradient(circle at top left, rgba(99, 102, 241, 0.22), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.12), transparent 24%),
                        linear-gradient(180deg, #020617 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="gradient-bg min-h-screen flex items-center justify-center px-4 py-10">
        <div class="relative w-full max-w-3xl overflow-hidden rounded-[32px] border border-slate-200/80 bg-white/95 shadow-soft backdrop-blur-lg dark:border-slate-700/60 dark:bg-slate-900/90">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 lg:gap-8">
                <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-emerald-500 via-cyan-500 to-sky-500 p-10 text-white">
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight">Create your account.</h1>
                        <p class="mt-4 leading-7 text-slate-100/90">Join Inventory POS for streamlined checkout, stock tracking, and sales reporting — all in one responsive platform.</p>
                    </div>
                    <div class="mt-8 rounded-3xl bg-white/10 p-6 backdrop-blur-sm">
                        <p class="text-sm uppercase tracking-[0.24em] text-slate-200/90">Get started quickly</p>
                        <p class="mt-4 text-lg font-medium">Secure login, instant onboarding, and checkout-ready UX.</p>
                    </div>
                </div>
                <div class="p-8 sm:p-10 lg:p-12">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.28em] text-emerald-600 dark:text-emerald-300">Sign up</p>
                            <h2 class="mt-4 text-3xl font-semibold">Create your account</h2>
                        </div>
                        <button id="theme-toggle" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-slate-100 px-4 py-2 text-sm text-slate-700 shadow-sm transition hover:bg-slate-200 dark:border-slate-700/80 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            <span id="theme-label">Dark</span>
                            <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-11.66l-.7.7M5.34 6.34l-.7.7m15.32 6.32h-1M4 12H3m16.66 4.66l-.7-.7M5.34 17.66l-.7-.7" />
                            </svg>
                        </button>
                    </div>

                    <form id="signup-form" method="POST" action="{{ route('register.post') }}" class="mt-8 space-y-6">
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
                                <label for="signup-username" class="text-sm font-medium text-slate-700 dark:text-slate-200">Username</label>
                                <input id="signup-username" name="username" type="text" autocomplete="username" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-500/20" placeholder="yourusername" value="{{ old('username') }}">
                                @error('username')
                                    <p class="mt-2 text-sm text-rose-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="signup-email" class="text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
                                <input id="signup-email" name="email" type="email" autocomplete="email" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-500/20" placeholder="you@example.com" value="{{ old('email') }}">
                                @error('email')
                                    <p class="mt-2 text-sm text-rose-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="relative">
                                <label for="signup-password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Password</label>
                                <div class="mt-2 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <input id="signup-password" name="password" type="password" autocomplete="new-password" required class="w-full bg-transparent text-slate-900 outline-none placeholder:text-slate-400 dark:text-slate-100" placeholder="At least 8 characters">
                                    <button type="button" id="signup-password-toggle" aria-label="Show password" class="text-slate-500 transition hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Show</button>
                                </div>
                                <p id="signup-password-error" class="mt-2 text-sm text-rose-600 hidden" role="alert"></p>
                            </div>
                            <div class="relative">
                                <label for="signup-password-confirm" class="text-sm font-medium text-slate-700 dark:text-slate-200">Confirm password</label>
                                <div class="mt-2 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                    <input id="signup-password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full bg-transparent text-slate-900 outline-none placeholder:text-slate-400 dark:text-slate-100" placeholder="Repeat your password">
                                    <button type="button" id="signup-password-confirm-toggle" aria-label="Show confirm password" class="text-slate-500 transition hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Show</button>
                                </div>
                                <p id="signup-password-confirm-error" class="mt-2 text-sm text-rose-600 hidden" role="alert"></p>
                            </div>
                        </div>

                        <label class="inline-flex items-start gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <input id="terms-checkbox" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>I agree to the <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-300">Terms & Conditions</a>.</span>
                        </label>
                        <p id="terms-error" class="mb-0 text-sm text-rose-600 hidden" role="alert"></p>

                        <button id="signup-submit" type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-emerald-600 px-4 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/30" data-loading-text="Creating account...">
                            Sign Up
                        </button>

                        <p class="text-center text-sm text-slate-500 dark:text-slate-400">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-300">Log in</a></p>
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

        const passwordToggle = document.getElementById('signup-password-toggle');
        const passwordInput = document.getElementById('signup-password');
        const confirmToggle = document.getElementById('signup-password-confirm-toggle');
        const confirmInput = document.getElementById('signup-password-confirm');

        const toggleVisibility = (button, input) => {
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.textContent = visible ? 'Show' : 'Hide';
            button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        };

        passwordToggle.addEventListener('click', () => toggleVisibility(passwordToggle, passwordInput));
        confirmToggle.addEventListener('click', () => toggleVisibility(confirmToggle, confirmInput));

        const form = document.getElementById('signup-form');
        const submitButton = document.getElementById('signup-submit');
        const usernameError = document.getElementById('signup-username-error');
        const emailError = document.getElementById('signup-email-error');
        const passwordError = document.getElementById('signup-password-error');
        const confirmError = document.getElementById('signup-password-confirm-error');
        const termsError = document.getElementById('terms-error');

        const validateEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

        form.addEventListener('submit', (event) => {
            let valid = true;
            [usernameError, emailError, passwordError, confirmError, termsError].forEach(el => el.classList.add('hidden'));

            if (document.getElementById('signup-username').value.trim().length < 3) {
                usernameError.textContent = 'Username must contain at least 3 characters.';
                usernameError.classList.remove('hidden');
                valid = false;
            }
            const emailValue = document.getElementById('signup-email').value.trim();
            if (!validateEmail(emailValue)) {
                emailError.textContent = 'Please enter a valid email address.';
                emailError.classList.remove('hidden');
                valid = false;
            }
            if (passwordInput.value.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
                passwordError.classList.remove('hidden');
                valid = false;
            }
            if (confirmInput.value !== passwordInput.value) {
                confirmError.textContent = 'Passwords do not match.';
                confirmError.classList.remove('hidden');
                valid = false;
            }
            if (!document.getElementById('terms-checkbox').checked) {
                termsError.textContent = 'You must accept the terms and conditions.';
                termsError.classList.remove('hidden');
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
