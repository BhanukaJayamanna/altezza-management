<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Fixing Owner Issue ===\n\n";

$apartment = \App\Models\Apartment::find(10);
if ($apartment) {
    echo "Setting apartment {$apartment->number} owner_id to null (was {$apartment->owner_id})\n";
    $apartment->owner_id = null;
    $apartment->save();
    echo "✓ Fixed!\n";
} else {
    echo "Apartment not found\n";
}

echo "\n=== Fix Complete ===\n";
