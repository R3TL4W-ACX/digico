<?php

/**
 * Comprehensive site functionality test after module removal
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "=== COMPREHENSIVE SITE TEST ===\n\n";

// Test 1: Basic site functionality
echo "1. BASIC SITE FUNCTIONALITY\n";
echo "============================\n";
echo "Drupal Version: " . \Drupal::VERSION . "\n";
echo "Site Status: " . (\Drupal::hasContainer() ? 'BOOTSTRAPPED' : 'FAILED') . "\n";

// Test 2: Database connectivity
echo "Database Connection: ";
try {
    $connection = \Drupal::database();
    $result = $connection->query("SELECT 1")->fetchField();
    echo ($result == 1 ? "✅ WORKING" : "❌ FAILED") . "\n";
} catch (Exception $e) {
    echo "❌ FAILED: " . $e->getMessage() . "\n";
}

// Test 3: Check if problematic modules are really disabled
echo "\n2. MODULE STATUS CHECK\n";
echo "======================\n";
$moduleHandler = \Drupal::moduleHandler();
$problematicModules = ['colorbox_load', 'ng_lightbox'];

foreach ($problematicModules as $module) {
    $status = $moduleHandler->moduleExists($module) ? "❌ STILL ENABLED" : "✅ DISABLED";
    echo "$module: $status\n";
}

// Test 4: Check that colorbox (the main module) is still working
echo "\nColorbox module: " . ($moduleHandler->moduleExists('colorbox') ? "✅ ENABLED" : "❌ DISABLED") . "\n";

// Test 5: Content accessibility
echo "\n3. CONTENT ACCESSIBILITY\n";
echo "========================\n";

// Get all published nodes
$nodeStorage = \Drupal::entityTypeManager()->getStorage('node');
$nids = \Drupal::entityQuery('node')
    ->condition('status', 1)
    ->accessCheck(FALSE)
    ->execute();

echo "Published nodes: " . count($nids) . "\n";

if (!empty($nids)) {
    echo "Testing first few nodes:\n";
    $testNodes = array_slice($nids, 0, 3); // Test first 3 nodes
    
    foreach ($testNodes as $nid) {
        $node = $nodeStorage->load($nid);
        if ($node) {
            echo "  - Node $nid ({$node->getTitle()}): ✅ LOADABLE\n";
        } else {
            echo "  - Node $nid: ❌ FAILED TO LOAD\n";
        }
    }
}

// Test 6: Check paragraph functionality (since you use paragraphs extensively)
echo "\n4. PARAGRAPHS FUNCTIONALITY\n";
echo "============================\n";
if ($moduleHandler->moduleExists('paragraphs')) {
    echo "Paragraphs module: ✅ ENABLED\n";
    
    // Get paragraph types
    $paragraphTypes = \Drupal::entityTypeManager()->getStorage('paragraphs_type')->loadMultiple();
    echo "Paragraph types: " . count($paragraphTypes) . "\n";
    
    foreach ($paragraphTypes as $type) {
        echo "  - {$type->label()} ({$type->id()})\n";
    }
} else {
    echo "Paragraphs module: ❌ DISABLED\n";
}

// Test 7: Theme functionality
echo "\n5. THEME FUNCTIONALITY\n";
echo "======================\n";
$activeTheme = \Drupal::theme()->getActiveTheme()->getName();
echo "Active theme: $activeTheme\n";

// Test if admin interface is accessible
echo "\n6. ADMIN INTERFACE\n";
echo "==================\n";
echo "Admin menu should be accessible at: http://digico.ddev.site/admin\n";
echo "Modules page should be accessible at: http://digico.ddev.site/admin/modules\n";

echo "\n=== TESTING COMPLETE ===\n";
echo "\nNEXT STEPS:\n";
echo "1. ✅ Manually visit http://digico.ddev.site to check homepage\n";
echo "2. ✅ Check admin interface at http://digico.ddev.site/admin\n";
echo "3. ✅ Verify content pages are displaying correctly\n";
echo "4. ✅ Test any lightbox functionality (should use colorbox now)\n";
echo "5. ✅ If all looks good, proceed with composer update\n";
?>
