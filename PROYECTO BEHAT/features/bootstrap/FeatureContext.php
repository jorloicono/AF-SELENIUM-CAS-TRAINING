<?php
/**
 * FeatureContext.php - INTERPRETADOR DE GHERKIN A SELENIUM
 * 
 * RESPONSABILIDADES:
 * 1. Mapear steps Gherkin → Código Selenium PHP
 * 2. Manejar sesiones de navegador
 * 3. Manejar screenshots automáticos en fallos
 * 4. Logs detallados
 * 
 * HERENCIA: MinkContext (proporciona 100+ steps predefinidos)
 */

use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

// CLASE PRINCIPAL - EXTENDE MinkContext (200+ steps gratis)
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var string[] Sesiones de navegador disponibles
     */
    private $sessionNames = ['default'];

    /**
     * @var array Capturas de pantalla de fallos
     */
    private $screenshots = [];

    /**
     * HOOK: BeforeScenario - Se ejecuta ANTES de cada scenario
     */
    public function beforeScenario(BeforeScenarioScope $event)
    {
        $scenario = $event->getScenario();
        echo "\n🚀 Iniciando: " . $scenario->getTitle() . "\n";

        // Tomar screenshot inicial
        $this->takeScreenshot('before_scenario');
    }

    /**
     * HOOK: AfterScenario - Se ejecuta DESPUÉS de cada scenario
     */
    public function afterScenario(AfterScenarioScope $event)
    {
        $scenario = $event->getScenario();
        $result = $event->getTestResult();

        if ($result->isPassed()) {
            echo "✅ Scenario PASSED: " . $scenario->getTitle() . "\n";
        } else {
            echo "❌ Scenario FAILED: " . $scenario->getTitle() . "\n";
            $this->takeScreenshot('after_failure');
        }
    }

    /**
     * ===== STEPS PERSONALIZADOS =====
     */

    /**
     * @Given /^I am on the (login|inventory|cart) page$/
     * 
     * Step personalizado para navegación rápida
     * Sintaxis en .feature: Given I am on the login page
     */
    public function iAmOnThePage($page)
    {
        $pages = [
            'login' => '/',
            'inventory' => '/inventory.html',
            'cart' => '/cart.html'
        ];

        $url = $this->getMinkParameter('base_url') . ($pages[$page] ?? $page);
        $this->visit($url);
    }

    /**
     * @When /^I login with username "([^"]*)" and password "([^"]*)"$/
     * 
     * Step reutilizable para login
     * Sintaxis: When I login with username "standard_user" and password "secret_sauce"
     */
    public function iLoginWithUsernameAndPassword($username, $password)
    {
        $page = $this->getSession()->getPage();

        // Llenar campos usando data-test attributes (más robustos que IDs)
        $page->fillField('user-name', $username);
        $page->fillField('password', $password);
        $page->pressButton('login-button');
    }

    /**
     * @Then /^the "([^"]*)" field should be visible$/
     */
    public function theFieldShouldBeVisible($field)
    {
        $page = $this->getSession()->getPage();
        $element = $page->findField($field);

        if (!$element) {
            throw new Exception("Campo '$field' no encontrado");
        }

        if (!$element->isVisible()) {
            throw new Exception("Campo '$field' no es visible");
        }
    }

    /**
     * @Then /^the "([^"]*)" button should be enabled$/
     */
    public function theButtonShouldBeEnabled($button)
    {
        $page = $this->getSession()->getPage();
        $element = $page->findButton($button);

        if (!$element->isVisible()) {
            throw new Exception("Botón '$button' no es visible");
        }

        // Verificar que no está disabled
        $attribute = $element->getAttribute('disabled');
        if ($attribute !== null) {
            throw new Exception("Botón '$button' está deshabilitado");
        }
    }

    /**
     * @Then /^the shopping cart badge should show "([^"]*)"$/
     */
    public function theShoppingCartBadgeShouldShow($expectedCount)
    {
        $page = $this->getSession()->getPage();
        $badge = $page->find('css', '.shopping_cart_link > a > span');

        if (!$badge) {
            throw new Exception('Badge del carrito no encontrado');
        }

        $actualCount = $badge->getText();
        if ($actualCount !== $expectedCount) {
            throw new Exception("Badge esperaba '$expectedCount', obtuvo '$actualCount'");
        }
    }

    /**
     * @Then /^the "([^"]*)" button should become "([^"]*)"$/
     */
    public function theButtonShouldBecome($originalButton, $newButtonText)
    {
        $page = $this->getSession()->getPage();
        $button = $page->findButton($originalButton);

        // Esperar a que cambie el texto del botón
        $this->getSession()->wait(5000, 500); // Poll cada 500ms hasta 5s

        $currentText = $button->getText();
        if (strpos($currentText, $newButtonText) === false) {
            throw new Exception("Botón no cambió. Esperado: '$newButtonText', Actual: '$currentText'");
        }
    }

    /**
     * @Given /^I click on "([^"]*)"$/
     */
    public function iClickOn($buttonId)
    {
        $page = $this->getSession()->getPage();
        $button = $page->findButton($buttonId);

        if (!$button) {
            // Intentar por ID
            $button = $page->find('css', '#' . $buttonId);
        }

        if (!$button) {
            throw new Exception("Elemento '$buttonId' no encontrado");
        }

        $button->click();
    }

    /**
     * @Given /^I click on shopping cart icon$/
     */
    public function iClickOnShoppingCartIcon()
    {
        $page = $this->getSession()->getPage();
        $cartIcon = $page->findLink('Shopping Cart');
        $cartIcon->click();
    }

    /**
     * ===== MÉTODOS UTILITARIOS =====
     */

    /**
     * Tomar screenshot automático
     */
    private function takeScreenshot($context = 'general')
    {
        try {
            $screenshot = $this->getSession()->getDriver()->getScreenshot();
            $filename = "screenshots/" . date('Y-m-d_H-i-s') . "_$context.png";
            file_put_contents($filename, $screenshot);
        } catch (Exception $e) {
            // Silenciosamente ignorar errores de screenshot
        }
    }
}
?>