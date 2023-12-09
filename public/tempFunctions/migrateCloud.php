<?php
function migrateSongs(string $hostTarget,string $userTarget,string $passTarget,string $dbTarget,string $portTarget){
    $hostSource = "46.2.65.203";
    $userSource = "anon";
    $passSource = "default";
    $dbSource = "music_tailor";
    $portSource = "8000";

    $connSource = new mysqli($hostSource, $userSource, $passSource, $dbSource,$portSource);

    $connTarget = new mysqli($hostTarget, $userTarget, $passTarget, $dbTarget,$portTarget);
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
            catch(mysqli_sql_exception $e){
            }

        }
    }
    $connSource->close();
    $connTarget->close();
    printf("Success\n");
}
?>