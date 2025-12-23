# Guía Completa: Testing de APIs en PHP

## Índice
1. [Introducción](#introducción)
2. [Configuración del Entorno](#configuración-del-entorno)
3. [Herramientas Principales](#herramientas-principales)
4. [Estructura del Proyecto](#estructura-del-proyecto)
5. [Testing Básico con Guzzle y PHPUnit](#testing-básico-con-guzzle-y-phpunit)
6. [Mocking de Respuestas](#mocking-de-respuestas)
7. [Validación de Esquemas JSON](#validación-de-esquemas-json)
8. [Testing Avanzado](#testing-avanzado)
9. [CI/CD Integration](#cicd-integration)
10. [Mejores Prácticas](#mejores-prácticas)

---

## Introducción

Testing de APIs es una práctica fundamental en desarrollo moderno. Permite validar que tus endpoints responden correctamente, mantienen el contrato de datos y manejan errores apropiadamente. En PHP, contamos con excelentes herramientas como PHPUnit y Guzzle para automatizar este proceso.

### Ventajas del Testing de APIs
- **Regresiones detectadas temprano**: Cambios en la API se detectan inmediatamente
- **Documentación ejecutable**: Los tests sirven como especificación viva de tu API
- **Confianza en refactoring**: Modifica código sabiendo que todo funciona
- **CI/CD automatizado**: Integración contínua sin intervención manual

---

## Configuración del Entorno

### Requisitos Previos
- PHP 7.4+
- Composer
- Git
- Editor de código (VS Code, PHPStorm, etc.)

### Instalación Inicial

```bash
# Crear un nuevo proyecto
mkdir my-api-tests
cd my-api-tests

# Inicializar Composer
composer init

# Instalar dependencias necesarias
composer require phpunit/phpunit guzzlehttp/guzzle
```

### Archivo `composer.json` Configurado

```json
{
    "name": "my-org/api-tests",
    "description": "Suite de tests para APIs",
    "require": {
        "php": "^7.4",
        "guzzlehttp/guzzle": "^7.0",
        "phpunit/phpunit": "^9.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0"
    },
    "autoload": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "test:watch": "phpunit --watch"
    }
}
```

---

## Herramientas Principales

### PHPUnit
Framework de testing basado en xUnit. Proporciona:
- Assertions para validaciones
- Fixtures para datos de test
- Mocking y stubbing
- Code coverage

### Guzzle
Cliente HTTP moderno para PHP. Permite:
- Realizar requests HTTP
- Manejar respuestas y errores
- Interceptores y middlewares
- Mock handler para testing

### Herramientas Adicionales
- **Pest PHP**: Framework de testing más moderno y elegante
- **Codeception**: Testing integral (unitario, integration, E2E)
- **VCR (php-vcr)**: Grabar y reproducir requests HTTP

---

## Estructura del Proyecto

```
my-api-tests/
├── tests/
│   ├── Feature/
│   │   ├── GitHubApiTest.php
│   │   ├── JSONPlaceholderTest.php
│   │   └── OpenWeatherMapTest.php
│   ├── Unit/
│   │   └── ApiClientTest.php
│   └── Fixtures/
│       ├── github-user.json
│       └── weather-response.json
├── src/
│   ├── ApiClient.php
│   └── ApiResponse.php
├── phpunit.xml
├── composer.json
├── composer.lock
└── README.md
```

### Archivo `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheResultFile=".phpunit.cache/test-results"
         executionOrder="depends,defects"
         forceCoversAnnotatedMethods="false"
         beStrictAboutCoversAnnotatedUncoveredMethods="false"
         beStrictAboutOutputDuringTests="true"
         beStrictAboutTestsThatDoNotTestAnything="true"
         beStrictAboutTodoTestedCode="false"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">
    <testsuites>
        <testsuite name="Feature Tests">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
        <testsuite name="Unit Tests">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
    </testsuites>

    <coverage cacheDirectory=".phpunit.cache/code-coverage"
              processUncoveredFiles="true"
              ignoreDeprecatedCodeUnitsFromCodeCoverage="true"
              disableCodeCoverageIgnore="false">
        <include>
            <directory suffix=".php">./src</directory>
        </include>
    </coverage>

    <php>
        <ini name="display_errors" value="1"/>
        <ini name="error_reporting" value="-1"/>
    </php>
</phpunit>
```

---

## Testing Básico con Guzzle y PHPUnit

### Ejemplo 1: Testing GitHub API

La API de GitHub es excelente para aprender porque:
- Es pública (sin autenticación necesaria)
- Tiene endpoints simples
- Documentación clara
- Respuestas JSON estructuradas

```php
<?php
// tests/Feature/GitHubApiTest.php

namespace Tests\Feature;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class GitHubApiTest extends TestCase
{
    private Client $http;
    private string $baseUri = 'https://api.github.com';

    protected function setUp(): void
    {
        $this->http = new Client([
            'base_uri' => $this->baseUri,
            'http_errors' => false, // No lanzar excepción en 4xx/5xx
        ]);
    }

    public function testGetUserReturns200()
    {
        $response = $this->http->request('GET', '/users/torvalds');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUserReturnsJsonResponse()
    {
        $response = $this->http->request('GET', '/users/torvalds');

        $this->assertEquals('application/json; charset=utf-8', 
            $response->getHeaderLine('content-type'));
    }

    public function testGetUserResponseStructure()
    {
        $response = $this->http->request('GET', '/users/torvalds');
        $data = json_decode((string)$response->getBody(), true);

        // Verificar que la respuesta es un array
        $this->assertIsArray($data);

        // Verificar campos obligatorios
        $this->assertArrayHasKey('login', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('public_repos', $data);

        // Verificar tipos
        $this->assertIsString($data['login']);
        $this->assertIsInt($data['id']);
        $this->assertIsString($data['name']);
        $this->assertIsInt($data['public_repos']);
    }

    public function testGetUserDataCorrect()
    {
        $response = $this->http->request('GET', '/users/torvalds');
        $data = json_decode((string)$response->getBody(), true);

        // Linus Torvalds
        $this->assertEquals('torvalds', $data['login']);
        $this->assertEquals('Linus Torvalds', $data['name']);
        $this->assertGreaterThan(10, $data['public_repos']);
    }

    public function testGetNonExistentUserReturns404()
    {
        $response = $this->http->request('GET', 
            '/users/this-user-does-not-exist-xyz123');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testSearchRepositoriesReturnsSuccessful()
    {
        $response = $this->http->request('GET', '/search/repositories', [
            'query' => [
                'q' => 'language:php stars:>1000',
                'sort' => 'stars',
                'order' => 'desc',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('items', $data);
        $this->assertIsArray($data['items']);
        $this->assertGreaterThan(0, count($data['items']));
    }
}
```

Ejecutar los tests:
```bash
./vendor/bin/phpunit tests/Feature/GitHubApiTest.php
```

### Ejemplo 2: Testing JSONPlaceholder (API fake para testing)

```php
<?php
// tests/Feature/JSONPlaceholderTest.php

namespace Tests\Feature;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class JSONPlaceholderTest extends TestCase
{
    private Client $http;

    protected function setUp(): void
    {
        $this->http = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com',
            'http_errors' => false,
        ]);
    }

    /**
     * Test GET - Obtener un post
     */
    public function testGetPostReturnsCorrectData()
    {
        $response = $this->http->request('GET', '/posts/1');

        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode((string)$response->getBody(), true);

        $this->assertEquals(1, $data['id']);
        $this->assertEquals(1, $data['userId']);
        $this->assertNotEmpty($data['title']);
        $this->assertNotEmpty($data['body']);
    }

    /**
     * Test POST - Crear un nuevo post
     */
    public function testCreatePostReturns201()
    {
        $payload = [
            'title' => 'Test Post',
            'body' => 'This is a test post created via PHPUnit',
            'userId' => 1,
        ];

        $response = $this->http->request('POST', '/posts', [
            'json' => $payload,
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($payload['title'], $data['title']);
        $this->assertEquals($payload['body'], $data['body']);
    }

    /**
     * Test PUT - Actualizar un post
     */
    public function testUpdatePostReturns200()
    {
        $payload = [
            'id' => 1,
            'title' => 'Updated Title',
            'body' => 'Updated body content',
            'userId' => 1,
        ];

        $response = $this->http->request('PUT', '/posts/1', [
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Updated Title', $data['title']);
    }

    /**
     * Test DELETE - Eliminar un post
     */
    public function testDeletePostReturns200()
    {
        $response = $this->http->request('DELETE', '/posts/1');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test GET List - Obtener lista con paginación
     */
    public function testGetPostsWithPaginationReturnsArray()
    {
        $response = $this->http->request('GET', '/posts', [
            'query' => [
                '_start' => 0,
                '_limit' => 10,
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);

        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(10, count($data));
        $this->assertGreaterThan(0, count($data));
    }
}
```

---

## Mocking de Respuestas

### ¿Cuándo Usar Mocking?

El mocking es esencial cuando:
- Los tests no deben depender de APIs externas
- Quieres probar comportamiento ante errores
- Necesitas tests rápidos y predecibles
- Trabajas en entorno sin conexión a internet

### Ejemplo: Mock Handler de Guzzle

```php
<?php
// tests/Unit/ApiClientTest.php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /**
     * Test respuesta exitosa con MockHandler
     */
    public function testSuccessfulApiCallWithMock()
    {
        // Crear un MockHandler con respuestas predefinidas
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], 
                json_encode([
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ])
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Realizar request
        $response = $client->request('GET', '/api/users/1');

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode((string)$response->getBody(), true);
        $this->assertEquals('John Doe', $data['name']);
        $this->assertEquals('john@example.com', $data['email']);
    }

    /**
     * Test manejo de errores (4xx/5xx)
     */
    public function testErrorResponseWithMock()
    {
        $mock = new MockHandler([
            new Response(404, ['Content-Type' => 'application/json'],
                json_encode(['error' => 'User not found'])
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false, // Importante: no lanzar excepción
        ]);

        $response = $client->request('GET', '/api/users/999');

        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode((string)$response->getBody(), true);
        $this->assertEquals('User not found', $data['error']);
    }

    /**
     * Test múltiples respuestas en secuencia
     */
    public function testMultipleSequentialRequests()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['id' => 1])),
            new Response(201, [], json_encode(['id' => 2])),
            new Response(200, [], json_encode(['id' => 3])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Primer request
        $resp1 = $client->request('GET', '/api/users/1');
        $data1 = json_decode((string)$resp1->getBody(), true);
        $this->assertEquals(1, $data1['id']);

        // Segundo request
        $resp2 = $client->request('POST', '/api/users');
        $data2 = json_decode((string)$resp2->getBody(), true);
        $this->assertEquals(2, $data2['id']);

        // Tercer request
        $resp3 = $client->request('GET', '/api/users/3');
        $data3 = json_decode((string)$resp3->getBody(), true);
        $this->assertEquals(3, $data3['id']);
    }

    /**
     * Test excepción de timeout
     */
    public function testConnectionTimeoutException()
    {
        $mock = new MockHandler([
            new RequestException(
                'Connection timeout',
                new Request('GET', '/api/users/1')
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->expectException(RequestException::class);
        $client->request('GET', '/api/users/1');
    }

    /**
     * Test respuesta vacía (204 No Content)
     */
    public function testEmptyResponseHandling()
    {
        $mock = new MockHandler([
            new Response(204), // No content
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->request('DELETE', '/api/users/1');

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty((string)$response->getBody());
    }

    /**
     * Test validación de headers
     */
    public function testResponseHeadersValidation()
    {
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'X-RateLimit-Remaining' => '59',
                'Cache-Control' => 'no-cache',
            ], json_encode(['status' => 'ok'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->request('GET', '/api/status');

        $this->assertStringContainsString('application/json', 
            $response->getHeaderLine('Content-Type'));
        $this->assertEquals('59', $response->getHeaderLine('X-RateLimit-Remaining'));
    }
}
```

### Validación de Requests Enviados

Puedes verificar que tu cliente envía requests correctamente:

```php
<?php
// Middleware para capturar requests
use GuzzleHttp\Middleware;

public function testRequestHeadersAreSentCorrectly()
{
    $container = [];
    $history = Middleware::history($container);

    $mock = new MockHandler([
        new Response(200, [], json_encode(['success' => true])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($history);

    $client = new Client([
        'handler' => $handlerStack,
        'headers' => [
            'Authorization' => 'Bearer token123',
            'Accept' => 'application/json',
        ]
    ]);

    $client->request('POST', '/api/login', [
        'json' => ['user' => 'john', 'password' => 'secret']
    ]);

    // Verificar la request que fue enviada
    $this->assertCount(1, $container);
    
    $transaction = $container[0];
    $request = $transaction['request'];

    $this->assertEquals('POST', $request->getMethod());
    $this->assertStringContainsString('/api/login', $request->getUri());
    $this->assertEquals('Bearer token123', 
        $request->getHeaderLine('Authorization'));
}
```

---

## Validación de Esquemas JSON

### ¿Qué es JSON Schema?

JSON Schema es una especificación para validar la estructura de datos JSON. Permite definir:
- Tipos de datos
- Campos requeridos
- Restricciones de valores
- Formatos especiales (email, URL, etc.)

### Implementación

```php
<?php
// tests/Feature/SchemaValidationTest.php

namespace Tests\Feature;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SchemaValidationTest extends TestCase
{
    private Client $http;

    protected function setUp(): void
    {
        $this->http = new Client([
            'base_uri' => 'https://jsonplaceholder.typicode.com',
            'http_errors' => false,
        ]);
    }

    /**
     * Schema para un usuario
     */
    private function getUserSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['id', 'name', 'email'],
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string', 'format' => 'email'],
                'phone' => ['type' => 'string'],
                'website' => ['type' => 'string'],
                'company' => ['type' => 'object'],
            ]
        ];
    }

    /**
     * Schema para lista de posts
     */
    private function getPostSchema(): array
    {
        return [
            'type' => 'object',
            'required' => ['userId', 'id', 'title', 'body'],
            'properties' => [
                'userId' => ['type' => 'integer'],
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'body' => ['type' => 'string'],
            ]
        ];
    }

    /**
     * Validar respuesta contra schema
     */
    private function validateAgainstSchema(array $data, array $schema): void
    {
        // Validar tipo
        if (isset($schema['type'])) {
            $this->assertCorrectType($data, $schema['type']);
        }

        // Validar campos requeridos
        if (isset($schema['required'])) {
            foreach ($schema['required'] as $field) {
                $this->assertArrayHasKey($field, $data,
                    "Field '{$field}' is required");
            }
        }

        // Validar propiedades
        if (isset($schema['properties'])) {
            foreach ($schema['properties'] as $field => $fieldSchema) {
                if (isset($data[$field]) && isset($fieldSchema['type'])) {
                    $this->assertCorrectType($data[$field], $fieldSchema['type']);
                }
            }
        }
    }

    /**
     * Helper para validar tipos
     */
    private function assertCorrectType($value, string $expectedType): void
    {
        switch ($expectedType) {
            case 'string':
                $this->assertIsString($value, 
                    "Expected string, got " . gettype($value));
                break;
            case 'integer':
                $this->assertIsInt($value, 
                    "Expected integer, got " . gettype($value));
                break;
            case 'object':
                $this->assertIsArray($value, 
                    "Expected object/array, got " . gettype($value));
                break;
            case 'array':
                $this->assertIsArray($value, 
                    "Expected array, got " . gettype($value));
                break;
            case 'boolean':
                $this->assertIsBool($value, 
                    "Expected boolean, got " . gettype($value));
                break;
        }
    }

    /**
     * Test validación de schema
     */
    public function testPostResponseMatchesSchema()
    {
        $response = $this->http->request('GET', '/posts/1');
        $data = json_decode((string)$response->getBody(), true);

        $this->validateAgainstSchema($data, $this->getPostSchema());
    }
}
```

---

## Testing Avanzado

### Clase Base para Tests de API

```php
<?php
// src/ApiTestCase.php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;

abstract class ApiTestCase extends TestCase
{
    protected Client $client;
    protected array $lastRequestHistory = [];

    /**
     * Crear cliente con mock handler
     */
    protected function createMockClient(array $responses)
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        // Agregar history middleware
        $history = Middleware::history($this->lastRequestHistory);
        $handlerStack->push($history);

        return new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);
    }

    /**
     * Obtener el último request
     */
    protected function getLastRequest()
    {
        if (empty($this->lastRequestHistory)) {
            $this->fail('No requests were made');
        }

        return end($this->lastRequestHistory)['request'];
    }

    /**
     * Obtener la última respuesta
     */
    protected function getLastResponse()
    {
        if (empty($this->lastRequestHistory)) {
            $this->fail('No requests were made');
        }

        return end($this->lastRequestHistory)['response'];
    }

    /**
     * Verificar que un campo existe en la respuesta JSON
     */
    protected function assertJsonHasField(array $json, string $field)
    {
        $this->assertArrayHasKey($field, $json,
            "JSON response missing field: {$field}");
    }

    /**
     * Verificar múltiples campos
     */
    protected function assertJsonHasFields(array $json, array $fields)
    {
        foreach ($fields as $field) {
            $this->assertJsonHasField($json, $field);
        }
    }

    /**
     * Verificar estructura anidada
     */
    protected function assertJsonHasNestedField(
        array $json,
        string $path,
        string $separator = '.'
    ) {
        $parts = explode($separator, $path);
        $current = $json;

        foreach ($parts as $part) {
            $this->assertIsArray($current, 
                "Cannot access '{$part}' in non-array value");
            $this->assertArrayHasKey($part, $current,
                "Missing nested field: {$path}");
            $current = $current[$part];
        }
    }
}
```

### Test de Integración Completo

```php
<?php
// tests/Feature/CompleteApiIntegrationTest.php

namespace Tests\Feature;

use Tests\ApiTestCase;
use GuzzleHttp\Psr7\Response;

class CompleteApiIntegrationTest extends ApiTestCase
{
    /**
     * Test flujo completo: GET -> POST -> PUT -> DELETE
     */
    public function testCompleteUserWorkflow()
    {
        // Mock responses
        $responses = [
            // GET /users
            new Response(200, ['Content-Type' => 'application/json'],
                json_encode([
                    ['id' => 1, 'name' => 'Alice'],
                    ['id' => 2, 'name' => 'Bob'],
                ])
            ),
            // POST /users
            new Response(201, ['Content-Type' => 'application/json'],
                json_encode(['id' => 3, 'name' => 'Charlie', 'created' => true])
            ),
            // GET /users/3
            new Response(200, ['Content-Type' => 'application/json'],
                json_encode(['id' => 3, 'name' => 'Charlie'])
            ),
            // PUT /users/3
            new Response(200, ['Content-Type' => 'application/json'],
                json_encode(['id' => 3, 'name' => 'Charles', 'updated' => true])
            ),
            // DELETE /users/3
            new Response(204),
        ];

        $this->client = $this->createMockClient($responses);

        // 1. GET lista de usuarios
        $response = $this->client->request('GET', 'https://api.example.com/users');
        $users = json_decode((string)$response->getBody(), true);
        $this->assertCount(2, $users);

        // 2. POST crear nuevo usuario
        $newUserData = ['name' => 'Charlie', 'email' => 'charlie@example.com'];
        $response = $this->client->request('POST', 'https://api.example.com/users',
            ['json' => $newUserData]
        );
        $newUser = json_decode((string)$response->getBody(), true);
        $this->assertEquals(3, $newUser['id']);
        $this->assertTrue($newUser['created']);

        // 3. GET usuario específico
        $response = $this->client->request('GET', 
            "https://api.example.com/users/{$newUser['id']}"
        );
        $user = json_decode((string)$response->getBody(), true);
        $this->assertEquals($newUser['id'], $user['id']);

        // 4. PUT actualizar usuario
        $updateData = ['name' => 'Charles'];
        $response = $this->client->request('PUT',
            "https://api.example.com/users/{$user['id']}",
            ['json' => $updateData]
        );
        $updated = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Charles', $updated['name']);

        // 5. DELETE usuario
        $response = $this->client->request('DELETE',
            "https://api.example.com/users/{$updated['id']}"
        );
        $this->assertEquals(204, $response->getStatusCode());
    }
}
```

---

## CI/CD Integration

### GitHub Actions

```yaml
# .github/workflows/api-tests.yml

name: API Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: ['7.4', '8.0', '8.1']

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP ${{ matrix.php-version }}
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-version }}
        extensions: curl, json
        tools: composer:v2

    - name: Get composer cache directory
      id: composer-cache
      run: echo "::set-output name=dir::$(composer config cache-files-dir)"

    - name: Cache composer dependencies
      uses: actions/cache@v3
      with:
        path: ${{ steps.composer-cache.outputs.dir }}
        key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
        restore-keys: ${{ runner.os }}-composer-

    - name: Install dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader

    - name: Run tests
      run: ./vendor/bin/phpunit

    - name: Generate coverage report
      run: ./vendor/bin/phpunit --coverage-clover coverage.xml

    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v3
      with:
        files: ./coverage.xml
```

---

## Mejores Prácticas

### 1. Tests Independientes
- Cada test debe ser autónomo
- No confiar en datos de otros tests
- Usar setup() y tearDown()

### 2. Nombres Descriptivos
```php
// ❌ Malo
public function testApi() { }

// ✅ Bueno
public function testGetUserByIdReturnsCorrectDataStructure() { }
public function testDeleteUserWithInvalidIdReturns404() { }
```

### 3. Una Cosa por Test
```php
// ❌ Malo: test hace demasiado
public function testUserEndpoint()
{
    // Crea user, obtiene, actualiza, elimina...
}

// ✅ Bueno: test enfocado
public function testCreateUserReturnsCorrectStatusCode() { }
public function testCreateUserReturnsNewUserId() { }
```

### 4. Usar Fixtures para Datos
```php
// tests/Fixtures/user-response.json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com"
}

// Test
public function testUserResponseFormat()
{
    $fixture = json_decode(
        file_get_contents(__DIR__ . '/Fixtures/user-response.json'),
        true
    );
    
    $this->validateAgainstSchema($fixture, $this->getUserSchema());
}
```

### 5. Organizar por Tipo
```
tests/
├── Feature/      # Tests que requieren API real
├── Unit/         # Tests con mocks
└── Integration/  # Tests con múltiples componentes
```

### 6. Usar Data Providers para Casos Múltiples
```php
/**
 * @dataProvider invalidUserProvider
 */
public function testInvalidUserHandling($userId, $expectedStatus)
{
    $response = $this->http->request('GET', "/users/{$userId}");
    $this->assertEquals($expectedStatus, $response->getStatusCode());
}

public function invalidUserProvider()
{
    return [
        'negative id' => [-1, 400],
        'zero id' => [0, 400],
        'non-existent' => [999999, 404],
        'invalid string' => ['abc', 400],
    ];
}
```

### 7. Test Casos de Error
```php
public function testApiErrorHandling()
{
    // Success case
    $this->testValidRequest();

    // Error cases
    $this->testMissingRequiredField();
    $this->testInvalidDataType();
    $this->testAuthenticationFailure();
    $this->testRateLimiting();
    $this->testServerError();
}
```

### 8. Documentar Comportamiento Esperado
```php
/**
 * Verifica que la API retorne paginación correcta
 * 
 * Requisito: El endpoint /users debe soportar parámetros:
 * - page: número de página (default: 1)
 * - limit: items por página (default: 20, máximo: 100)
 * 
 * @test
 */
public function testUserPaginationWithValidParameters()
{
    // ...
}
```

---

## Recursos Adicionales

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Guzzle Documentation](https://docs.guzzlephp.org/)
- [Pest PHP - Modern Testing Framework](https://pestphp.com/)
- [Codeception - Full-Stack Testing](https://codeception.com/)
- [JSON Schema Validation](https://json-schema.org/)

---

## Conclusión

Testing de APIs es fundamental para mantener calidad y confiabilidad. Con PHPUnit y Guzzle tienes herramientas poderosas para:

- Validar comportamiento de endpoints
- Automatizar testing en CI/CD
- Documentar requisitos de API
- Detectar regresiones temprano

Comienza con tests simples y ve agregando complejidad según necesites. ¡Feliz testing! 🚀
