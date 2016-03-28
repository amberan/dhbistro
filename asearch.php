<?php
define('DB_SERVER', 'localhost');
define('DB_USER', $dbusr);
define('DB_PASSWORD', $password);
//define('DB_NAME', 'demo');


if (isset($_GET['term'])){
    $return_arr = array();

    try {
        $conn = new PDO("mysql:host=".DB_SERVER.DB_USER, DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare('SELECT surname FROM nw_persons WHERE surname LIKE %:term%');
        
        $stmt->execute(array('term' => '%'.$_GET['term'].'%'));
        
        while($row = $stmt->fetch()) {
            $return_arr[] =  $row['surname'];
        }

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }


    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}

?>