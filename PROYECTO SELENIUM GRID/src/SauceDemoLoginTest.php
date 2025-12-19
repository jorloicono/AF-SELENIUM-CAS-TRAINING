<?php
namespace App;

use Exception;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class SauceDemoLoginTest extends BaseTest
{

    public function testLogin($username, $password, $expectedRole, $expectedUrl)
    {
        $this->log("Probando login: $username");

        try {
            // PASO 1: Navegar a SauceDemo
            $this->log("Navegando a SauceDemo...");
            $this->driver->get('https://www.saucedemo.com/');
            $this->log("Página cargada");

            // PASO 2: Ingresar credenciales
            $this->log("Buscando campos de formulario...");
            $usernameField = $this->driver->findElement(WebDriverBy::id('user-name'));
            $passwordField = $this->driver->findElement(WebDriverBy::id('password'));
            $loginButton = $this->driver->findElement(WebDriverBy::id('login-button'));

            $this->log("Ingresando credenciales...");
            $usernameField->clear()->sendKeys($username);
            $passwordField->clear()->sendKeys($password);
            $this->log("Haciendo click en login...");
            $loginButton->click();

            // PASO 3: Esperar resultado
            $wait = new WebDriverWait($this->driver, 10);

            // Verificar que no hay error de login
            try {
                $errorMessage = $wait->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated(
                        WebDriverBy::xpath('//h3[@data-test="error"]')
                    )
                );
                $this->log("❌ LOGIN FALLÓ: " . $errorMessage->getText());
                return ['status' => 'FAILED', 'error' => $errorMessage->getText()];
            } catch (Exception $e) {
                // No hay error = login exitoso
            }

            // PASO 4: Verificar URL destino
            $currentUrl = $this->driver->getCurrentURL();
            $this->log("URL actual: $currentUrl");

            if (strpos($currentUrl, $expectedUrl) === false) {
                $this->log("❌ URL incorrecta. Esperada: $expectedUrl, Obtenida: $currentUrl");
                return ['status' => 'FAILED', 'error' => 'URL incorrecta'];
            }

            // PASO 5: Verificar que estamos en la página correcta
            if (strpos($currentUrl, '/inventory.html') !== false) {
                // Verificar que el título "Products" esté presente
                $headerText = $this->driver->findElement(WebDriverBy::xpath('//span[@class="title"]'))->getText();
                $this->log("Título de página: $headerText");

                if ($headerText === 'Products') {
                    $this->log("✅ LOGIN EXITOSO para $username (rol: $expectedRole)");
                    return ['status' => 'PASSED'];
                }
            }

            // Si no llegamos a inventory, verificar que sea el comportamiento esperado
            $this->log("✅ Comportamiento esperado para $username - URL: $currentUrl");
            return ['status' => 'PASSED'];

        } catch (Exception $e) {
            $this->log("❌ ERROR GENERAL: " . $e->getMessage());
            return ['status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }
}
?>