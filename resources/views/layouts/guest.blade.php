<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Altezza Property Management System - Professional property management solution for apartments, leases, owners, and more.">
        <meta name="keywords" content="property management, apartment management, lease management, owner management, rooftop reservations">
        <meta name="author" content="Altezza Property Management">

        <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name', 'Altezza Property Management') . ' - Login' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect x='20' y='25' width='60' height='70' fill='%234F46E5' rx='2'/%3E%3Crect x='15' y='20' width='70' height='8' fill='%2310B981' rx='1'/%3E%3Crect x='25' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='35' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='45' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='55' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='65' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            
            .login-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }
            
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            .login-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .floating-element {
                animation: float 6s ease-in-out infinite;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
        </style>
    </head>
    <body class="antialiased">
        <!-- Background with animated gradient -->
        <div class="min-h-screen login-bg relative overflow-hidden">
            <!-- Floating background elements -->
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full floating-element"></div>
            <div class="absolute top-32 right-20 w-12 h-12 bg-white/5 rounded-full floating-element" style="animation-delay: -2s;"></div>
            <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-white/10 rounded-full floating-element" style="animation-delay: -4s;"></div>
            <div class="absolute bottom-10 right-10 w-8 h-8 bg-white/5 rounded-full floating-element" style="animation-delay: -1s;"></div>
            
            <!-- Main content container -->
            <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
                <div class="max-w-md w-full space-y-8">
                    <!-- Logo and branding -->
                    <div class="text-center">
                        <div class="flex justify-center mb-6">
                            <x-altezza-logo />
                        </div>
                        <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
                        <p class="text-white/80 text-sm">Sign in to your property management dashboard</p>
                    </div>

                    <!-- Login form container -->
                    <div class="login-card rounded-2xl shadow-2xl p-8">
                        {{ $slot }}
                    </div>
                    
                    <!-- Footer -->
                    <div class="text-center text-white/60 text-xs">
                        <p>&copy; {{ date('Y') }} Altezza Property Management System. All rights reserved.</p>
                        <p class="mt-1">Secure • Reliable • Professional</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
