<?php
    session_start();
    include "config.php";
    $error = 0;
    if(isset($_POST['sign-in'])){

        $user = $_POST['username'];
        $pwd = $_POST['password'];

        try{

            $sql = "SELECT * FROM tbuser WHERE (username ='$user' OR email='$user') AND password=md5('".$pwd."') AND active ='1' AND isadmin='1'"; 
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_BOTH);

            $_SESSION['name'] = $data['username'];
            $_SESSION['valid'] = true;

            if(count($data) <1){
                echo "Not found any username";
                exit();
            }
   
            header ("location: index.php");

        }catch(PDOException $err){
            echo $err;
        }
    }
