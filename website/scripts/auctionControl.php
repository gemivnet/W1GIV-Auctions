<?php

session_start();
if (!isset($_SESSION['email'])) {
	
	header('Location: /login.php');
	
}

include 'connect.php';

$sessionAuctionID = $_GET['id'];

if ($stmt = $mysqli -> prepare("SELECT * FROM auctions WHERE organizationID = ? AND id = ?")) {
	$stmt -> bind_param("ss", $_SESSION['organizationID'], $sessionAuctionID);
	$stmt -> execute();
	$stmt -> store_result();
	
	
	if ($stmt -> num_rows == 0) {
	
		header('Location: ../../home.php');
	}
	
	$stmt -> close();
}

// 0 = Mark as Pending
// 1 = Mark as In Progress
// 2 = Mark as Completed
// 3 = Edit Attendee

switch ($_GET['action']) {
	case 0:
		if ($stmt = $mysqli -> prepare("UPDATE auctions SET status='Pending' WHERE id=?")) {
			$stmt -> bind_param("i", $sessionAuctionID);
			$stmt -> execute();
			$stmt -> close();
		}
		if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
			$action = "set state of auction to pending.";
			$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
			$stmt -> execute();
			$stmt -> close();
		}	
		header('Location: ../../manage.php?id='.$sessionAuctionID);
		break;
	case 1:
		if ($stmt = $mysqli -> prepare("UPDATE auctions SET status='In Progress' WHERE id=?")) {
			$stmt -> bind_param("i", $sessionAuctionID);
			$stmt -> execute();
			$stmt -> close();
		}
		if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
			$action = "set state of auction to in progress.";
			$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
			$stmt -> execute();
			$stmt -> close();
		}	
		header('Location: ../../manage.php?id='.$sessionAuctionID);
		break;
	case 2:
		if ($stmt = $mysqli -> prepare("UPDATE auctions SET status='Completed' WHERE id=?")) {
			$stmt -> bind_param("i", $sessionAuctionID);
			$stmt -> execute();
			$stmt -> close();
			
		}
		if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
			$action = "set state of auction to completed.";
			$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
			$stmt -> execute();
			$stmt -> close();
		}	
		header('Location: ../../manage.php?id='.$sessionAuctionID);
		break;
	case 3:
		$name = $_POST['name'];
		$address = $_POST['address'];
		$phone = $_POST['phone'];
		$additional = $_POST['additional'];
		$emailAddress = $_POST['emailAddress'];
		$email = $_POST['email'];
		$reciept = $_POST['reciept'];

		if ($email == "on") {
			$email = 1;
		} else {
			$email = 0;
		}
								
		if ($reciept == "on") {
			$reciept = 1;
		} else {
			$reciept = 0;
		}

		if ($stmt = $mysqli -> prepare("UPDATE attendees SET name=?, address=?, phone=?, email=?, recieveClubEmail=?, recieveRecieptEmail=?, additional=? WHERE id=?")) {
			$stmt -> bind_param("ssssiisi", $name, $address, $phone, $emailAddress, $email, $reciept, $additional, $_GET['attendee']);
			$stmt -> execute();
			$stmt -> close();
		}
	
		if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
			$action = "edited attendee $name.";
			$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
			$stmt -> execute();
			$stmt -> close();
		}	
	
		header('Location: ../../attendees.php?id='.$sessionAuctionID);	
	
		break;
	case 4:
		$buyer = $_POST['buyer'];
		$seller = $_POST['seller'];
		$description = $_POST['description'];
		$price = $_POST['price'];
	
		if ($stmt = $mysqli -> prepare("UPDATE items SET buyer=?, seller=?, description=?, price=? WHERE id=?")) {
			$stmt -> bind_param("iisdi", $buyer, $seller, $description, $price, $_GET['item']);
			$stmt -> execute();
			$stmt -> close();
		}
	
		if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
			$action = "edited item $description.";
			$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
			$stmt -> execute();
			$stmt -> close();
		}	
		header('Location: ../../items.php?id='.$sessionAuctionID);	
	
		break;
}	

$mysqli->close();

?>