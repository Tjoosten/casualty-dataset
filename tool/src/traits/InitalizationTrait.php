<?php

namespace NoMoreWar\Casualties\Traits;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait InitalizationTrait
 *
 * @package NoMoreWar\Casualties\Commands
 */
trait InitalizationTrait
{
    public function createDatabaseTables($dbServer)
    {
        $dbName        = getenv('DB_NAME');
        $migrationPath = __DIR__ . '/../../../db-scheme/database-scheme.sql';
        $scheme        = file_get_contents($migrationPath);

        if ($dbServer->query("USE {$dbName}") && $dbServer->query($scheme)) {
             return true;
        }

        return false;
    }

    public function seedNaraData($dbServer, $file, $table)
    {
        $query = "LOAD DATA LOCAL INFILE '{$file}' INTO TABLE {$table} FIELDS TERMINATED BY '|'";

        if ($dbServer->query($query)) {
            return true;
        }

        return false;
    }

    public function truncateTable($dbServer, $table)
    {
        $dbServer->query("TRUNCATE TABLE $table");
    }

    public function createALlCasualties($dbServer, $vietnamFile, $koreanFile, $table)
    {
        $vietnamImport = $this->seedNaraData($dbServer, $vietnamFile, $table);
        $koreanImport  = $this->seedNaraData($dbServer, $koreanFile, $table);

        if ($vietnamImport && $koreanImport) {
            return true;
        }

        return false;
    }

}