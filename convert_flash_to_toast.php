<?php

// Script to convert ->with() flash messages to toast helper functions
// This is a development utility to speed up the conversion process

$patterns = [
    // Success messages
    [
        'search' => '/->with\(\'success\',\s*([^)]+)\)/',
        'replace' => function($matches) {
            $message = trim($matches[1], '\'"');
            return ";\n        toast_success('$message');\n        return";
        }
    ],
    
    // Error messages  
    [
        'search' => '/->with\(\'error\',\s*([^)]+)\)/',
        'replace' => function($matches) {
            $message = trim($matches[1], '\'"');
            return ";\n        toast_error('$message');\n        return";
        }
    ],
    
    // Warning messages
    [
        'search' => '/->with\(\'warning\',\s*([^)]+)\)/',
        'replace' => function($matches) {
            $message = trim($matches[1], '\'"');
            return ";\n        toast_warning('$message');\n        return";
        }
    ],
    
    // Info messages
    [
        'search' => '/->with\(\'info\',\s*([^)]+)\)/',
        'replace' => function($matches) {
            $message = trim($matches[1], '\'"');
            return ";\n        toast_info('$message');\n        return";
        }
    ],
];

function convertFile($filePath) {
    global $patterns;
    
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    foreach ($patterns as $pattern) {
        $content = preg_replace_callback($pattern['search'], $pattern['replace'], $content);
    }
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "Converted: $filePath\n";
        return true;
    }
    
    return false;
}

// List of controller files to convert
$controllers = [
    'app/Http/Controllers/OwnerController.php',
    'app/Http/Controllers/LeaseController.php', 
    'app/Http/Controllers/PaymentController.php',
    'app/Http/Controllers/MaintenanceRequestController.php',
    'app/Http/Controllers/ComplaintController.php',
    'app/Http/Controllers/NoticeController.php',
    'app/Http/Controllers/UtilityMeterController.php',
    'app/Http/Controllers/UtilityReadingController.php',
    'app/Http/Controllers/UtilityBillController.php',
    'app/Http/Controllers/UtilityUnitPriceController.php',
];

echo "Starting flash message to toast conversion...\n\n";

$converted = 0;
foreach ($controllers as $controller) {
    if (convertFile($controller)) {
        $converted++;
    }
}

echo "\nConversion complete! Converted $converted files.\n";
echo "Please review the changes and test the functionality.\n";
