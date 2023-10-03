<?php
include('../database/db.php');

$tables = array();
$sql = "SHOW TABLES";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

// echo json_encode($tables);

$sqlScript = "";
foreach ($tables as $table) {
    $tableName = $table['Tables_in_gorder'];
    $query = "SHOW CREATE TABLE `$tableName`";
    $result = $conn->query($query);
    $row = $result->fetch_row();

    $sqlScript .= "\n\n" . $row[1] . ";\n\n";

    $query = "SELECT * FROM `$tableName`";
    $result = $conn->query($query);

    $columnCount = $result->num_rows;
    // echo $table['Tables_in_gorder'] . ' ' . $columnCount;
    // echo '<br>';

    for ($i = 0; $i < $columnCount; $i++) {
        while ($row = $result->fetch_row()) {
            $sqlScript .= "INSERT INTO `$tableName` VALUES(";
            for ($j = 0; $j < $columnCount; $j++) {
                // $row[$j] = $row[$j];

                if (isset($row[$j])) {
                    $sqlScript .= '"' . $row[$j] . '"';
                } else {
                    $sqlScript .= '""';
                }

                if ($j < ($columnCount - 1)) {
                    $sqlScript .= ',';
                }
            }
            $sqlScript .= ");\n";
        }
    }
    $sqlScript .= "\n";
}

if (!empty($sqlScript)) {
    $back_up_file_name = $dbname . "_backup_" . time() . ".sql";
    $fileHandler = fopen($back_up_file_name, 'w+');
    $numberOfLines = fwrite($fileHandler, $sqlScript);
    fclose($fileHandler);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($back_up_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cashe-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($back_up_file_name));
    ob_clean();
    flush();
    readfile($back_up_file_name);
    // exec('rm ' . $back_up_file_name);
}
