<!-- Toast Notification Container (managed by altezza-toast.js) -->
<div id="altezza-toast-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full">
    <!-- Toasts will be dynamically inserted here -->
</div>

<script>
// Process server-side Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    // Set server messages for processing
    window.altezzaServerMessages = {
        @if(session('success'))
            success: {!! json_encode(session('success')) !!},
        @endif
        @if(session('error'))
            error: {!! json_encode(session('error')) !!},
        @endif
        @if(session('warning'))
            warning: {!! json_encode(session('warning')) !!},
        @endif
        @if(session('info'))
            info: {!! json_encode(session('info')) !!},
        @endif
    };
    
    // Process action toasts
    @if(session('toast_with_action'))
        @php $actionToast = session('toast_with_action'); @endphp
        setTimeout(() => {
            altezzaToast.show({
                type: {!! json_encode($actionToast['type']) !!},
                title: {!! json_encode($actionToast['title']) !!},
                message: {!! json_encode($actionToast['message']) !!},
                timeout: 0,
                action: {
                    label: {!! json_encode($actionToast['action']['label']) !!},
                    url: {!! json_encode($actionToast['action']['url']) !!}
                }
            });
        }, 200);
    @endif
    
    // Initialize toast system
    if (window.altezzaToast) {
        window.altezzaToast.processServerMessages();
    }
});
</script>
