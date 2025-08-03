@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null
])

@php
$variants = [
    'primary' => 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300',
    'success' => 'bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white shadow-lg hover:shadow-xl',
    'danger' => 'bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white shadow-lg hover:shadow-xl',
    'warning' => 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white shadow-lg hover:shadow-xl',
    'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white bg-transparent',
];

$sizes = [
    'sm' => 'px-3 py-2 text-xs font-medium',
    'md' => 'px-4 py-2.5 text-sm font-semibold',
    'lg' => 'px-6 py-3 text-base font-semibold',
    'xl' => 'px-8 py-4 text-lg font-semibold',
];

$classes = $variants[$variant] . ' ' . $sizes[$size];
$commonClasses = 'inline-flex items-center justify-center rounded-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none';
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes . ' ' . $commonClasses]) }}>
    {{ $slot }}
</a>
@else
<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes . ' ' . $commonClasses]) }}
>
    {{ $slot }}
</button>
@endif
