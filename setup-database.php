<?php
/**
 * Resets and imports the final project_management.sql seed.
 * http://localhost/Web-Technologies-Project_Group-08/setup-database.php
 */
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

$sqlFile = __DIR__ . '/project_management.sql';
if (!is_readable($sqlFile)) {
    die('<p>Missing project_management.sql</p>');
}

$mysqlBin = '/Applications/XAMPP/xamppfiles/bin/mysql';
$socket   = DB_SOCKET;
$socketArg = is_readable($socket) ? '--socket=' . escapeshellarg($socket) : '-h127.0.0.1';

try {
    $dsn = is_readable($socket)
        ? 'mysql:unix_socket=' . $socket . ';charset=utf8mb4'
        : 'mysql:host=' . DB_HOST . ';port=3306;charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $pdo->exec('DROP DATABASE IF EXISTS `' . DB_NAME . '`');
    $pdo->exec('CREATE DATABASE `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');

    $import = sprintf(
        '%s -u%s %s %s < %s 2>&1',
        escapeshellcmd($mysqlBin),
        escapeshellarg(DB_USER),
        $socketArg,
        escapeshellarg(DB_NAME),
        escapeshellarg($sqlFile)
    );
    exec($import, $out, $code);

    if ($code !== 0) {
        throw new RuntimeException(implode("\n", $out));
    }

    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Database ready</title></head>';
    echo '<body style="font-family:system-ui;padding:40px;max-width:520px">';
    echo '<h1 style="color:#15803d">Database imported</h1>';
    echo '<p><code>project_management</code> now has the 3 demo users, workspace <strong>GRP08A</strong>, projects, and sample tasks.</p>';
    echo '<ul><li>mim@gmail.com / mim0@</li><li>sinha@gmail.com / sinha0</li><li>mohini@gmail.com / mohini0</li></ul>';
    echo '<p><a href="TASK1/index.php?page=login">Go to login →</a></p></body></html>';
} catch (Throwable $e) {
    echo '<h1 style="color:#b91c1c">Setup failed</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
