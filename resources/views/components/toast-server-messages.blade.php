@php
    $toastMessages = collect([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('info')
    ])->filter()->map(function($message, $type) {
        return [
            'type' => $type,
            'title' => ucfirst($type) . '!',
            'message' => $message,
            'timeout' => $type === 'error' ? 7000 : 5000
        ];
    });
    
    // Handle action toasts
    $actionToast = session('toast_with_action');
    if ($actionToast) {
        $toastMessages->push(array_merge($actionToast, [
            'timeout' => 0, // Don't auto-dismiss action toasts
            'action' => [
                'label' => $actionToast['action']['label'],
                'callback' => "window.location.href = '{$actionToast['action']['url']}'"
            ]
        ]));
    }
@endphp

@if($toastMessages->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($toastMessages as $toast)
                setTimeout(() => {
                    window.showToast({!! json_encode($toast) !!});
                }, {{ $loop->index * 100 }});
            @endforeach
        });
    </script>
@endif
