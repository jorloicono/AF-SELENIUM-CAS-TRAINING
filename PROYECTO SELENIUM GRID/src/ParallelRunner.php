<?php
namespace App;

class ParallelRunner
{

    /**
     * Ejecuta tests en paralelo usando procesos PHP
     */
    public static function runParallel($users, $browsers = ['chrome', 'firefox'], $maxConcurrent = 4)
    {
        $processes = [];
        $results = [];
        $totalTests = count($users) * count($browsers);
        $completedTests = 0;

        echo "🚀 EJECUTANDO $totalTests TESTS EN PARALELO (máx $maxConcurrent concurrentes)\n";
        echo "Usuarios: " . count($users) . " | Navegadores: " . count($browsers) . "\n\n";

        // Crear lista de todos los tests a ejecutar
        $allTests = [];
        foreach ($users as $index => $user) {
            foreach ($browsers as $browser) {
                $testId = "T" . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . "-" . strtoupper($browser);
                $allTests[] = [
                    'testId' => $testId,
                    'user' => $user,
                    'browser' => $browser
                ];
            }
        }

        // Ejecutar tests en lotes
        while ($completedTests < $totalTests) {
            // Iniciar nuevos procesos hasta el límite
            while (count($processes) < $maxConcurrent && !empty($allTests)) {
                $testInfo = array_shift($allTests);
                $testId = $testInfo['testId'];
                $user = $testInfo['user'];
                $browser = $testInfo['browser'];
                $testId = "T" . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . "-" . strtoupper($browser);

                $command = "php -f " . escapeshellarg(dirname(__DIR__) . "/run_single_test.php") . " " .
                    escapeshellarg($user['username']) . " " .
                    escapeshellarg($user['password']) . " " .
                    escapeshellarg($user['expected_role']) . " " .
                    escapeshellarg($user['expected_url']) . " " .
                    escapeshellarg($browser) . " " .
                    escapeshellarg($testId);

                $descriptorSpec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
                $pipes = [];
                $process = proc_open($command, $descriptorSpec, $pipes);

                // Configurar pipes en modo no bloqueante
                if ($process !== false) {
                    stream_set_blocking($pipes[1], false);
                    stream_set_blocking($pipes[2], false);
                }

                $processes[$testId] = [
                    'process' => $process,
                    'pipes' => $pipes,
                    'user' => $user['username'],
                    'browser' => $browser
                ];

                echo "[$testId] Proceso iniciado: {$user['username']} en $browser\n";
            }

            // Verificar procesos completados
            foreach ($processes as $testId => $info) {
                $result = proc_get_status($info['process']);
                if (!$result['running']) {
                    // Leer resultado ANTES de cerrar el proceso
                    stream_set_blocking($info['pipes'][1], true);
                    stream_set_blocking($info['pipes'][2], true);
                    $stdout = stream_get_contents($info['pipes'][1]);
                    $stderr = stream_get_contents($info['pipes'][2]);

                    // Cerrar pipes
                    fclose($info['pipes'][0]);
                    fclose($info['pipes'][1]);
                    fclose($info['pipes'][2]);

                    // Cerrar proceso
                    $exitCode = proc_close($info['process']);

                    // Procesar resultado
                    $decodedResult = json_decode($stdout, true);
                    if ($decodedResult) {
                        $results[$testId] = $decodedResult;
                    } else {
                        $results[$testId] = ['status' => 'ERROR', 'error' => trim($stderr) ?: 'No output', 'stdout' => substr($stdout, 0, 200), 'exitCode' => $exitCode];
                    }

                    echo "[$testId] Finalizado: {$info['user']} ({$results[$testId]['status']})\n";

                    unset($processes[$testId]);
                    $completedTests++;
                }
            }

            // Pequeña espera antes de verificar de nuevo
            if (count($processes) > 0 || !empty($allTests)) {
                usleep(500000); // 0.5 segundos
            }
        }

        self::generateReport($results);
        return $results;
    }

    private static function generateReport($results)
    {
        // Crear directorio reports si no existe
        if (!is_dir('reports')) {
            mkdir('reports', 0777, true);
        }

        $passed = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['status'] === 'PASSED')
                $passed++;
            else
                $failed++;
        }

        echo "\n" . str_repeat("═", 60) . "\n";
        echo "📊 REPORTE FINAL\n";
        echo "   ✅ Pasados: $passed\n";
        echo "   ❌ Fallidos: $failed\n";
        echo "   📈 Tasa éxito: " . round(($passed / count($results)) * 100, 1) . "%\n";
        echo str_repeat("═", 60) . "\n\n";

        // Guardar reporte HTML
        $report = "<html><body><h1>SauceDemo Parallel Test Report</h1>";
        foreach ($results as $testId => $result) {
            $status = $result['status'] === 'PASSED' ? '✅' : '❌';
            $report .= "<div>$testId: $status {$result['status']}</div>";
            if (isset($result['error'])) {
                $report .= "<div style='color:red;'>Error: {$result['error']}</div>";
            }
        }
        $report .= "</body></html>";

        file_put_contents('reports/report_' . date('Y-m-d_H-i-s') . '.html', $report);
        echo "📄 Reporte guardado en reports/\n";
    }
}
?>