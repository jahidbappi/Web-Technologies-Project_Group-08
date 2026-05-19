<?php

class ManageDatabaseConnection
{
    private $host = "localhost";
    private $dbname = "management_project";
    private $username = "root";
    private $password = "";

    public $connection;

    public function connect()
    {
        try
        {
            $this->connection = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbname,
                $this->username,
                $this->password
            );

            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $this->connection;
        }
        catch(PDOException $e)
        {
            die("Database Connection Failed : " . $e->getMessage());
        }
    }
}

?>