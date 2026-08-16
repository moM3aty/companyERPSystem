<?php
// Path: public/index.php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('NOUR_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Start Output Buffering
|--------------------------------------------------------------------------
| This prevents views that echo HTML directly from sending output before
| HTTP headers are prepared.
*/
ob_start();

/*
|--------------------------------------------------------------------------
| Environment Helper
|--------------------------------------------------------------------------
*/
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key]
            ?? $_SERVER[$key]
            ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        $lower = strtolower((string) $value);

        if ($lower === 'true') {
            return true;
        }

        if ($lower === 'false') {
            return false;
        }

        if ($lower === 'null') {
            return null;
        }

        return $value;
    }
}

/*
|--------------------------------------------------------------------------
| Load Helpers
|--------------------------------------------------------------------------
*/
$possibleHelperPaths = [
    BASE_PATH . '/app/Core/Helpers/functions.php',
    BASE_PATH . '/app/core/Helpers/functions.php',
    BASE_PATH . '/app/Core/helpers/functions.php',
    BASE_PATH . '/app/core/helpers/functions.php',
];

foreach ($possibleHelperPaths as $helperPath) {
    if (file_exists($helperPath)) {
        require_once $helperPath;
        break;
    }
}

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/
$configPath = BASE_PATH . '/config';
$config = [];

if (is_dir($configPath)) {
    foreach (glob($configPath . '/*.php') as $configFile) {
        $key = basename($configFile, '.php');
        $config[$key] = require $configFile;
    }
}

/*
|--------------------------------------------------------------------------
| Resolve Request URI
|--------------------------------------------------------------------------
*/
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUri = dirname($scriptName);

$requestUri = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
) ?? '/';

/*
|--------------------------------------------------------------------------
| Strip Application Subfolder
|--------------------------------------------------------------------------
| Example:
| /ERP/public/dashboard -> /dashboard
|--------------------------------------------------------------------------
*/
if (
    $baseUri !== '/'
    && $baseUri !== '\\'
    && strpos($requestUri, $baseUri) === 0
) {
    $requestUri = substr(
        $requestUri,
        strlen($baseUri)
    );
}

/*
|--------------------------------------------------------------------------
| Strip index.php
|--------------------------------------------------------------------------
*/
if (strpos($requestUri, '/index.php') === 0) {
    $requestUri = substr(
        $requestUri,
        10
    );
}

$requestUri = (
    $requestUri === ''
    || $requestUri === false
)
    ? '/'
    : '/' . ltrim($requestUri, '/');

$_SERVER['REQUEST_URI'] = $requestUri;

/*
|--------------------------------------------------------------------------
| Composer Autoloader
|--------------------------------------------------------------------------
*/
$autoloader = BASE_PATH . '/vendor/autoload.php';

if (file_exists($autoloader)) {
    require_once $autoloader;
}

/*
|--------------------------------------------------------------------------
| Fallback PSR-4 Autoloader
|--------------------------------------------------------------------------
*/
spl_autoload_register(function (string $class): void {

    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    $prefixLength = strlen($prefix);

    if (strncmp($prefix, $class, $prefixLength) !== 0) {
        return;
    }

    $relativeClass = substr(
        $class,
        $prefixLength
    );

    $parts = explode(
        '\\',
        $relativeClass
    );

    /*
    |--------------------------------------------------------------------------
    | Exact Path First
    |--------------------------------------------------------------------------
    */
    $exactFile = $baseDir
        . str_replace(
            '\\',
            '/',
            $relativeClass
        )
        . '.php';

    if (file_exists($exactFile)) {
        require_once $exactFile;
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Case-Insensitive Path Resolution
    |--------------------------------------------------------------------------
    */
    $currentDir = rtrim(
        $baseDir,
        '/'
    );

    foreach ($parts as $index => $part) {

        $isLast = (
            $index === count($parts) - 1
        );

        $target = $isLast
            ? $part . '.php'
            : $part;

        if (
            !is_dir($currentDir)
            && !$isLast
        ) {
            return;
        }

        $files = @scandir($currentDir);

        if ($files === false) {
            return;
        }

        $found = false;

        foreach ($files as $file) {
            if (
                strcasecmp(
                    $file,
                    $target
                ) === 0
            ) {
                $currentDir .= '/' . $file;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return;
        }
    }

    if (is_file($currentDir)) {
        require_once $currentDir;
    }
});

/*
|--------------------------------------------------------------------------
| Application Boot
|--------------------------------------------------------------------------
*/
try {

    $app = require_once BASE_PATH . '/bootstrap/app.php';

    /*
    |--------------------------------------------------------------------------
    | Bind Config
    |--------------------------------------------------------------------------
    */
    if (method_exists($app, 'instance')) {
        $app->instance(
            'config',
            $config
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Capture Request
    |--------------------------------------------------------------------------
    */
    $request = App\Core\Http\Request::capture();

    /*
    |--------------------------------------------------------------------------
    | Resolve Router
    |--------------------------------------------------------------------------
    */
    $router = $app->make(
        \App\Core\Routing\Router::class
    );

    /*
    |--------------------------------------------------------------------------
    | Dispatch Request
    |--------------------------------------------------------------------------
    */
    $response = $router->dispatch(
        $request
    );

    /*
    |--------------------------------------------------------------------------
    | Get Any Output Produced Directly By Views
    |--------------------------------------------------------------------------
    */
    $bufferedOutput = '';

    if (ob_get_level() > 0) {
        $bufferedOutput = ob_get_contents() ?: '';
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Proper Response Object
    |--------------------------------------------------------------------------
    */
    if ($response instanceof \App\Core\Http\Response) {

        /*
        | If a view already echoed content, keep it only when the Response
        | itself contains no content.
        */
        if (
            $bufferedOutput !== ''
            && $response->getContent() === ''
        ) {
            $response = new \App\Core\Http\Response(
                $bufferedOutput,
                $response->getStatusCode()
            );
        }

        /*
        | Clear output buffer before Response sends content.
        */
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $response->send();

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Handle String / HTML Response
    |--------------------------------------------------------------------------
    */
    if (is_string($response)) {

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $html = $response;

        if ($bufferedOutput !== '') {
            $html = $bufferedOutput . $html;
        }

        $responseObject = new \App\Core\Http\Response(
            $html,
            200,
            [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]
        );

        $responseObject->send();

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Controllers That Echo Their View And Return Null
    |--------------------------------------------------------------------------
    */
    if ($response === null) {

        if (ob_get_level() > 0) {
            $bufferedOutput = ob_get_contents() ?: '';
            ob_end_clean();
        }

        if ($bufferedOutput !== '') {

            $responseObject = new \App\Core\Http\Response(
                $bufferedOutput,
                200,
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ]
            );

            $responseObject->send();

            exit;
        }

        throw new \RuntimeException(
            'Router dispatch returned null and produced no output. '
            . 'The matched controller action must return a Response or HTML content.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Any Other Return Type
    |--------------------------------------------------------------------------
    */
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    $responseObject = new \App\Core\Http\Response(
        (string) $response,
        200
    );

    $responseObject->send();

} catch (\Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | Clean Existing Output
    |--------------------------------------------------------------------------
    */
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    /*
    |--------------------------------------------------------------------------
    | Set Error Status Safely
    |--------------------------------------------------------------------------
    */
    if (!headers_sent()) {
        http_response_code(500);
    }

    /*
    |--------------------------------------------------------------------------
    | Error Page
    |--------------------------------------------------------------------------
    */
    echo '<div style="font-family:system-ui,-apple-system,sans-serif;padding:30px;max-width:900px;margin:30px auto;background:#ffffff;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.1);border-left:6px solid #d32f2f;">';

    echo '<h2 style="color:#d32f2f;margin-top:0;">Critical System Error</h2>';

    echo '<p style="font-size:16px;color:#333;">';
    echo '<strong>Message:</strong> ';
    echo htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    );
    echo '</p>';

    echo '<p style="font-size:14px;color:#666;">';
    echo '<strong>File:</strong> ';
    echo htmlspecialchars(
        $e->getFile(),
        ENT_QUOTES,
        'UTF-8'
    );
    echo ' on line ';
    echo (int) $e->getLine();
    echo '</p>';

    echo '<pre style="background:#1e293b;color:#f8fafc;padding:15px;border-radius:8px;overflow-x:auto;font-size:12px;line-height:1.5;">';

    echo htmlspecialchars(
        $e->getTraceAsString(),
        ENT_QUOTES,
        'UTF-8'
    );

    echo '</pre>';
    echo '</div>';
}