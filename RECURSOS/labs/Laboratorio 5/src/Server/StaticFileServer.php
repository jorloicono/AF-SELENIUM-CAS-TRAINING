<?php
namespace App\Server;

class StaticFileServer
{
    private $port = SERVER_PORT;
    private $pagesPath;

    public function __construct($pagesPath)
    {
        $this->pagesPath = realpath($pagesPath);
        echo "[SERVER] Sirviendo páginas desde: {$this->pagesPath}\n";
    }

    public function isRunning()
    {
        $connection = @fsockopen('127.0.0.1', $this->port, $errno, $errstr, 1);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }

    public function start()
    {
        // Simular servidor (en producción usar ReactPHP o similar)
        echo "[SERVER] Servidor simulado activo: " . BASE_URL . "\n";
    }
}
?>