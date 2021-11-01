<?php

namespace Framework;

use PDO;
use PDOException;

final class Database
{

    private PDO $_pdo;

    /**
     * Connect to a database
     * @param string $host
     * @param int $port
     * @param string $dbName
     * @param string $username
     * @param string $password
     * @throws PDOException
     */
    public function __construct(string $host, int $port, string $dbName, string $username, string $password)
    {
        $this->_pdo = new PDO("mysql:dbname=$dbName;host=$host;port=$port;charset=UTF8", $username, $password);
    }

    /**
     * Get the pdo object (database connection)
     * @return PDO
     */
    public function get(): PDO
    {
        return $this->_pdo;
    }

}