<?php

// CREATE TABLE users (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     username VARCHAR(50) NOT NULL,
//     password VARCHAR(255) NOT NULL,
//     email VARCHAR(100) NOT NULL,
//     create_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );

class connection
{
    private const DBHOST = "localhost";
    private const DBUSER = "root";
    private const DBPASS = "";
    private const DBNAME = "tailwind-site";
    protected $connection;

    function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=" . self::DBHOST . ";dbname=" . self::DBNAME, self::DBUSER, self::DBPASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            echo "Connection failed: " . $error->getMessage();
        }
    }
}

new connection();
