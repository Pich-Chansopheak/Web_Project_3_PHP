<?php
include "config.php";
session_start();
    if(isset($_POST['sign-up'])){

        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $pwd = $_POST['password'];

        try{
            date_default_timezone_set('Asia/Phnom_Penh');
            $time = date('Y-m-d H:i:s');
            $sql = "INSERT INTO tbuser(username,password,fullname,email,isadmin,active,lastlogin) VALUES ('$username',md5('".$password."'),'$fullname','$email','0','1','$time')";
            $stmt = $conn->prepare($sql);
            
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_BOTH);
            if($stmt->rowCount() <1){
                echo "Cannot Signup the account";
                exit();
            }
            $_SESSION['valid'] = true;
            $_SESSION['name'] = $username;
            header ("location: index.php");

        }catch(PDOException $err){
            echo $err;
        }
    }
