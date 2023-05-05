<?php
  include "../config.php";
  include "../libraries/img.php";
  include "../libraries/auth.php";
  
  isLogin('login.php');
  define("MAXPERPAGE",3);
  $page ="slideshow.php";
  $pagination =true;
  if(isset($_GET['p'])){
    $p=$_GET['p'];
    switch($p){
      
      case "product":
            $page ="product.php";
            $pagination =true;
            break;
      case "category":
            $page ="category.php";
            $pagination =true;
            break;
      case "user":
            $page ="user.php";
            $pagination =true;
            break;    
      case "login":
            $page ="login.php";
            break;

    }
  }
  
?>
<!DOCTYPE html>
<html lang="en">
  <?php include "includes/head.php" ?>
<body>
<div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include "includes/sidebar.php" ?>
        <!-- / Menu -->
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
         
          <!-- / Navbar -->
          <div class="content-wrapper">
            <!-- Content wrapper -->
              <?php include $page?>
              <?php if($pagination) include "includes/pagination.php";?>
              <?php include "includes/footer.php" ?>
              <div class="content-backdrop fade"></div>
          </div><!-- /Content wrapper -->
        </div>
        <!-- / Layout page -->
        
      </div>
      
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
</div>
    <!-- / Layout wrapper -->

<!-- Layout wrapper -->
<?php include "includes/foot.php" ?>
<?php $conn = null;?>
