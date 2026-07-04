<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TaskFlow - Sign In</title>
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter'] antialiased bg-zinc-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Animated background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 0s"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-indigo-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s"></div>
    </div>

    <!-- Floating shapes -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-20 h-20 border border-zinc-700/30 rounded-lg rotate-45 animate-float opacity-20"></div>
        <div class="absolute bottom-20 right-10 w-16 h-16 border border-zinc-700/30 rounded-full animate-float opacity-20" style="animation-delay: 1s"></div>
        <div class="absolute top-40 right-20 w-12 h-12 border border-zinc-700/30 rounded-lg rotate-12 animate-float opacity-15" style="animation-delay: 2s"></div>
        <div class="absolute bottom-40 left-20 w-14 h-14 border border-zinc-700/30 rounded-full animate-float opacity-15" style="animation-delay: 3s"></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md">
        <div id="login-card" class="bg-zinc-900/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-zinc-800/60 p-8 md:p-10 animate-fadeIn">

            <!-- Logo / Brand -->
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-500/25 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-lg">K</span>
                </div>
                <div class="text-left">
                    <h1 class="text-xl font-bold text-zinc-100">TaskFlow</h1>
                    <p class="text-sm text-zinc-400">Sign in to your account</p>
                </div>
            </div>

            <!-- Error Alert -->
            <div id="error-alert" class="hidden mb-5 bg-red-900/30 border border-red-800/50 text-red-400 text-sm rounded-xl px-4 py-3 flex items-center gap-2 animate-slideDown">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="error-message"></span>
            </div>

            <!-- Login Form -->
            <form id="login-form" class="flex flex-col gap-5">
                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-sm font-medium text-zinc-300">Email</label>
                    <div class="flex items-center gap-2.5 bg-zinc-800/80 border border-zinc-700 rounded-xl px-3.5 h-12 focus-within:ring-2 focus-within:ring-indigo-500/30 focus-within:border-indigo-500/50 transition-all duration-300">
                        <svg class="w-5 h-5 text-zinc-500 flex-shrink-0 group-focus-within:text-indigo-400 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <input
                            id="email"
                            type="email"
                            placeholder="Enter your email"
                            required
                            autocomplete="email"
                            class="flex-1 bg-transparent border-0 outline-none text-zinc-100 placeholder-zinc-500 h-full text-base p-0 focus:ring-0"
                        />
                    </div>
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-medium text-zinc-300">Password</label>
                    <div class="flex items-center gap-2.5 bg-zinc-800/80 border border-zinc-700 rounded-xl px-3.5 h-12 focus-within:ring-2 focus-within:ring-indigo-500/30 focus-within:border-indigo-500/50 transition-all duration-300">
                        <svg class="w-5 h-5 text-zinc-500 flex-shrink-0 group-focus-within:text-indigo-400 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <input
                            id="password"
                            type="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            class="flex-1 bg-transparent border-0 outline-none text-zinc-100 placeholder-zinc-500 h-full text-base p-0 focus:ring-0"
                        />
                        <button type="button" id="toggle-password" class="text-zinc-500 hover:text-zinc-300 transition-colors p-1" tabindex="-1">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input
                                id="remember"
                                type="checkbox"
                                class="peer sr-only"
                            />
                            <div class="w-4 h-4 border border-zinc-600 rounded bg-zinc-800 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 peer-checked:[&>*]:opacity-100">
                                <svg class="w-4 h-4 text-white opacity-0 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-zinc-400 group-hover:text-zinc-300 transition-colors">Remember me</span>
                    </label>
                    <button type="button" class="text-sm text-zinc-500 hover:text-indigo-400 transition-colors">
                        Forgot password?
                    </button>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    id="submit-btn"
                    class="relative w-full h-12 bg-indigo-600 hover:bg-indigo-700 disabled:bg-zinc-700 disabled:text-zinc-500 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-indigo-500/25 disabled:shadow-none flex items-center justify-center gap-2.5"
                >
                    <span id="btn-text">Sign In</span>
                    <svg id="btn-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-zinc-800"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-zinc-900 px-4 text-sm text-zinc-500">or continue with</span>
                </div>
            </div>

            <!-- Social Buttons -->
            <div class="flex flex-col gap-3">
                <a href="#" onclick="event.preventDefault()" class="flex items-center justify-center gap-3 w-full h-11 border border-zinc-800 hover:border-zinc-700 bg-zinc-800/50 hover:bg-zinc-800 rounded-xl text-zinc-300 text-sm font-medium transition-all duration-200 cursor-not-allowed opacity-60">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google
                </a>
                <a href="#" onclick="event.preventDefault()" class="flex items-center justify-center gap-3 w-full h-11 border border-zinc-800 hover:border-zinc-700 bg-zinc-800/50 hover:bg-zinc-800 rounded-xl text-zinc-300 text-sm font-medium transition-all duration-200 cursor-not-allowed opacity-60">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                    </svg>
                    Apple
                </a>
            </div>

            <!-- Sign up link -->
            <p class="text-center text-sm text-zinc-500 mt-6">
                Don&apos;t have an account?
                <button type="button" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    Sign Up
                </button>
            </p>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            33% { transform: translateY(-15px) rotate(2deg); }
            66% { transform: translateY(-8px) rotate(-1deg); }
        }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
        .animate-slideDown { animation: slideDown 0.3s ease-out; }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('login-form');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const errorAlert = document.getElementById('error-alert');
            const errorMessage = document.getElementById('error-message');
            const togglePassword = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            const remember = document.getElementById('remember');

            // Toggle password visibility
            togglePassword.addEventListener('click', function () {
                const isPassword = password.type === 'password';
                password.type = isPassword ? 'text' : 'password';
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            });

            // Auto-focus email
            email.focus();

            // Show error
            function showError(msg) {
                errorMessage.textContent = msg;
                errorAlert.classList.remove('hidden');
                errorAlert.classList.remove('animate-slideDown');
                void errorAlert.offsetWidth;
                errorAlert.classList.add('animate-slideDown');
            }

            function hideError() {
                errorAlert.classList.add('hidden');
            }

            // Set loading state
            function setLoading(loading) {
                if (loading) {
                    submitBtn.disabled = true;
                    btnText.textContent = 'Signing in...';
                    btnSpinner.classList.remove('hidden');
                } else {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Sign In';
                    btnSpinner.classList.add('hidden');
                }
            }

            // Handle form submit
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                hideError();

                const emailVal = email.value.trim();
                const passVal = password.value;

                if (!emailVal || !passVal) {
                    showError('Please fill in all fields');
                    return;
                }

                setLoading(true);

                try {
                    const res = await fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ email: emailVal, password: passVal }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.message || 'Invalid credentials');
                    }

                    const { token, user } = data;

                    // Store token
                    if (remember.checked) {
                        localStorage.setItem('auth_token', token);
                    } else {
                        sessionStorage.setItem('auth_token', token);
                    }

                    // Redirect based on role
                    const roles = user.roles || [];
                    if (roles.includes('super_admin')) {
                        window.location.href = '/admin';
                    } else {
                        window.location.href = '/';
                    }
                } catch (err) {
                    showError(err.message);
                    setLoading(false);

                    // Shake animation on error
                    document.getElementById('login-card').animate([
                        { transform: 'translateX(0)' },
                        { transform: 'translateX(-8px)' },
                        { transform: 'translateX(8px)' },
                        { transform: 'translateX(-4px)' },
                        { transform: 'translateX(4px)' },
                        { transform: 'translateX(0)' },
                    ], { duration: 400, easing: 'ease-in-out' });
                }
            });

            // Clear error on input
            email.addEventListener('input', hideError);
            password.addEventListener('input', hideError);
        });
    </script>
</body>
</html>
