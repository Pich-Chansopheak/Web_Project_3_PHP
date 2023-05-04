<?php
	session_start();
    include "config.php";
	include "includes/head.php";

	try{
		if(isset($_POST['pro_id'])){
			$pro_id = $_POST['pro_id'];
			$pro_name = $_POST['pro_name'];
			$pro_price = $_POST['pro_price'];
			$pro_img = $_POST['pro_img'];
			$qty = 1;
			$total_price = $pro_price * $qty;
		

				$stmt = $conn->prepare("SELECT pro_id FROM cart WHERE pro_id=? ");
				$stmt->bindParam(1, $pro_id, PDO::PARAM_INT);
				$stmt->execute();
				// $stmt->setFetchMode(PDO::FETCH_ASSOC);
				// $result = $stmt->fetchAll();
		
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				// pro_id
				$code = $result["pro_id"];

				echo `<div class="alert alert-success message" role="alert">`.
					$code. 
				`</div>`;

				// print_r($code);

				if(!$code){
					$query = $conn->prepare("INSERT INTO cart (pro_name, pro_price, pro_img, qty, pro_id, total_price)
												VALUES (?,?,?,?,?,?)");
					$query->bindParam(1, $pro_name, PDO::PARAM_STR);
					$query->bindParam(2, $pro_price, PDO::PARAM_STR);
					$query->bindParam(3, $pro_img, PDO::PARAM_STR);
					$query->bindParam(4, $qty, PDO::PARAM_INT);
					$query->bindParam(5, $pro_id, PDO::PARAM_INT);
					$query->bindParam(6, $total_price, PDO::PARAM_STR);
					$query->execute();
					echo `<div class="alert alert-success" role="alert">
							Item added to your cart!
				    		</div>`;
				}else{
					echo `<div class="alert alert-danger" role="alert">
							Item already added to your cart!
				    		</div>`;
				}
			
		}

		if(isset($_GET['cartItem']) && isset($_GET['cartItem']) == 'cart-item'){
			$stmt = $conn->prepare("SELECT * FROM cart");
			$stmt->execute();
			// $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$stmt->fetchAll(PDO::FETCH_OBJ);
			$rows = $stmt->rowCount();

			echo $rows;
		}

		if(isset($_GET['remove'])){
			$id = $_GET['remove'];

			$stmt = $conn->prepare("DELETE FROM cart WHERE id=?");
			$stmt->bindParam(1, $id, PDO::PARAM_INT);
			$stmt->execute();

			$_SESSION['showAlert'] = 'block';
			$_SESSION['message'] = 'Item removed from the cart!';
			header('location: index.php?p=cart');
		}

		if(isset($_POST['qty'])){
			$qty = $_POST['qty'];
			$pid = $_POST['pid'];
			$pprice = $_POST['pprice'];
			$tprice = $_POST['tprice'];
			
			// $tprice = $qty * $pprice;

			echo `<div id="msg">`.
					$tprice.
				`</div>`;
			
			$stmt = $conn->prepare("UPDATE cart SET qty=?, total_price=? WHERE id=?");
			$stmt->bindParam(1, $qty, PDO::PARAM_INT);
			$stmt->bindParam(2, $tprice, PDO::PARAM_STR);
			$stmt->bindParam(3, $pid, PDO::PARAM_INT);
			$stmt->execute();
			
		}

		// if(isset($_POST['pqty'])){
		// 	$pqty = $_POST['pqty'];
		// 	$ppid = $_POST['ppid'];
		// 	$pprice = $_POST['ppprice'];
		// 	// $ttprice = $_POST['ttprice'];
			
		// 	$ttprice = $pqty * $pprice;

		// 	echo `<div id="msg">`.
		// 			$ttprice.
		// 		`</div>`;

		// 	$stmt = $conn->prepare("UPDATE cart SET qty=?, total_price=? WHERE id=?");
		// 	$stmt->bindParam(1, $pqty, PDO::PARAM_INT);
		// 	$stmt->bindParam(2, $ttprice, PDO::PARAM_STR);
		// 	$stmt->bindParam(3, $ppid, PDO::PARAM_INT);
		// 	$stmt->execute();
			
		// }

	}catch(PDOException $e){
		echo "Error ". $e->getMessage();
	}
$conn = null;
?>