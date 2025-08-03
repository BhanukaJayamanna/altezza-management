<!-- Toast Notification Container (managed by altezza-toast.js) -->
<div id="altezza-toast-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full">
    <!-- Toasts will be dynamically inserted here -->
</div>

<script>
// Process server-side Laravel flash messages
document.addEventListener('DOMContentLoaded', function() {
    // Set server messages for processing
    window.altezzaServerMessages = {
        <?php if(session('success')): ?>
            success: <?php echo json_encode(session('success')); ?>,
        <?php endif; ?>
        <?php if(session('error')): ?>
            error: <?php echo json_encode(session('error')); ?>,
        <?php endif; ?>
        <?php if(session('warning')): ?>
            warning: <?php echo json_encode(session('warning')); ?>,
        <?php endif; ?>
        <?php if(session('info')): ?>
            info: <?php echo json_encode(session('info')); ?>,
        <?php endif; ?>
    };
    
    // Process action toasts
    <?php if(session('toast_with_action')): ?>
        <?php $actionToast = session('toast_with_action'); ?>
        setTimeout(() => {
            altezzaToast.show({
                type: <?php echo json_encode($actionToast['type']); ?>,
                title: <?php echo json_encode($actionToast['title']); ?>,
                message: <?php echo json_encode($actionToast['message']); ?>,
                timeout: 0,
                action: {
                    label: <?php echo json_encode($actionToast['action']['label']); ?>,
                    url: <?php echo json_encode($actionToast['action']['url']); ?>

                }
            });
        }, 200);
    <?php endif; ?>
    
    // Initialize toast system
    if (window.altezzaToast) {
        window.altezzaToast.processServerMessages();
    }
});
</script>
<?php /**PATH F:\APPS\altezza\altezza\altezza\resources\views/components/toast-simple.blade.php ENDPATH**/ ?>