<?php

namespace App\Pages;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Guru99Login
{
    /**
     * Instancia del WebDriver para interactuar con el navegador
     * 
     * @var RemoteWebDriver
     */
    private $driver;

    /**
     * Localizador del campo de usuario (uid)
     * Estrategia: buscar por atributo "name" con valor "uid"
     * 
     * @var WebDriverBy
     */
    private $user99GuruName;

    /**
     * Localizador del campo de contraseña
     * Estrategia: buscar por atributo "name" con valor "password"
     * 
     * @var WebDriverBy
     */
    private $password99Guru;

    /**
     * Localizador del título de la página de login
     * Estrategia: buscar por atributo "class" con valor "barone"
     * 
     * @var WebDriverBy
     */
    private $titleText;

    /**
     * Localizador del botón de login
     * Estrategia: buscar por atributo "name" con valor "btnLogin"
     * 
     * @var WebDriverBy
     */
    private $login;

    /**
     * Constructor de la página de login
     * Recibe el driver y define todos los localizadores de elementos
     * 
     * @param RemoteWebDriver $driver Instancia del WebDriver
     */
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;

        // Definir localizadores para cada elemento
        $this->user99GuruName = WebDriverBy::name('uid');
        $this->password99Guru = WebDriverBy::name('password');
        $this->titleText = WebDriverBy::className('barone');
        $this->login = WebDriverBy::name('btnLogin');
    }

    /**
     * Escribe el nombre de usuario en el campo correspondiente
     * 
     * @param string $strUserName Nombre de usuario a ingresar
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function setUserName($strUserName)
    {
        try {
            $element = $this->driver->findElement($this->user99GuruName);
            $element->clear(); // Limpiar el campo primero
            $element->sendKeys($strUserName);
        } catch (\Exception $e) {
            throw new \Exception("Error al establecer el nombre de usuario: " . $e->getMessage());
        }
    }

    /**
     * Escribe la contraseña en el campo correspondiente
     * 
     * @param string $strPassword Contraseña a ingresar
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function setPassword($strPassword)
    {
        try {
            $element = $this->driver->findElement($this->password99Guru);
            $element->clear(); // Limpiar el campo primero
            $element->sendKeys($strPassword);
        } catch (\Exception $e) {
            throw new \Exception("Error al establecer la contraseña: " . $e->getMessage());
        }
    }

    /**
     * Hace clic en el botón de login
     * 
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function clickLogin()
    {
        try {
            $this->driver->findElement($this->login)->click();
            // Esperar a que la página se cargue (implícitamente)
            sleep(2);
        } catch (\Exception $e) {
            throw new \Exception("Error al hacer clic en el botón de login: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el título de la página de login
     * Este título generalmente contiene "Guru99 Bank"
     * 
     * @return string Texto del título
     * @throws Exception Si el elemento no se encuentra
     */
    public function getLoginTitle()
    {
        try {
            $titleElement = $this->driver->findElement($this->titleText);
            return $titleElement->getText();
        } catch (\Exception $e) {
            throw new \Exception("Error al obtener el título: " . $e->getMessage());
        }
    }

    /**
     * Método de alto nivel que encapsula todo el flujo de login
     * Esto es la esencia del POM: un método que representa una acción de negocio
     * 
     * @param string $strUserName Nombre de usuario
     * @param string $strPassword Contraseña
     * @return void
     */
    public function loginToGuru99($strUserName, $strPassword)
    {
        echo "[INFO] Iniciando sesión con usuario: $strUserName\n";

        // Llenar el campo de usuario
        $this->setUserName($strUserName);

        // Llenar el campo de contraseña
        $this->setPassword($strPassword);

        // Hacer clic en el botón de login
        $this->clickLogin();

        echo "[INFO] Sesión iniciada exitosamente\n";
    }
}