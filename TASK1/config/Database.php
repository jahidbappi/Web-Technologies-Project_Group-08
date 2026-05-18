<?php

class Database {
    private static ?PDO $instance = null;

    private static string $host = "localhost";
    private static string $user = "root";
    private static string $pass = "";
    private static string $dbName = "project_management";

    public static function connect(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die('<h3 style="color:red;font-family:sans-serif;">
                    Database connection failed.<br>
                    Make sure XAMPP MySQL is running and the DB "' . self::$dbName . '" exists.<br>
                    <small>' . htmlspecialchars($e->getMessage()) . '</small>
                </h3>');
            }
        }

        return self::$instance;
    }
}