<?php

require_once dirname(__DIR__, 2) . '/config/database.php';

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance === null) {
            self::$instance = db_connect_pdo();
        }
        return self::$instance;
    }
}
