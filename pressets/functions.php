<?php
function env(string $defineName, mixed $default = null): mixed
{
    // Cache em memória para ler o arquivo .env apenas uma vez por requisição
    static $variables = null;

    if ($variables === null) {
        $envPath = __DIR__ . '/../.env';

        if (!file_exists($envPath)) {
            throw new Exception('Could not find .env file at: ' . $envPath);
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $variables = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignora comentários e linhas sem '='
            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            // Divide apenas na primeira ocorrência do '='
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Trata valores entre aspas (duplas, simples ou crases)
            if (preg_match('/^([\'"`])(.*)\1$/s', $value, $matches)) {
                $value = $matches[2];
            } else {
                // Remove comentários inline caso não esteja entre aspas
                $value = trim(explode('#', $value)[0]);
            }

            // Conversão de tipos de dados comuns
            $lowerValue = strtolower($value);
            if ($lowerValue === 'true') {
                $value = true;
            } elseif ($lowerValue === 'false') {
                $value = false;
            } elseif ($lowerValue === 'null') {
                $value = null;
            } elseif (is_numeric($value)) {
                $value = str_contains($value, '.') ? (float) $value : (int) $value;
            }

            $variables[$key] = $value;
        }
    }

    return $variables[$defineName] ?? $default;
}

function config(string $pressetKey, mixed $default = null): mixed
{
    $cahceDefinesPath = __DIR__ . '/../bootstrap/cache';
    $cahceDefinesFile = $cahceDefinesPath.'/defines.php';
    static $defines = null;

    if (file_exists($cahceDefinesFile) && is_readable($cahceDefinesFile)) {
        $defines = include($cahceDefinesFile);
    } else {
        $defines = include(__DIR__ . '/define.php');
        if (!file_exists($cahceDefinesPath)) {
            mkdir($cahceDefinesPath, 0777, true);
        }
        file_put_contents($cahceDefinesFile, '<?php return '.var_export($defines, true).';');
    }

    // Busca pela notação de ponto
    $keys = explode('.', $pressetKey);
    $current = $defines;

    foreach ($keys as $key) {
        if (is_array($current) && array_key_exists($key, $current)) {
            $current = $current[$key];
        } else {
            return $default;
        }
    }

    return $current;
}

function getCurrentRouteAndParams(){
    $routeInfo = $_SERVER['REQUEST_URI'];
    $routeInfo = trim($routeInfo, '/');

    $currentURL = explode('/', $routeInfo);
    $routeURL = $currentURL[0] ?? '';

    $routeURL = \App\Core\Helpers\StringHelper::clean($routeURL);

    if ($routeURL == '') $routeURL = '/';

    $params = [];

    for ($i = 2; isset($currentURL[$i]); $i++) {
        $params[] = $currentURL[$i];
    }

    return [
        'route' => $routeURL,
        'params' => $params
    ];
}