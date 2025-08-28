@props(['title' => null, 'subtitle' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Altezza Property Management System - Professional property management solution for apartments, leases, owners, and more.">
        <meta name="keywords" content="property management, apartment management, lease management, owner management, rooftop reservations">
        <meta name="author" content="Altezza Property Management">

        @if($title)
            <title>{{ $title }} - {{ config('app.name') }}</title>
        @else
            <title>{{ config('app.name', 'Altezza Property Management') }}</title>
        @endif

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect x='20' y='25' width='60' height='70' fill='%234F46E5' rx='2'/%3E%3Crect x='15' y='20' width='70' height='8' fill='%2310B981' rx='1'/%3E%3Crect x='25' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='35' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='45' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='55' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3Crect x='65' y='30' width='6' height='4' fill='white' opacity='0.8'/%3E%3C/svg%3E">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Toast Notification System -->
        <script src="{{ asset('js/altezza-toast.js') }}"></script>
        
        <!-- Real-time Notification System -->
        <link rel="stylesheet" href="{{ asset('css/altezza-notifications.css') }}">
        <script src="{{ asset('js/altezza-notifications.js') }}"></script>
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{ $slot }}
        </div>
    </body>
</html>
