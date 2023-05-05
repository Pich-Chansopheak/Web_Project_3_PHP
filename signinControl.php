<?php
    session_start();
    include "config.php";
    $error = 0;
    if(isset($_POST['sign-in'])){

        $user = $_POST['username'];
        $pwd = $_POST['password'];

        try{

            $sql = "SELECT * FROM tbuser WHERE (username ='$user' OR email='$user') AND password=md5('".$pwd."') AND active ='1'"; 
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    
            if($stmt->rowCount() < 1){
                echo "Not found any username";
                exit(0);
            }else{
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['name'] = $result['username'];    
                $_SESSION['valid'] = true;
                header ("location: index.php");
            }
        }catch(PDOException $err){
            echo $err;
        }
    }
