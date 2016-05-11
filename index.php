<?php
echo "<pre>";
header('Content-type: charset=utf-8');
function connect ($database) {
    $connection = mysqli_connect('localhost', 'root', '', $database) or die('Could not connect: ' . mysqli_error($connection));
    mysqli_select_db($connection, $database) or die('Could not select database');
    return $connection;
}

function close ($connection) {
    mysqli_close($connection);
}

function query ($sql, $connection) {
    $result = mysqli_query($connection, $sql) or die('Query failed: ' . mysqli_error($connection));
    if ($result === TRUE) {
        return $result;
    }
    if ($result === FALSE) {
        return [];
    }
    $data = [];
    while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $data[] = $line;
    }
    mysqli_free_result($result);
    return $data;
}
function select ($connection, $table) {
    $sql = "SELECT * FROM $table";
    $data = query($sql, $connection);
    return $data;
}
function recursionDB ($data, $id) {
    $result = [];
    foreach ($data as $key => $employee) {
        if ($employee['parent_id'] == $id) {
            $result[] = $employee;
        }
    }
    foreach ($result as $key => $subordinate) {
        $result[$key]['sub'] = recursionDB($data,$subordinate['id_employee']);
    }
    return $result;
}

$connection = connect('company');
mysqli_set_charset($connection, "cp1251");
$table = 'employee';
$data = select($connection, $table);
$result = recursionDB($data, 0);
print_r($result);