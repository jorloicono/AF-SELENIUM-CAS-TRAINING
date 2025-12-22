<?php
require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use App\Tests\ExpectedConditionsTests;

try {
    $tests = new ExpectedConditionsTests();
    $tests->runAllTests();
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    WebDriverHelper::stop();
}
?>