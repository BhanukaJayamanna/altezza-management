<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- User and Pusher meta for real-time notifications -->
        @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
        @endauth

        <title>{{ config('app.name', 'Altezza Property Management') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Pusher.js for real-time functionality -->
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        
        <!-- Toast Notification System -->
        <script src="{{ asset('js/altezza-toast.js') }}"></script>
        
        <!-- Original Notification System -->
        <script src="{{ asset('js/altezza-notifications.js') }}"></script>
        
        <!-- Real-time Notification System -->
        <script src="{{ asset('js/altezza-realtime-notifications.js') }}"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.sidebar')
        </div>
        
        <!-- Toast Notifications -->
        @include('components.toast-simple')
    </body>
</html>
