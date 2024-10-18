<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TablesController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // $mysqlInput = $request->input('mysql');

        
        $final_data = [];

        $mysqlInput = ($this->getDatabaseStructure());

        foreach ($mysqlInput as $key => $value) {
            $sqlite = "";
            $lines = explode("\n", $value);

            $currentTable = '';
            $keys = [];

            $skip = [
                '/^CREATE DATABASE/i',
                '/^USE/i',
                '/^\/\*/i',
                '/^--/i'
            ];

            foreach ($lines as $line) {
                $line = trim($line);

                // Skip lines that match regexes in the skip array
                foreach ($skip as $pattern) {
                    if (preg_match($pattern, $line)) {
                        continue 2;
                    }
                }

                // Include all `INSERT` lines and replace \' by ''
                if (preg_match('/^(INSERT|\()/i', $line)) {
                    $sqlite .= preg_replace("/\\\\'/", "''", $line) . "\n";
                    continue;
                }

                // Print the `CREATE` line as is and capture the table name
                if (preg_match('/^\s*CREATE TABLE.*[`"](.*)[`"]/', $line, $matches)) {
                    $currentTable = $matches[1];
                    $sqlite .= "\n" . $line . "\n";
                    continue;
                }

                // Clean table end line like: ) ENGINE=InnoDB ...
                if (str_starts_with($line, ')')) {
                    $sqlite .= ");\n";
                    continue;
                }

                // Remove CONSTRAINT ... part from lines
                $line = preg_replace('/^CONSTRAINT [`"][\w]+[`"]\s+/i', '', $line);

                // Replace "XXXXX KEY" by "KEY" except "PRIMARY KEY", "FOREIGN KEY" and "UNIQUE KEY"
                $line = preg_replace('/^[^FOREIGN][^PRIMARY][^UNIQUE]\w+\s+KEY/i', 'KEY', $line);

                // Handle UNIQUE or regular KEY definitions
                if (preg_match('/^(UNIQUE\s)*KEY\s+[`"](\w+)[`"]\s+\([`"](\w+)[`"]/', $line, $matches)) {
                    $keyUnique = $matches[1] ?? "";
                    $keyName = $matches[2];
                    $colName = $matches[3];
                    $keys[] = 'CREATE ' . $keyUnique . 'INDEX `' . $currentTable . '_' . $keyName . '` ON `' . $currentTable . '` (`' . $colName . '`);';
                    continue;
                }

                // Print all fields definition lines except "KEY" lines and lines starting with ")"
                if (!str_starts_with($line, ')') && !preg_match('/[\w]+\sKEY/', $line)) {
                    // Clear invalid keywords
                    $line = preg_replace('/AUTO_INCREMENT|CHARACTER SET [^ ]+|UNSIGNED/i', '', $line);
                    $line = preg_replace('/DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP|COLLATE [^ ]+/i', '', $line);
                    $line = preg_replace('/COMMENT\s[\'"`].*?[\'"`]/i', '', $line);
                    $line = preg_replace('/SET\([^)]+\)|ENUM[^)]+\)/i', 'TEXT ', $line);
                    // Clear weird MySQL types such as varchar(40) and int(11)
                    $line = preg_replace('/int\([0-9]*\)/i', 'INTEGER', $line);
                    $line = preg_replace('/varchar\([0-9]*\)|LONGTEXT/i', 'TEXT', $line);
                }

                if ($line !== "") {
                    $sqlite .= $line . "\n";
                }
            }

            // Fix last table line with comma
            $sqlite = preg_replace('/,\n\);/', "\n);", $sqlite);

            // Include all gathered keys as CREATE INDEX
            $sqlite .= "\n\n" . implode("\n", $keys) . "\n\n";



            $sqlite = preg_replace('/0x(\w+)/i', "X'$1'", $sqlite);

            $final_data[$key] = $sqlite;

        }


        return response()->json($final_data);
    }

    public function getDatabaseStructure()
    {
        // Get all the tables in the database
        $tables = DB::select('SHOW TABLES');

        $databaseName = env('DB_DATABASE');
        $tableKey = 'Tables_in_' . $databaseName;

        $structure = [];

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Get the create statement for each table
            $createTable = DB::select("SHOW CREATE TABLE {$tableName}");
            $structure[$tableName] = $createTable[0]->{'Create Table'};
        }

        return $structure;
    }
}
