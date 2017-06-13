<?php

session_start();

$organizationName = $_POST['organizationName'];
$organizationAddress = $_POST['organizationAddress'];
$organizationPhone = $_POST['organizationPhone'];
$contactFirstName = $_POST['contactFirstName'];
$contactLastName = $_POST['contactLastName'];
$contactEmail = $_POST['contactEmail'];
$contactConfirmEmail = $_POST['contactConfirmEmail'];
$contactPhone = $_POST['contactPhone'];
$contactTitle = $_POST['contactTitle'];

if ($contactEmail != $contactConfirmEmail) {

	$_SESSION['Register.organizationName'] = $organizationName;
	$_SESSION['Register.organizationAddress'] = $organizationAddress;
	$_SESSION['Register.organizationPhone'] = $organizationPhone;
	$_SESSION['Register.contactFirstName'] = $contactFirstName;
	$_SESSION['Register.contactLastName'] = $contactLastName;
	$_SESSION['Register.contactEmail'] = $contactEmail;
	$_SESSION['Register.contactConfirmEmail'] = $contactConfirmEmail;
	$_SESSION['Register.contactPhone'] = $contactPhone;
	$_SESSION['Register.contactTitle'] = $contactTitle;

	$_SESSION['Register.emailError'] = "true";

	header("Location: /register");
} else {

	include 'connect.php';

	include_once ('PHPMailer/class.phpmailer.php');
	require_once ('PHPMailer/class.smtp.php');

	$mail = new PHPMailer;

	$mail -> isSMTP();
	$mail -> Host = 'smtp.zoho.com';
	$mail -> SMTPAuth = true;
	$mail -> Username = 'noreply@w1giv.com';
	$mail -> Password = 'card2G5pQ';
	$mail -> SMTPSecure = 'ssl';
	$mail -> Port = 465;

	$mail -> From = 'noreply@w1giv.com';
	$mail -> FromName = 'W1GIV Auctions';
	$mail -> addAddress($contactEmail, $contactFirstName." ".$contactLastName);

	$mail -> isHTML(true);

	$mail -> Subject = 'Here is the subject';
	$mail -> Body = 'test';
	$mail -> AltBody = 'This is the body in plain text for non-HTML mail clients';

}
?>