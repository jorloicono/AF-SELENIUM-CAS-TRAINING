<?php
namespace POMExample\Tests\Login;

require_once __DIR__ . '/../ParentTest.php';

use POMExample\Tests\ParentTest;

class LoginWithPOMTest extends ParentTest
{
    private string $username = 'standard_user';
    private string $password = 'secret_sauce';

    public function validLogin(): void
    {
        $this->setUp();
        try {
            $this->loginPage->openLoginPage();
            $this->loginPage->enterUserName($this->username);
            $this->loginPage->enterPassWord($this->password);
            $this->loginPage->clickOnLogin();

            $this->productsPage->checkProductsPageOpened();
            echo "✓ validLogin PASADO\n";
        } catch (\Throwable $e) {
            echo "✗ validLogin FALLÓ: " . $e->getMessage() . "\n";
        } finally {
            $this->tearDown();
        }
    }
}

// Ejecutar “test” a mano:
$test = new LoginWithPOMTest();
$test->validLogin();
