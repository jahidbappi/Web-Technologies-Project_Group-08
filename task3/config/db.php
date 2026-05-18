<?php

/**
 * Connects to the database imported from `project_management.sql`.
 *
 * Tries multiple TCP ports so it works whether XAMPP MySQL listens on
 * 3306 (default) or 3307 (some setups / exported dumps).
 */

const DB_HOST = '127.0.0.1';
const DB_NAME = 'project_management';
const DB_USER = 'root';
const DB_PASS = '';

/** Tried first, then DB_PORTS_FALLBACK. Override via TASK3_DB_PORTS="3307,3308" if needed. */
const DB_PORT_PRIMARY = 3306;
const DB_PORTS_FALLBACK = [3307, 3306];

/** Local XAMPP socket fallback if TCP ports are unavailable. */
const DB_SOCKET = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $env = getenv('TASK3_DB_PORTS');
    if ($env !== false && $env !== '') {
        $ports = array_map('intval', array_filter(array_map('trim', explode(',', $env))));
    } else {
        $ports = array_values(array_unique(array_merge(
            [DB_PORT_PRIMARY],
            DB_PORTS_FALLBACK
        )));
    }

    $lastException = null;
    foreach ($ports as $port) {
        if ($port < 1) {
            continue;
        }
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . $port . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $lastException = $e;
        }
    }

    $socket = getenv('TASK3_DB_SOCKET') ?: DB_SOCKET;
    if ($socket && file_exists($socket) && is_readable($socket)) {
        try {
            $dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $lastException = $e;
        }
    }

    http_response_code(500);
    echo '<h2 style="font-family:sans-serif;color:#b91c1c">Database connection failed</h2>';
    echo '<pre style="font-family:monospace;background:#fee2e2;padding:12px;border-radius:8px">'
        . htmlspecialchars($lastException ? $lastException->getMessage() : 'Unknown error') . '</pre>';
    echo '<p style="font-family:sans-serif">Start <strong>MySQL</strong> in the XAMPP control panel, '
        . 'then confirm phpMyAdmin shows database <code>' . htmlspecialchars(DB_NAME) . '</code>.</p>';
    echo '<p style="font-family:sans-serif">Ports tried: <code>' . htmlspecialchars(implode(', ', $ports)) . '</code>. '
        . 'Override with env <code>TASK3_DB_PORTS</code> (comma-separated).</p>';
    exit;
}
