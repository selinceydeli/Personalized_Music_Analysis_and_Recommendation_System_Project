<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use mysqli;

class MysqlController extends Controller
{
    public function migrateMysql(Request $request)
    {

        $hostDB = $request->input('hostDB');
        $hostUser = $request->input('hostUser');
        $hostPort = $request->input('hostPort');
        $hostIP = $request->input('hostIP');
        $hostPass = $request->input('hostPass');
        if (empty($hostDB) || empty($hostUser) || empty($hostPort) || empty($hostIP) || empty($hostPass)){
            return redirect('/add')->with('message', 'Please fill all the information');
        }
        $result = $this->migrateSongs($hostIP,$hostUser,$hostPass,$hostDB,$hostPort);
        if ($result != false){
            return redirect('/add')->with('message', 'Database is migrated to the destination');
        }
        else{
            return redirect('/add')->with('message', 'There is an error');
        }
        
    }
    private function migrateSongs(string $hostTarget,string $userTarget,string $passTarget,string $dbTarget,string $portTarget){
        $hostSource = "176.217.94.165";
        $userSource = "anon";
        $passSource = "default";
        $dbSource = "music_tailor";
        $portSource = "8000";
        try{
        $connSource = new mysqli($hostSource, $userSource, $passSource, $dbSource,$portSource);
        }
        catch(Exception $e){
            return false;
        }
        try{
        $connTarget = new mysqli($hostTarget, $userTarget, $passTarget, $dbTarget,$portTarget);
        }
        catch(Exception $e){
            return false;
        }
        $tableArray = array("performers","albums","songs");
        foreach ($tableArray as $table){
            $query = "SHOW CREATE TABLE $table";
            $result = $connSource->query($query);
            $row = $result->fetch_assoc();
            $createTableStatement = $row['Create Table'];
    
            $createTableStatement = str_replace("CREATE TABLE ", "CREATE TABLE IF NOT EXISTS ", $createTableStatement);
            $connTarget->query($createTableStatement);
            $showColumnsQuery = "SHOW COLUMNS FROM $table";
            $columnsResult = $connTarget->query($showColumnsQuery);
            $columns = array();
            while ($column = $columnsResult->fetch_assoc()) {
                $columns[] = $column['Field'];
            }
            $insertQuery = "INSERT INTO  $table (" . implode(", ", $columns) . ") VALUES\n";
            $selectQuery = "SELECT * FROM $table";
    
            $result = $connSource->query($selectQuery);
    
    
            while ($row = $result->fetch_assoc()) {
                $rowNew = array();
                foreach ($row as $key => &$val) {
                    if ($val == '')
                    {
                        $val = date('Y-m-d H:i:s');
                    }
                    $val = addslashes($val);
                    $newKey = '`'.$key.'`';
                    $rowNew[$newKey]=$val;
                    unset($row);
                }
                $keys = implode(", ", array_keys($rowNew));
                $values = implode("', '", array_values($rowNew));
                
                $albumInsertQuery = "INSERT INTO $table ($keys) VALUES ('$values')";
                try{
                $connTarget->query($albumInsertQuery);}
                catch(Exception $e){
                    $connSource->close();
                    $connTarget->close();
                    return false;
                }
    
            }
        }
        $connSource->close();
        $connTarget->close();
        return true;
    }

}
