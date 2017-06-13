<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <title>W1GIV Auctions</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
  
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
        
                <a href="/"><b>W1GIV</b> Auctions</a>
					</div>
                   
                        <div class="login-box-body">
                          
													<?php

											$email = $_GET['e'];

											$key = "w1givauctions";

											$email = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($email), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

											include 'scripts/connect.php';
													
													if (isset($_GET['id'])) {
													
															
														if ($stmt = $mysqli -> prepare("UPDATE attendees SET recieveClubEmail=0 AND recieveRecieptEmail=0 WHERE auctionID = ? AND email = ?")) {
															$stmt -> bind_param("is", $_GET['id'], $email);
															$stmt -> execute();
															$stmt -> close();
														}
														
														if ($stmt = $mysqli -> prepare("SELECT organizations.name FROM organizations, auctions WHERE auctions.id = ? AND auctions.organizationID = organizations.ID")) {
															$stmt -> bind_param("i", $_GET['id']);
															$stmt -> execute();
															$stmt -> bind_result($organ);
															$stmt -> fetch();
															$stmt -> close();
														}
														
														echo "<p>Your email address, $email, has been removed from the mailing list of $organ.</p>";
														
													} else {
														
															if ($stmt = $mysqli -> prepare("UPDATE attendees SET recieveClubEmail=0 AND recieveRecieptEmail=0 WHERE email = ?")) {
															$stmt -> bind_param("s", $email);
															$stmt -> execute();
															$stmt -> close();
														}
														
														echo "<p>Your email address, $email, has been removed removed from all W1GIV Auctions and organization mailing lists.</p>";
														
													}
													
													$mysqli->close();
													
											?>
													
                        </div>
	
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="../../plugins/iCheck/icheck.min.js"></script>

  </body>
</html>