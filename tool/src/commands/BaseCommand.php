<?php

namespace NoMoreWar\Casualties\Commands;

use Dotenv\Dotenv;
use NoMoreWar\Casualties\Exceptions\DatabaseException;
use PDO;
use PDOException;
use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    /**
     *  Connection variable for the database server.
     *
     * @var PDO $pdo;
     */
    public $pdo;

    /**
     * Parent function for the Command classes.
     *
     * @return PDO
     */
    public function __construct()
    {
        parent::__construct(); // Call the construct from the command class first.

        $dotenv = new Dotenv(__DIR__ . '/../../');      // Locate the environment file.
        $dotenv->load(); // Load the environment file.

        try { // To connection with the database server.
            $dsn = 'mysql: dbname=' . getenv('DB_NAME') . ';host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT');
            $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ]);

            return $this->pdo;
        } catch (PDOException $databaseException) { // Could not connect to the server.
            throw new DatabaseException($databaseException);
        }
    }
}