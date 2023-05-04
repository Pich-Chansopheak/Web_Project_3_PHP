<?php
    session_start();
    include "config.php";

    if(isset($_POST['sign-in'])){

        $user = $_POST['username'];
        $pwd = $_POST['password'];

        try{

            $sql = "SELECT * FROM tbuser WHERE username = :username OR email = :username AND password = :pwd";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $user);
            $stmt->bindParam(':pwd', $pwd);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_BOTH);

            $_SESSION['name'] = $data['username'];

            if(count($data) <1){
                echo "Not found any username";
                exit();
            }
   
            header ("location: index.php");

        }catch(PDOException $err){
            echo $err;
        }
    }
