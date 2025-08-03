<?php

if (!function_exists('toast_success')) {
    /**
     * Add a success toast message to the session.
     */
    function toast_success(string $message, string $title = 'Success!'): void
    {
        session()->flash('success', $message);
    }
}

if (!function_exists('toast_error')) {
    /**
     * Add an error toast message to the session.
     */
    function toast_error(string $message, string $title = 'Error!'): void
    {
        session()->flash('error', $message);
    }
}

if (!function_exists('toast_warning')) {
    /**
     * Add a warning toast message to the session.
     */
    function toast_warning(string $message, string $title = 'Warning!'): void
    {
        session()->flash('warning', $message);
    }
}

if (!function_exists('toast_info')) {
    /**
     * Add an info toast message to the session.
     */
    function toast_info(string $message, string $title = 'Info'): void
    {
        session()->flash('info', $message);
    }
}

if (!function_exists('toast_with_action')) {
    /**
     * Add a toast message with an action button.
     */
    function toast_with_action(string $type, string $message, string $actionLabel, string $actionUrl, string $title = null): void
    {
        $title = $title ?? ucfirst($type) . '!';
        
        session()->flash('toast_with_action', [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action' => [
                'label' => $actionLabel,
                'url' => $actionUrl
            ]
        ]);
    }
}
