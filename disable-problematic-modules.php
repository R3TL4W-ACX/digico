<?php

/**
 * Disable problematic modules safely
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "=== DISABLING PROBLEMATIC MODULES ===\n\n";

// Get the module installer service
$moduleInstaller = \Drupal::service('module_installer');
$moduleHandler = \Drupal::moduleHandler();

$problematicModules = ['colorbox_load', 'ng_lightbox'];
$disabledModules = [];

foreach ($problematicModules as $module) {
    if ($moduleHandler->moduleExists($module)) {
        echo "Disabling module: $module...";
        try {
            $moduleInstaller->uninstall([$module]);
            $disabledModules[] = $module;
            echo " ✅ SUCCESS\n";
        } catch (Exception $e) {
            echo " ❌ FAILED: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Module $module is not enabled.\n";
    }
}

if (!empty($disabledModules)) {
    echo "\n✅ Successfully disabled modules:\n";
    foreach ($disabledModules as $module) {
        echo "  - $module\n";
    }
    
    // Clear caches
    echo "\nClearing caches...\n";
    drupal_flush_all_caches();
    echo "✅ Caches cleared!\n";
} else {
    echo "\n⚠️  No modules were disabled.\n";
}

echo "\n=== MODULE DISABLING COMPLETE ===\n";
echo "You can now test the website to ensure everything still works.\n";
?>
