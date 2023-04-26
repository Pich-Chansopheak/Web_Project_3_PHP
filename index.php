<?php
	include "config.php";

	$page = "home.php";
	$slider = true;
	$slidebar = true;
	if(isset($_GET['p']))
	{
		$p= $_GET['p'];
		switch($p)
		{
			case "shop":
				$page = "shop.php";
				$slider = false;
				$slidebar = false;
				break;
			case "cart":
				$page = "cart.php";
				$slider = false;
				$slidebar = false;
				break;
			case "blog":
				$page = "blog.php";
				$slider = false;
				$slidebar = false;
				break;
			case "about":
				$page = "about.php";
				$slider = false;
				$slidebar = false;
				break;
			case "contact":
				$page = "contact.php";
				$slider = false;
				$slidebar = false;
				break;
			// case "signin":
			// 		$page = "signin.php";
			// 		$slider = false;
			// 		$slidebar = false;
			// 		break;
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "includes/head.php"?>
<body class="animsition">
	
<?php include "includes/header.php" ?>
<?php include "includes/cart.php" ?>	

<?php if($slider) include "includes/slider.php" ?>
	<!-- Banner -->
	<div class="sec-banner bg0 p-t-80 p-b-50">
		<div class="container">
        <?php if($slidebar) include "includes/slidebar.php" ?>
        </div>
	</div>


<?php include $page ?>
<?php include "includes/footer.php" ?>
</body>
</html>

<?php
	$conn = null;
?>