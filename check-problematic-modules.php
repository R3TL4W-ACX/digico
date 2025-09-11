<?php

/**
 * Remove problematic modules script
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "=== REMOVING PROBLEMATIC MODULES ===\n\n";

$moduleHandler = \Drupal::moduleHandler();

// Check which problematic modules are currently enabled
$problematicModules = ['colorbox_load', 'ng_lightbox'];
$enabledProblematic = [];

foreach ($problematicModules as $module) {
    if ($moduleHandler->moduleExists($module)) {
        $enabledProblematic[] = $module;
        echo "✓ Found enabled module: $module\n";
    } else {
        echo "- Module not enabled: $module\n";
    }
}

if (empty($enabledProblematic)) {
    echo "✅ No problematic modules are currently enabled!\n";
    echo "The modules are either already disabled or not installed.\n";
} else {
    echo "\n⚠️  WARNING: Found " . count($enabledProblematic) . " problematic module(s) enabled.\n";
    echo "These need to be disabled before Drupal 9 upgrade:\n";
    foreach ($enabledProblematic as $module) {
        echo "  - $module\n";
    }
    echo "\nTo disable these modules, you would need to:\n";
    echo "1. Use Drush: ddev drush pmu colorbox_load ng_lightbox\n";
    echo "2. Or use the admin interface at /admin/modules\n";
    echo "\nAfter disabling, you can safely remove them from composer.json\n";
}

echo "\n=== MODULE CHECK COMPLETE ===\n";
?>
