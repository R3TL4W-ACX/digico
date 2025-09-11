<?php

/**
 * Comprehensive Drupal Site Assessment
 * Analyzes current state and upgrade possibilities
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "=== COMPREHENSIVE DRUPAL SITE ASSESSMENT ===\n\n";

// 1. CURRENT SYSTEM STATE
echo "1. CURRENT SYSTEM STATE\n";
echo "========================\n";
echo "Drupal Version: " . \Drupal::VERSION . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Database Type: " . \Drupal::database()->databaseType() . "\n";
echo "Site URL: " . \Drupal::request()->getSchemeAndHttpHost() . "\n";
echo "Installation Profile: " . \Drupal::installProfile() . "\n";
echo "\n";

// 2. ENABLED MODULES ANALYSIS
echo "2. ENABLED MODULES ANALYSIS\n";
echo "============================\n";
$moduleHandler = \Drupal::moduleHandler();
$moduleList = $moduleHandler->getModuleList();

$coreModules = [];
$contribModules = [];
$customModules = [];

foreach ($moduleList as $name => $module) {
    $info = \Drupal::service('info_parser')->parse($module->getPathname());
    $packageInfo = isset($info['package']) ? $info['package'] : 'Other';
    
    if (strpos($module->getPath(), 'core/modules') !== false) {
        $coreModules[$name] = [
            'version' => isset($info['version']) ? $info['version'] : 'Unknown',
            'package' => $packageInfo
        ];
    } elseif (strpos($module->getPath(), 'modules/contrib') !== false) {
        $contribModules[$name] = [
            'version' => isset($info['version']) ? $info['version'] : 'Unknown',
            'package' => $packageInfo,
            'path' => $module->getPath()
        ];
    } else {
        $customModules[$name] = [
            'version' => isset($info['version']) ? $info['version'] : 'Unknown',
            'package' => $packageInfo,
            'path' => $module->getPath()
        ];
    }
}

echo "Core Modules Enabled: " . count($coreModules) . "\n";
echo "Contrib Modules Enabled: " . count($contribModules) . "\n";
echo "Custom Modules Enabled: " . count($customModules) . "\n\n";

echo "CONTRIB MODULES DETAILS:\n";
foreach ($contribModules as $name => $info) {
    echo "  - $name: {$info['version']} ({$info['package']})\n";
}
echo "\n";

if (!empty($customModules)) {
    echo "CUSTOM MODULES:\n";
    foreach ($customModules as $name => $info) {
        echo "  - $name: {$info['version']} (Path: {$info['path']})\n";
    }
    echo "\n";
}

// 3. THEME ANALYSIS
echo "3. THEME ANALYSIS\n";
echo "==================\n";
$themeHandler = \Drupal::service('theme_handler');
$themes = $themeHandler->listInfo();

echo "Active Theme: " . \Drupal::config('system.theme')->get('default') . "\n";
echo "Admin Theme: " . \Drupal::config('system.theme')->get('admin') . "\n\n";

foreach ($themes as $name => $theme) {
    if ($theme->status) {
        $info = \Drupal::service('info_parser')->parse($theme->getPathname());
        echo "Enabled Theme: $name\n";
        echo "  Version: " . (isset($info['version']) ? $info['version'] : 'Unknown') . "\n";
        echo "  Path: " . $theme->getPath() . "\n";
        echo "  Base Theme: " . (isset($info['base theme']) ? $info['base theme'] : 'None') . "\n\n";
    }
}

// 4. CONTENT ANALYSIS
echo "4. CONTENT ANALYSIS\n";
echo "===================\n";

// Node types
$nodeTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
echo "Content Types (" . count($nodeTypes) . "):\n";
foreach ($nodeTypes as $type) {
    $nodeCount = \Drupal::entityQuery('node')
        ->condition('type', $type->id())
        ->accessCheck(FALSE)
        ->count()
        ->execute();
    echo "  - {$type->label()} ({$type->id()}): $nodeCount nodes\n";
}
echo "\n";

// Paragraph types (if paragraphs module is enabled)
if ($moduleHandler->moduleExists('paragraphs')) {
    $paragraphTypes = \Drupal::entityTypeManager()->getStorage('paragraphs_type')->loadMultiple();
    echo "Paragraph Types (" . count($paragraphTypes) . "):\n";
    foreach ($paragraphTypes as $type) {
        echo "  - {$type->label()} ({$type->id()})\n";
    }
    echo "\n";
}

// Taxonomy vocabularies
$vocabularies = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->loadMultiple();
if (!empty($vocabularies)) {
    echo "Taxonomy Vocabularies (" . count($vocabularies) . "):\n";
    foreach ($vocabularies as $vocab) {
        $termCount = \Drupal::entityQuery('taxonomy_term')
            ->condition('vid', $vocab->id())
            ->accessCheck(FALSE)
            ->count()
            ->execute();
        echo "  - {$vocab->label()} ({$vocab->id()}): $termCount terms\n";
    }
    echo "\n";
}

// 5. DATABASE ANALYSIS
echo "5. DATABASE ANALYSIS\n";
echo "====================\n";
$database = \Drupal::database();
$tables = $database->schema()->findTables('%');
echo "Database Tables: " . count($tables) . "\n";

// Get some key table sizes
$keyTables = ['node', 'node__field_paragraphs', 'paragraph', 'users', 'cache_default'];
foreach ($keyTables as $table) {
    if (in_array($table, $tables)) {
        $count = $database->select($table, 't')->countQuery()->execute()->fetchField();
        echo "  - $table: $count rows\n";
    }
}
echo "\n";

// 6. SECURITY ANALYSIS
echo "6. SECURITY ANALYSIS\n";
echo "=====================\n";

// Check for security updates
$updateManager = \Drupal::service('update.manager');
if ($updateManager) {
    echo "Security update check available: Yes\n";
} else {
    echo "Security update check available: No\n";
}

// Check key security settings
$settings = \Drupal::config('system.site');
echo "Maintenance mode: " . ($settings->get('maintenance_mode') ? 'ON' : 'OFF') . "\n";

$userSettings = \Drupal::config('user.settings');
echo "User registration: " . $userSettings->get('register') . "\n";
echo "\n";

// 7. PERFORMANCE ANALYSIS
echo "7. PERFORMANCE ANALYSIS\n";
echo "========================\n";
$performanceConfig = \Drupal::config('system.performance');
echo "CSS aggregation: " . ($performanceConfig->get('css.preprocess') ? 'ON' : 'OFF') . "\n";
echo "JS aggregation: " . ($performanceConfig->get('js.preprocess') ? 'ON' : 'OFF') . "\n";
echo "Cache max age: " . $performanceConfig->get('cache.page.max_age') . " seconds\n";
echo "\n";

// 8. UPGRADE COMPATIBILITY ANALYSIS
echo "8. UPGRADE COMPATIBILITY ANALYSIS\n";
echo "==================================\n";

// Check PHP compatibility for Drupal 9
echo "Current PHP version: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✓ PHP version compatible with Drupal 9\n";
} else {
    echo "✗ PHP version needs upgrade for Drupal 9 (minimum 7.4)\n";
}

if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    echo "✓ PHP version compatible with Drupal 10\n";
} else {
    echo "✗ PHP version needs upgrade for Drupal 10 (minimum 8.0)\n";
}

// Check contrib module D9 compatibility
echo "\nCONTRIB MODULE D9 COMPATIBILITY:\n";
$d9CompatibleModules = [
    'pathauto' => '1.8+',
    'paragraphs' => '1.12+',
    'token' => '1.9+',
    'ctools' => '3.4+',
    'field_group' => '3.1+',
    'google_analytics' => '3.1+',
    'imce' => '2.4+',
    'honeypot' => '2.0+',
    'captcha' => '1.2+'
];

foreach ($contribModules as $moduleName => $moduleInfo) {
    if (isset($d9CompatibleModules[$moduleName])) {
        echo "  ✓ $moduleName: Current version {$moduleInfo['version']}, D9 compatible from {$d9CompatibleModules[$moduleName]}\n";
    } else {
        echo "  ? $moduleName: Compatibility unknown, needs research\n";
    }
}

echo "\n9. RECOMMENDATIONS\n";
echo "==================\n";

$recommendations = [];

// Drupal version recommendation
if (version_compare(\Drupal::VERSION, '8.9.0', '<')) {
    $recommendations[] = "CRITICAL: Upgrade to Drupal 8.9.x first (current LTS) before attempting D9 upgrade";
} elseif (version_compare(\Drupal::VERSION, '9.0.0', '<')) {
    $recommendations[] = "HIGH: Consider upgrading to Drupal 9 for continued security support";
}

// Module recommendations
if (count($contribModules) > 20) {
    $recommendations[] = "MEDIUM: Consider auditing contrib modules - you have " . count($contribModules) . " enabled";
}

// Performance recommendations
if (!$performanceConfig->get('css.preprocess')) {
    $recommendations[] = "LOW: Enable CSS aggregation for better performance";
}
if (!$performanceConfig->get('js.preprocess')) {
    $recommendations[] = "LOW: Enable JS aggregation for better performance";
}

// Security recommendations
if ($userSettings->get('register') !== 'admin_only') {
    $recommendations[] = "MEDIUM: Consider restricting user registration to admins only";
}

foreach ($recommendations as $i => $rec) {
    echo ($i + 1) . ". $rec\n";
}

echo "\n10. NEXT STEPS ANALYSIS\n";
echo "=======================\n";
echo "Based on this assessment, here are the recommended next steps:\n\n";

echo "IMMEDIATE ACTIONS:\n";
echo "- Test all current functionality thoroughly\n";
echo "- Create database backup before any changes\n";
echo "- Update contrib modules to latest D8 compatible versions\n\n";

echo "SHORT TERM (1-2 weeks):\n";
echo "- Upgrade to Drupal 8.9.x (latest LTS)\n";
echo "- Update all contrib modules to D9-compatible versions\n";
echo "- Test site functionality after each update\n\n";

echo "MEDIUM TERM (1-2 months):\n";
echo "- Plan Drupal 9 upgrade\n";
echo "- Test custom theme compatibility with D9\n";
echo "- Upgrade to Drupal 9.x\n\n";

echo "LONG TERM (3-6 months):\n";
echo "- Consider Drupal 10 upgrade path\n";
echo "- Evaluate need for custom module development\n";
echo "- Performance optimization\n\n";

echo "=== ASSESSMENT COMPLETE ===\n";
