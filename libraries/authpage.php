<?php
    include_once "config.php";
    session_start();
    function isLogin($page){
        if(!isset($_SESSION['valid'])){
            header("location: $page");
            exit(0);
        }
        
    }
    
?>