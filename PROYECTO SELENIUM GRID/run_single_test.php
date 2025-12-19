<?php
// Script ejecutado por cada proceso paralelo
if ($argc !== 7) {
    die("Uso: php run_single_test.php <username> <password> <role> <url> <browser> <testId>\n");
}

[$username, $password, $expectedRole, $expectedUrl, $browser, $testId] =
    [$argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6]];

require_once 'vendor/autoload.php';
require_once 'src/SauceDemoLoginTest.php';

use App\SauceDemoLoginTest;

$test = new SauceDemoLoginTest($testId, $browser);
$test->setup();

$result = $test->testLogin($username, $password, $expectedRole, $expectedUrl);
$test->teardown();

echo json_encode($result);
?>