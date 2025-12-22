<?php

namespace App\Pages;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Guru99HomePage
{
    /**
     * Instancia del WebDriver
     * 
     * @var RemoteWebDriver
     */
    private $driver;

    /**
     * Localizador del elemento que contiene el nombre del usuario logueado
     * Estrategia: buscar mediante XPath una fila de tabla con clase "heading3"
     * Este elemento muestra algo como: "Manger ID : mgr123"
     * 
     * @var WebDriverBy
     */
    private $homePageUserName;

    /**
     * Constructor de la página de Home
     * 
     * @param RemoteWebDriver $driver Instancia del WebDriver
     */
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;

        // Definir el localizador usando XPath
        $this->homePageUserName = WebDriverBy::xpath("//table//tr[@class='heading3']");
    }

    /**
     * Obtiene el texto del dashboard que contiene el ID del administrador
     * Este método es usado por los tests para verificar que el login fue exitoso
     * y que el usuario correcto se encuentra logueado
     * 
     * @return string Texto del elemento que contiene el ID del usuario
     * @throws Exception Si el elemento no se encuentra en la página
     */
    public function getHomePageDashboardUserName()
    {
        try {
            $element = $this->driver->findElement($this->homePageUserName);
            $text = $element->getText();

            echo "[INFO] Texto del dashboard obtenido: $text\n";

            return $text;
        } catch (\Exception $e) {
            throw new \Exception(
                "Error al obtener el nombre de usuario del dashboard: " . $e->getMessage()
            );
        }
    }

    /**
     * Verifica que el usuario logueado es el esperado
     * Método de conveniencia para las pruebas
     * 
     * @param string $expectedUserName El nombre de usuario que se espera
     * @return bool True si el usuario es el esperado
     */
    public function isUserLoggedInCorrectly($expectedUserName)
    {
        $dashboardText = $this->getHomePageDashboardUserName();

        // Buscar el nombre de usuario en el texto del dashboard (case-insensitive)
        if (stripos($dashboardText, $expectedUserName) !== false) {
            echo "[SUCCESS] El usuario '$expectedUserName' está logueado correctamente\n";
            return true;
        } else {
            echo "[ERROR] El usuario esperado '$expectedUserName' no se encontró en: $dashboardText\n";
            return false;
        }
    }
}