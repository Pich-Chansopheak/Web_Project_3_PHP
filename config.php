<?php
    // define("HOST","localhost");
    // define("USER","root");
    // define("PASS","");
    // define("DB","php_ecommerce");

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "php_ecommerce";

    try{
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){
        echo "Error: ".$e->getMessage();
    }
?>