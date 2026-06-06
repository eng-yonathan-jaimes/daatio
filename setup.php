<?php

/**
 * Database setup script for Daatio
 * Reads database.sql (and deploy/sql/*.sql) and executes against MySQL.
 *
 * Usage:
 *   php setup.php
 *
 * Credentials are read from .env.
 */

$envPath = __DIR__ . '/.env';
$sqlPath = __DIR__ . '/database.sql';
$deploySqlDir = __DIR__ . '/deploy/sql';

// ---------- Read .env ----------
if (!file_exists($envPath)) {
    exit(".env file not found at {$envPath}\n");
}

$envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($envLines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[$parts[0]] = trim($parts[1], " \t\n\r\0\x0B\"'");
    }
}

$host     = $env['DB_HOST']     ?? '127.0.0.1';
$port     = $env['DB_PORT']     ?? '3306';
$database = $env['DB_DATABASE'] ?? 'daatio';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

echo "Connecting to MySQL at {$host}:{$port} as {$username} ...\n";

// ---------- Connect (no database selected yet) ----------
try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected.\n";
} catch (PDOException $e) {
    exit("Connection failed: " . $e->getMessage() . "\n");
}

// ---------- Create database if it doesn't exist ----------
echo "Ensuring database '{$database}' exists...\n";
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `{$database}`");
echo "Database '{$database}' selected.\n";

// ---------- Execute SQL files ----------
$sqlFiles = [$sqlPath];

// Add deploy SQL files
if (is_dir($deploySqlDir)) {
    $deployFiles = glob($deploySqlDir . '/*.sql');
    sort($deployFiles);
    foreach ($deployFiles as $file) {
        // Skip notes files
        if (str_contains(basename($file), 'notes')) {
            continue;
        }
        $sqlFiles[] = $file;
    }
}

function executeSqlFile(PDO $pdo, string $filePath): void
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "  WARNING: Could not read {$filePath}\n";
        return;
    }

    // Strip CREATE DATABASE and USE statements — we've already handled those
    $content = preg_replace('/CREATE\s+DATABASE\s+(IF\s+NOT\s+EXISTS\s+)?`?\w+`?\s*;/i', '', $content);
    $content = preg_replace('/USE\s+`?\w+`?\s*;/i', '', $content);

    // Split by semicolons, preserving multi-line statements
    $statements = [];
    $current    = '';
    $lines      = explode("\n", $content);

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
            continue;
        }
        $current .= $line . "\n";
        if (str_ends_with($trimmed, ';')) {
            $stmt = trim($current);
            if ($stmt !== '' && $stmt !== ';') {
                $statements[] = $stmt;
            }
            $current = '';
        }
    }

    // Remaining content without trailing semicolon
    if (trim($current) !== '') {
        $statements[] = trim($current);
    }

    $fileName = basename($filePath);
    $executed = 0;
    $errors   = 0;

    foreach ($statements as $i => $stmt) {
        try {
            $pdo->exec($stmt);
            $executed++;
        } catch (PDOException $e) {
            $errors++;
            echo "  ERROR in {$fileName} (statement #" . ($i + 1) . "): " . $e->getMessage() . "\n";
            echo "  SQL: " . substr($stmt, 0, 120) . "...\n";
        }
    }

    echo "  {$fileName}: {$executed} executed, {$errors} errors\n";
}

echo "\nExecuting SQL files:\n";
foreach ($sqlFiles as $file) {
    executeSqlFile($pdo, $file);
}

echo "\nSetup complete.\n";
