<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$projectDir = dirname(__DIR__);

$env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: null;
if ($env !== 'test') {
    putenv('APP_ENV=test');
    $_ENV['APP_ENV'] = 'test';
    $_SERVER['APP_ENV'] = 'test';
}

$dotenv = new Dotenv();
$envLocalPhp = $projectDir . '/.env.local.php';

if (is_file($envLocalPhp)) {
    /** @var array<string, mixed> $vars */
    $vars = require $envLocalPhp;
    $dotenv->populate($vars);
} else {
    $dotenv->usePutenv()->bootEnv($projectDir . '/.env', 'test');
}

/**
 * Helper to run bin/console commands reliably.
 */
$php = escapeshellarg(PHP_BINARY);
$console = "{$php} " . escapeshellarg($projectDir . '/bin/console');
$envArg = '--env=test';
$ni = '--no-interaction';

$run = static function (string $cmd) use ($console): void {
    passthru($console . ' ' . $cmd, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Command failed: bin/console {$cmd}\n");
        exit($code);
    }
};

/**
 * set up new DB
 */
$run("doctrine:database:drop {$envArg} --force --if-exists");
$run("doctrine:database:create {$envArg} {$ni}");

$run("doctrine:migrations:migrate {$envArg} {$ni}");
