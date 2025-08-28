@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => 'up'
])

@php
$colors = [
    'blue' => 'from-blue-500 to-blue-600',
    'green' => 'from-emerald-500 to-green-600',
    'yellow' => 'from-amber-500 to-orange-600',
    'red' => 'from-red-500 to-pink-600',
    'purple' => 'from-purple-500 to-indigo-600',
    'indigo' => 'from-indigo-500 to-blue-600',
];

$bgColors = [
    'blue' => 'from-blue-50 to-blue-100',
    'green' => 'from-emerald-50 to-green-100',
    'yellow' => 'from-amber-50 to-orange-100',
    'red' => 'from-red-50 to-pink-100',
    'purple' => 'from-purple-50 to-indigo-100',
    'indigo' => 'from-indigo-50 to-blue-100',
];

$iconBg = $colors[$color] ?? $colors['blue'];
$cardBg = $bgColors[$color] ?? $bgColors['blue'];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-gradient-to-br {{ $iconBg }} rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        </div>
        <div class="ml-5 w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-slate-500 truncate">{{ $title }}</dt>
                <dd class="flex items-baseline">
                    <div class="text-2xl font-bold text-slate-900">{{ $value }}</div>
                    @if($trend)
                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trendDirection === 'up' ? 'text-green-600' : 'text-red-600' }}">
                            @if($trendDirection === 'up')
                                <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="self-center flex-shrink-0 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="sr-only">{{ $trendDirection === 'up' ? 'Increased' : 'Decreased' }} by</span>
                            {{ $trend }}
                        </div>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
