<?php
    include_once "../config.php";
    session_start();
    function isLogin($page){
        if(!isset($_SESSION['valid'])){
            header("location: $page");
            exit(0);
        }
        
    }
    
    function checkLogin($username,$password){
        global $conn;
        $sql = "SELECT * FROM tbuser WHERE username ='$username' OR email='$username' AND password=md5('".$password."') AND active ='1' AND isadmin='1'"; 
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        echo $stmt->rowCount();
        if($stmt->rowCount() > 0)
        {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $result['userid'];
            date_default_timezone_set('Asia/Phnom_Penh');
            $time = date('Y-m-d H:i:s');
            $sql = "UPDATE tbuser SET lastlogin ='$time' WHERE userid =$id ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $_SESSION['valid'] = true;
            return true;
        }
        return false;
    }
    function checkSignup($username, $password,$fullname,$email){
        global $conn;
        date_default_timezone_set('Asia/Phnom_Penh');
        $time = date('Y-m-d H:i:s');
        $sql = "INSERT INTO tbuser(username,password,fullname,email,isadmin,active,lastlogin) VALUES ('$username',md5('".$password."'),'$fullname','$email','0','1','$time')"; 
        $stmt = $conn->prepare($sql);
        // $stmt->execute();
        
        if($stmt->execute())
        {
            $_SESSION['valid'] = true;
            return true;
        }
        return false;
    }
?>