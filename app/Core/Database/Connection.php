<?php

namespace App\Core\Database;

use PDO;
class Connection {

    private \PDO $pdo;
    public function __construct() {
        $this->pdo = new \PDO("mysql:host=localhost;port=3306;dbname=financial_app_db", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function pdo(): \PDO {
        return $this->pdo;
    }
}