<?php
include "config.php";

    if(isset($_POST['sign-up'])){

        $user = $_POST['username'];
        $email = $_POST['email'];
        $pwd = $_POST['password'];

        try{

            $sql ="INSERT INTO signup (user_name, email, password) VALUES (:username, :email, :pwd)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $user);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':pwd', $pwd);
        
            $data = $stmt->execute();
      
            if(count($data) <1){
                echo "Cannot Signup the account";
                exit();
            }

            header ("location: index.php");

        }catch(PDOException $err){
            echo $err;
        }
    }
