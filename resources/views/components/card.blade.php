@props(['title' => null, 'subtitle' => null, 'padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-200']) }}>
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
            @if($title)
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-slate-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="{{ $padding }}">
            {{ $slot }}
        </div>
    @else
        <div class="{{ $padding }}">
            {{ $slot }}
        </div>
    @endif
</div>
