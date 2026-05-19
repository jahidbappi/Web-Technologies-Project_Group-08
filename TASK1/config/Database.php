<?php

class Database {
    private static ?PDO $instance = null;

    private static string $host = "127.0.0.1";
    private static array $ports = [3306, 3307];
    private static string $user = "root";
    private static string $pass = "";
    private static string $dbName = "project_management";

    public static function connect(): PDO {
        if (self::$instance === null) {
            $last = null;
            foreach (self::$ports as $port) {
                $dsn = 'mysql:host=' . self::$host . ';port=' . $port . ';dbname=' . self::$dbName . ';charset=utf8mb4';
                try {
                    self::$instance = new PDO($dsn, self::$user, self::$pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return self::$instance;
                } catch (PDOException $e) {
                    $last = $e;
                }
            }
            die('<h3 style="color:red;font-family:sans-serif;">
                Database connection failed.<br>
                Make sure XAMPP MySQL is running and the DB "' . self::$dbName . '" exists.<br>
                <small>' . htmlspecialchars($last ? $last->getMessage() : 'Unknown error') . '</small>
            </h3>');
        }

        return self::$instance;
    }
}