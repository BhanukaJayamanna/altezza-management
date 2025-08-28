@props(['size' => 'md'])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8',
        'md' => 'w-12 h-12', 
        'lg' => 'w-16 h-16',
        'xl' => 'w-20 h-20'
    ];
    
    $textSizes = [
        'sm' => 'text-lg',
        'md' => 'text-2xl',
        'lg' => 'text-3xl', 
        'xl' => 'text-4xl'
    ];
    
    $subtextSizes = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center space-x-3']) }}>
    <!-- Building Icon/Logo -->
    <div class="relative">
        <svg class="{{ $sizeClasses[$size] }} text-blue-600" viewBox="0 0 100 100" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <!-- Main building structure -->
            <rect x="20" y="25" width="60" height="70" fill="currentColor" rx="2"/>
            <!-- Building levels -->
            <rect x="22" y="27" width="56" height="2" fill="white" opacity="0.3"/>
            <rect x="22" y="35" width="56" height="2" fill="white" opacity="0.3"/>
            <rect x="22" y="43" width="56" height="2" fill="white" opacity="0.3"/>
            <rect x="22" y="51" width="56" height="2" fill="white" opacity="0.3"/>
            <rect x="22" y="59" width="56" height="2" fill="white" opacity="0.3"/>
            <rect x="22" y="67" width="56" height="2" fill="white" opacity="0.3"/>
            
            <!-- Windows -->
            <rect x="25" y="30" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="35" y="30" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="45" y="30" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="55" y="30" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="65" y="30" width="6" height="4" fill="white" opacity="0.8"/>
            
            <rect x="25" y="38" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="35" y="38" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="45" y="38" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="55" y="38" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="65" y="38" width="6" height="4" fill="white" opacity="0.8"/>
            
            <rect x="25" y="46" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="35" y="46" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="45" y="46" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="55" y="46" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="65" y="46" width="6" height="4" fill="white" opacity="0.8"/>
            
            <rect x="25" y="54" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="35" y="54" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="45" y="54" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="55" y="54" width="6" height="4" fill="white" opacity="0.8"/>
            <rect x="65" y="54" width="6" height="4" fill="white" opacity="0.8"/>
            
            <!-- Entrance -->
            <rect x="40" y="75" width="20" height="20" fill="white" opacity="0.9"/>
            <rect x="42" y="77" width="16" height="16" fill="currentColor"/>
            
            <!-- Rooftop -->
            <rect x="15" y="20" width="70" height="8" fill="#10B981" rx="1"/>
            <rect x="17" y="22" width="66" height="2" fill="white" opacity="0.3"/>
            
            <!-- Side buildings for depth -->
            <rect x="10" y="35" width="15" height="60" fill="currentColor" opacity="0.7" rx="1"/>
            <rect x="75" y="40" width="15" height="55" fill="currentColor" opacity="0.5" rx="1"/>
        </svg>
        
        <!-- Subtle shadow/glow effect -->
        <div class="absolute inset-0 bg-blue-600 opacity-20 blur-lg transform scale-110 -z-10"></div>
    </div>
    
    <!-- System Name -->
    <div class="flex flex-col">
        <span class="{{ $textSizes[$size] }} font-bold text-white tracking-tight">ALTEZZA</span>
        <span class="{{ $subtextSizes[$size] }} font-medium text-white/80 -mt-1">Property Management</span>
    </div>
</div>
