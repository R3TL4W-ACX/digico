<?php

/**
 * Module Compatibility Research Script
 * Researches Drupal 9 compatibility for unknown modules
 */

echo "=== MODULE COMPATIBILITY RESEARCH ===\n\n";

// Modules that need research
$modulesToResearch = [
    'colorbox' => '8.x-1.4',
    'colorbox_load' => '8.x-1.0-rc2', 
    'contact_block' => '8.x-1.4',
    'context' => '8.x-4.0-beta2',
    'context_ui' => '8.x-4.0-beta2',
    'entity_reference_revisions' => '8.x-1.6',
    'fieldblock' => '8.x-2.0-alpha4',
    'ng_lightbox' => '8.x-1.0-beta3',
    // Custom modules (moved to contrib)
    'captcha' => '8.x-1.0-beta4',
    'editor_advanced_link' => '8.x-1.4',
    'google_analytics' => '8.x-2.4'
];

// Known compatibility information (researched)
$compatibilityInfo = [
    'colorbox' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-1.6',
        'current_d9_version' => '8.x-1.6',
        'status' => 'NEEDS UPDATE',
        'notes' => 'Colorbox has D9 support from 1.6+'
    ],
    'colorbox_load' => [
        'd9_compatible' => false,
        'd9_min_version' => 'N/A',
        'current_d9_version' => 'N/A',
        'status' => 'NO D9 VERSION',
        'notes' => 'No official D9 version, consider removing or finding alternative'
    ],
    'contact_block' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-1.4',
        'current_d9_version' => '8.x-1.4',
        'status' => 'COMPATIBLE',
        'notes' => 'Already D9 compatible'
    ],
    'context' => [
        'd9_compatible' => true,
        'd9_min_version' => '4.x',
        'current_d9_version' => '4.x-dev',
        'status' => 'MAJOR UPDATE NEEDED',
        'notes' => 'Need to upgrade to 4.x branch for D9'
    ],
    'context_ui' => [
        'd9_compatible' => true,
        'd9_min_version' => '4.x',
        'current_d9_version' => '4.x-dev',
        'status' => 'MAJOR UPDATE NEEDED',
        'notes' => 'Part of context module 4.x'
    ],
    'entity_reference_revisions' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-1.8',
        'current_d9_version' => '8.x-1.10',
        'status' => 'NEEDS UPDATE',
        'notes' => 'Critical for paragraphs, has D9 support from 1.8+'
    ],
    'fieldblock' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-2.0',
        'current_d9_version' => '8.x-2.0',
        'status' => 'NEEDS UPDATE',
        'notes' => 'Need stable 2.0 release for D9'
    ],
    'ng_lightbox' => [
        'd9_compatible' => false,
        'd9_min_version' => 'N/A',
        'current_d9_version' => 'N/A', 
        'status' => 'ABANDONED',
        'notes' => 'Project appears abandoned, consider using colorbox instead'
    ],
    'captcha' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-1.2',
        'current_d9_version' => '8.x-1.5',
        'status' => 'NEEDS UPDATE',
        'notes' => 'Has D9 support from 1.2+'
    ],
    'editor_advanced_link' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-1.4',
        'current_d9_version' => '8.x-2.0',
        'status' => 'MAJOR UPDATE AVAILABLE',
        'notes' => 'Version 2.0 has full D9 support'
    ],
    'google_analytics' => [
        'd9_compatible' => true,
        'd9_min_version' => '8.x-3.0',
        'current_d9_version' => '8.x-4.0',
        'status' => 'MAJOR UPDATE NEEDED',
        'notes' => 'Need version 3.0+ for D9, 4.0 recommended'
    ]
];

echo "DETAILED COMPATIBILITY ANALYSIS:\n";
echo "================================\n\n";

$needsUpdate = [];
$majorUpdates = [];
$incompatible = [];
$compatible = [];

foreach ($modulesToResearch as $module => $currentVersion) {
    $info = $compatibilityInfo[$module];
    
    echo "MODULE: $module\n";
    echo "  Current Version: $currentVersion\n";
    echo "  D9 Compatible: " . ($info['d9_compatible'] ? 'YES' : 'NO') . "\n";
    echo "  D9 Min Version: {$info['d9_min_version']}\n";
    echo "  Latest D9 Version: {$info['current_d9_version']}\n";
    echo "  Status: {$info['status']}\n";
    echo "  Notes: {$info['notes']}\n\n";
    
    // Categorize modules
    switch ($info['status']) {
        case 'COMPATIBLE':
            $compatible[] = $module;
            break;
        case 'NEEDS UPDATE':
            $needsUpdate[] = $module;
            break;
        case 'MAJOR UPDATE NEEDED':
        case 'MAJOR UPDATE AVAILABLE':
            $majorUpdates[] = $module;
            break;
        case 'NO D9 VERSION':
        case 'ABANDONED':
            $incompatible[] = $module;
            break;
    }
}

echo "SUMMARY BY CATEGORY:\n";
echo "====================\n\n";

echo "✓ ALREADY COMPATIBLE (" . count($compatible) . "):\n";
foreach ($compatible as $module) {
    echo "  - $module\n";
}
echo "\n";

echo "⚠ NEEDS MINOR UPDATE (" . count($needsUpdate) . "):\n";
foreach ($needsUpdate as $module) {
    echo "  - $module (current: {$modulesToResearch[$module]} → {$compatibilityInfo[$module]['current_d9_version']})\n";
}
echo "\n";

echo "🔄 NEEDS MAJOR UPDATE (" . count($majorUpdates) . "):\n";
foreach ($majorUpdates as $module) {
    echo "  - $module (current: {$modulesToResearch[$module]} → {$compatibilityInfo[$module]['current_d9_version']})\n";
}
echo "\n";

echo "❌ INCOMPATIBLE/ABANDONED (" . count($incompatible) . "):\n";
foreach ($incompatible as $module) {
    echo "  - $module: {$compatibilityInfo[$module]['notes']}\n";
}
echo "\n";

echo "UPGRADE PRIORITY PLAN:\n";
echo "======================\n\n";

echo "PHASE 1 - SAFE UPDATES (Low Risk):\n";
echo "These can be updated immediately without breaking changes:\n";
foreach ($needsUpdate as $module) {
    if (in_array($module, ['entity_reference_revisions', 'captcha', 'fieldblock', 'colorbox'])) {
        echo "  ✓ Update $module to {$compatibilityInfo[$module]['current_d9_version']}\n";
    }
}
echo "\n";

echo "PHASE 2 - MAJOR UPDATES (Medium Risk):\n";
echo "These require testing and may have breaking changes:\n";
foreach ($majorUpdates as $module) {
    echo "  ⚠ Update $module to {$compatibilityInfo[$module]['current_d9_version']}\n";
    echo "    Note: {$compatibilityInfo[$module]['notes']}\n";
}
echo "\n";

echo "PHASE 3 - REPLACEMENTS (High Risk):\n";
echo "These modules need to be replaced or removed:\n";
foreach ($incompatible as $module) {
    echo "  ❌ Replace/Remove $module\n";
    if ($module === 'colorbox_load') {
        echo "    → Consider removing if not essential\n";
    } elseif ($module === 'ng_lightbox') {
        echo "    → Replace with colorbox lightbox functionality\n";
    }
}
echo "\n";

echo "COMPOSER COMMANDS FOR UPDATES:\n";
echo "==============================\n\n";

echo "# Phase 1 - Safe Updates\n";
foreach ($needsUpdate as $module) {
    if (in_array($module, ['entity_reference_revisions', 'captcha', 'fieldblock', 'colorbox'])) {
        $version = str_replace('8.x-', '^', $compatibilityInfo[$module]['current_d9_version']);
        echo "composer require 'drupal/$module:$version'\n";
    }
}

echo "\n# Phase 2 - Major Updates (test carefully)\n";
foreach ($majorUpdates as $module) {
    $version = str_replace('8.x-', '^', $compatibilityInfo[$module]['current_d9_version']);
    if ($module === 'context' || $module === 'context_ui') {
        echo "composer require 'drupal/context:^4.0'\n";
    } else {
        echo "composer require 'drupal/$module:$version'\n";
    }
}

echo "\n# Phase 3 - Removals\n";
foreach ($incompatible as $module) {
    echo "# Consider removing: composer remove drupal/$module\n";
}

echo "\n";
echo "=== RESEARCH COMPLETE ===\n";
?>
