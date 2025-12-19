<?php
namespace App;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;

class BaseTest
{
    protected $driver;
    protected $testId;
    protected $browser;

    public function __construct($testId, $browser = 'chrome')
    {
        $this->testId = $testId;
        $this->browser = $browser;
    }

    public function setup()
    {
        echo "[TEST {$this->testId}] [" . strtoupper($this->browser) . "] Iniciando...\n";

        // Definir capacidades según el navegador
        switch (strtolower($this->browser)) {
            case 'firefox':
                $capabilities = DesiredCapabilities::firefox();
                break;
            case 'chrome':
            default:
                $capabilities = DesiredCapabilities::chrome();
                break;
        }

        // Conectar a Selenium Grid Hub
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444/',  // URL del Hub de Selenium Grid
            $capabilities,
            30000,  // Timeout de conexión en milisegundos
            30000   // Timeout de request
        );

        // Configurar timeouts
        $this->driver->manage()->timeouts()->implicitlyWait(10);
        $this->driver->manage()->timeouts()->pageLoadTimeout(30);

        // Maximizar ventana (opcional: usar tamaño fijo si es headless)
        try {
            $this->driver->manage()->window()->maximize();
        } catch (\Exception $e) {
            // En algunos entornos headless, maximize puede fallar
            $this->driver->manage()->window()->setSize(new WebDriverDimension(1920, 1080));
        }

        echo "[TEST {$this->testId}] [" . strtoupper($this->browser) . "] Driver listo\n";
    }

    public function teardown()
    {
        if ($this->driver) {
            $this->driver->quit();
            echo "[TEST {$this->testId}] [" . strtoupper($this->browser) . "] Finalizado\n\n";
        }
    }

    public function log($message)
    {
        echo "[TEST {$this->testId}] [" . strtoupper($this->browser) . "] $message\n";
    }
}
?>