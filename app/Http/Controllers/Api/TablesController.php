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


        return response()->json($mysqlInput);
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
            $createStatement = $createTable[0]->{'Create Table'};

            // Parse the create statement to extract columns information
            preg_match_all('/`(\w+)`\s+(\w+\(.*?\)|\w+)(\s+NOT NULL|\s+NULL)?/', $createStatement, $matches, PREG_SET_ORDER);

            $columns = [];
            foreach ($matches as $match) {
                $columnName = $match[1];
                $columnType = $match[2];
                $isNullable = isset($match[3]) && trim($match[3]) === 'NOT NULL' ? 'NOT NULL' : 'NULL';

                // Extract options for all types
                if (preg_match('/^(\w+)\((.*)\)$/', $columnType, $typeMatch)) {
                    $type = $typeMatch[1];
                    $options = $typeMatch[2];
                    $columns[$columnName] = [
                        'type' => $type,
                        'options' => $options,
                        'nullable' => $isNullable,
                    ];
                } else {
                    $columns[$columnName] = [
                        'type' => $columnType,
                        'nullable' => $isNullable,
                    ];
                }
            }

            $structure[$tableName] = $columns;
        }

        return ($structure);
    }

}
