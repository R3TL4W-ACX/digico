<?php

/**
 * Check available module updates for current Drupal 8.5.3
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "=== MODULE UPDATE ANALYSIS ===\n\n";

echo "Current Drupal Version: " . \Drupal::VERSION . "\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

// Get currently enabled contrib modules
$moduleHandler = \Drupal::moduleHandler();
$moduleList = $moduleHandler->getModuleList();

$contribModules = [];
foreach ($moduleList as $name => $module) {
    if (strpos($module->getPath(), 'modules/contrib') !== false) {
        $info = \Drupal::service('info_parser')->parse($module->getPathname());
        $contribModules[$name] = [
            'current_version' => isset($info['version']) ? $info['version'] : 'Unknown',
            'path' => $module->getPath()
        ];
    }
}

echo "CONTRIB MODULES ANALYSIS:\n";
echo "=========================\n";

// Recommended updates that are safe for D8.5.3
$safeUpdates = [
    'pathauto' => [
        'current' => '^1.2',
        'recommended' => '^1.8',
        'd8_compatible' => '1.8.x',
        'risk' => 'LOW',
        'notes' => 'Security and bug fixes, backward compatible'
    ],
    'honeypot' => [
        'current' => '^1.27',
        'recommended' => '^1.30',
        'd8_compatible' => '1.30.x',
        'risk' => 'LOW',
        'notes' => 'Security updates available'
    ],
    'paragraphs' => [
        'current' => '^1.2',
        'recommended' => '^1.12',
        'd8_compatible' => '1.12.x',
        'risk' => 'MEDIUM',
        'notes' => 'Major version jump, test carefully'
    ],
    'imce' => [
        'current' => '^1.6',
        'recommended' => '^1.7',
        'd8_compatible' => '1.7.x',
        'risk' => 'LOW',
        'notes' => 'Minor version update'
    ],
    'ctools' => [
        'current' => '^3.0',
        'recommended' => '^3.4',
        'd8_compatible' => '3.4.x',
        'risk' => 'LOW',
        'notes' => 'API improvements, backward compatible'
    ],
    'token' => [
        'current' => '^1.1',
        'recommended' => '^1.10',
        'd8_compatible' => '1.10.x',
        'risk' => 'MEDIUM',
        'notes' => 'Significant version jump, but well tested'
    ],
    'field_group' => [
        'current' => '^1.0',
        'recommended' => '^1.3',
        'd8_compatible' => '1.3.x',
        'risk' => 'LOW',
        'notes' => 'Bug fixes and improvements'
    ],
    'colorbox' => [
        'current' => '^1.4',
        'recommended' => '^1.6',
        'd8_compatible' => '1.6.x',
        'risk' => 'LOW',
        'notes' => 'Required for D9 compatibility'
    ],
    'contact_block' => [
        'current' => '^1.4',
        'recommended' => '^1.7',
        'd8_compatible' => '1.7.x',
        'risk' => 'LOW',
        'notes' => 'Minor improvements'
    ]
];

foreach ($contribModules as $moduleName => $moduleInfo) {
    echo "MODULE: $moduleName\n";
    echo "  Current: {$moduleInfo['current_version']}\n";
    
    if (isset($safeUpdates[$moduleName])) {
        $update = $safeUpdates[$moduleName];
        echo "  Recommended: {$update['recommended']}\n";
        echo "  Risk Level: {$update['risk']}\n";
        echo "  Notes: {$update['notes']}\n";
    } else {
        echo "  Status: No update recommended at this stage\n";
    }
    echo "\n";
}

echo "RECOMMENDED UPDATE STRATEGY:\n";
echo "============================\n\n";

echo "PHASE 1 - LOW RISK UPDATES (Do first):\n";
$lowRisk = ['pathauto', 'honeypot', 'imce', 'ctools', 'field_group', 'colorbox', 'contact_block'];
foreach ($lowRisk as $module) {
    if (isset($safeUpdates[$module])) {
        echo "  composer require 'drupal/$module:{$safeUpdates[$module]['recommended']}'\n";
    }
}

echo "\nPHASE 2 - MEDIUM RISK UPDATES (Test carefully):\n";
$mediumRisk = ['paragraphs', 'token'];
foreach ($mediumRisk as $module) {
    if (isset($safeUpdates[$module])) {
        echo "  composer require 'drupal/$module:{$safeUpdates[$module]['recommended']}'\n";
    }
}

echo "\nBENEFITS OF THESE UPDATES:\n";
echo "- Security patches\n";
echo "- Bug fixes\n";
echo "- Better D9 compatibility preparation\n";
echo "- Improved functionality\n";
echo "- Better long-term maintenance\n";

echo "\n=== ANALYSIS COMPLETE ===\n";
?>
