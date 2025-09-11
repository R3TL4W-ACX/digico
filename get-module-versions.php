<?php
/**
 * Get Current Module Versions
 * Script to inventory all enabled modules before manual updates
 */

// Bootstrap Drupal
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

// Get module handler
$moduleHandler = \Drupal::service('module_handler');
$moduleList = $moduleHandler->getModuleList();

// Get module data (D8.5 compatible)
$moduleData = system_rebuild_module_data();

echo "=== CURRENT MODULE INVENTORY ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

echo "ENABLED MODULES:\n";
echo str_repeat("=", 80) . "\n";
printf("%-30s %-15s %-20s %s\n", "MODULE", "VERSION", "TYPE", "STATUS");
echo str_repeat("-", 80) . "\n";

$coreModules = [];
$contribModules = [];
$customModules = [];

foreach ($moduleList as $moduleName => $module) {
    $moduleInfo = $moduleData[$moduleName];
    $version = isset($moduleInfo->info['version']) ? $moduleInfo->info['version'] : 'Unknown';
    $package = isset($moduleInfo->info['package']) ? $moduleInfo->info['package'] : 'Unknown';
    
    // Determine module type
    $path = $module->getPath();
    if (strpos($path, 'core/modules') !== false) {
        $type = 'Core';
        $coreModules[$moduleName] = [
            'version' => $version,
            'package' => $package,
            'path' => $path
        ];
    } elseif (strpos($path, 'modules/contrib') !== false) {
        $type = 'Contrib';
        $contribModules[$moduleName] = [
            'version' => $version,
            'package' => $package,
            'path' => $path
        ];
    } else {
        $type = 'Custom';
        $customModules[$moduleName] = [
            'version' => $version,
            'package' => $package,
            'path' => $path
        ];
    }
    
    printf("%-30s %-15s %-20s %s\n", 
        substr($moduleName, 0, 29), 
        substr($version, 0, 14), 
        substr($package, 0, 19),
        $type
    );
}

echo "\n" . str_repeat("=", 80) . "\n";

// Focus on contrib modules for updates
echo "\nCONTRIB MODULES FOR MANUAL UPDATE:\n";
echo str_repeat("=", 50) . "\n";

if (empty($contribModules)) {
    echo "No contrib modules found.\n";
} else {
    foreach ($contribModules as $name => $info) {
        echo "Module: {$name}\n";
        echo "  Current Version: {$info['version']}\n";
        echo "  Package: {$info['package']}\n";
        echo "  Path: {$info['path']}\n";
        echo "  Update Priority: " . getUpdatePriority($name) . "\n\n";
    }
}

// Check for problematic modules that might still be installed
echo "CHECKING FOR PROBLEMATIC MODULES:\n";
echo str_repeat("=", 40) . "\n";

$problematicModules = ['colorbox_load', 'ng_lightbox'];
foreach ($problematicModules as $module) {
    if (isset($moduleList[$module])) {
        echo "⚠️  {$module} is still enabled!\n";
    } else {
        echo "✅ {$module} successfully removed\n";
    }
}

echo "\nSUMMARY:\n";
echo str_repeat("=", 20) . "\n";
echo "Total Enabled Modules: " . count($moduleList) . "\n";
echo "Core Modules: " . count($coreModules) . "\n";
echo "Contrib Modules: " . count($contribModules) . "\n";
echo "Custom Modules: " . count($customModules) . "\n";

function getUpdatePriority($moduleName) {
    $highPriority = ['pathauto', 'honeypot', 'token', 'paragraphs'];
    $mediumPriority = ['ctools', 'field_group', 'entity_reference_revisions'];
    $lowPriority = ['google_analytics', 'colorbox'];
    
    if (in_array($moduleName, $highPriority)) {
        return "HIGH (Security/Critical)";
    } elseif (in_array($moduleName, $mediumPriority)) {
        return "MEDIUM (Stability)";
    } elseif (in_array($moduleName, $lowPriority)) {
        return "LOW (Enhancement)";
    } else {
        return "ASSESS (Research needed)";
    }
}

echo "\n=== READY FOR MANUAL UPDATES ===\n";
?>
