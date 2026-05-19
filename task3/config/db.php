<?php

require_once dirname(__DIR__, 2) . '/config/database.php';

function db(): PDO {
    return db_connect_pdo();
}
