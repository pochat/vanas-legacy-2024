<?php

# Libreria de funciones
require("lib/sp_general.inc.php");


/**
 * @function    backupDatabaseTables
 * @author      CodexWorld
 * @link        http://www.codexworld.com
 * @usage       Backup database tables and save in SQL file
 */
function backupDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $tables = '*')
{
    //connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = $db->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }

    //loop through the tables
    foreach ($tables as $table) {
        $result = $db->query("SELECT * FROM $table");
        $numColumns = $result->field_count;

        $return .= "DROP TABLE $table;";

        $result2 = $db->query("SHOW CREATE TABLE $table");
        $row2 = $result2->fetch_row();

        $return .= "nn" . $row2[1] . ";nn";

        for ($i = 0; $i < $numColumns; $i++) {
            while ($row = $result->fetch_row()) {
                $return .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $numColumns; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = mb_ereg_replace("n", "n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($numColumns - 1)) {
                        $return .= ',';
                    }
                }
                $return .= ");n";
            }
        }

        $return .= "nnn";
    }

    //save file
    $handle = fopen('db-backup-' . time() . '.sql', 'w+');
    fwrite($handle, $return);
    fclose($handle);
}

backupDatabaseTables('localhost', 'root', '', 'vanas_prod');
?>