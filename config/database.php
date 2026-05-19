<?php

/**
 * Shared MySQL connection — socket first (XAMPP Mac), short TCP timeouts.
 */

const DB_HOST   = '127.0.0.1';
const DB_NAME   = 'project_management';
const DB_USER   = 'root';
const DB_PASS   = '';
const DB_PORTS  = [3306, 3307];
const DB_SOCKET = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
const DB_CONNECT_TIMEOUT = 2;

function db_pdo_options(): array {
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    if (defined('PDO::MYSQL_ATTR_CONNECT_TIMEOUT')) {
        $opts[PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = DB_CONNECT_TIMEOUT;
    }
    return $opts;
}

function db_connect_pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $last = null;

    if (is_readable(DB_SOCKET)) {
        try {
            $dsn = 'mysql:unix_socket=' . DB_SOCKET . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, db_pdo_options());
            return $pdo;
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    foreach (DB_PORTS as $port) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST,
                $port,
                DB_NAME
            );
            $pdo = new PDO($dsn, DB_USER, DB_PASS, db_pdo_options());
            return $pdo;
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    db_connection_failed_html($last);
}

function db_connect_mysqli(): mysqli {
    static $conn = null;
    if ($conn instanceof mysqli && !$conn->connect_error) {
        return $conn;
    }

    $last = null;

    if (is_readable(DB_SOCKET)) {
        $candidate = mysqli_init();
        if ($candidate) {
            mysqli_options($candidate, MYSQLI_OPT_CONNECT_TIMEOUT, DB_CONNECT_TIMEOUT);
            if (@mysqli_real_connect($candidate, 'localhost', DB_USER, DB_PASS, DB_NAME, 0, DB_SOCKET)) {
                $candidate->set_charset('utf8mb4');
                $conn = $candidate;
                return $conn;
            }
            $last = mysqli_connect_error();
        }
    }

    foreach (DB_PORTS as $port) {
        $candidate = mysqli_init();
        if (!$candidate) {
            continue;
        }
        mysqli_options($candidate, MYSQLI_OPT_CONNECT_TIMEOUT, DB_CONNECT_TIMEOUT);
        if (@mysqli_real_connect($candidate, DB_HOST, DB_USER, DB_PASS, DB_NAME, $port)) {
            $candidate->set_charset('utf8mb4');
            $conn = $candidate;
            return $conn;
        }
        $last = mysqli_connect_error();
    }

    db_connection_failed_html(new Exception($last ?? 'Connection refused'));
}

function db_connection_failed_html(?Throwable $last): never {
    $msg = $last ? $last->getMessage() : 'Unknown error';
    $mysqlRunning = is_readable(DB_SOCKET);

    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>MySQL not running</title>';
    echo '<style>body{font-family:system-ui,sans-serif;max-width:560px;margin:48px auto;padding:0 20px;color:#1e293b}';
    echo 'h1{color:#b91c1c;font-size:1.35rem}code{background:#f1f5f9;padding:2px 6px;border-radius:4px}';
    echo 'ol{line-height:1.7}.ok{color:#15803d}.bad{color:#b91c1c}</style></head><body>';
    echo '<h1>Database connection failed</h1>';
    echo '<p><strong>MySQL is not running.</strong> (' . htmlspecialchars($msg) . ')</p>';
    echo '<ol><li>Open XAMPP → <strong>Start</strong> MySQL.</li>';
    echo '<li>Reload this page.</li></ol>';
    echo '<p>Socket: ' . ($mysqlRunning ? '<span class="ok">ready</span>' : '<span class="bad">not found</span>') . '</p>';
    echo '</body></html>';
    exit;
}
