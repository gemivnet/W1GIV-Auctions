<?php
session_start();

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>W1GIV Auction</title>
		
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
		<link rel="stylesheet" href="../../plugins/iCheck/square/blue.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="register-page">
		<div class="register-box">
			<div class="register-logo">
				<a href="../../index2.html"><b>W1GIV</b> Auction</a>
			</div>

			<div class="register-box-body">


				<h4>Organization Information</h4>
				<form action="../../scripts/register.php" method="post">
					<div class="form-group has-feedback">
						<input type="text" class="form-control" required="true" name="organizationName"
						<?php

						if (isset($_SESSION['Register.organizationName'])) {
							echo "value='" . $_SESSION['Register.organizationName'] . "'";
							unset($_SESSION['Register.organizationName']);
						} else {
							echo "placeholder='Organization Name'";
						}
						?>
						>
						<span class="glyphicon glyphicon-list-alt form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control" required="true" name="organizationAddress"
						
						<?php

						if (isset($_SESSION['Register.organizationAddress'])) {
							echo "value='" . $_SESSION['Register.organizationAddress'] . "'";
							unset($_SESSION['Register.organizationAddress']);
						} else {
							echo "placeholder='Organization Full Address'";
						}
						?>
						
						>
						<span class="glyphicon glyphicon-road form-control-feedback"></span>
					</div>

					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="organizationPhone" 
						
						<?php

						if (isset($_SESSION['Register.organizationPhone'])) {
							echo "value='" . $_SESSION['Register.organizationPhone'] . "'";
							unset($_SESSION['Register.organizationPhone']);
						} else {
							echo "placeholder='Organization Phone Number'";
						}
						?>
						
						>
						<span class="glyphicon glyphicon-earphone form-control-feedback"></span>
					</div>

					<h4>Primary Contact Information</h4>

					<p>
						Please enter the information for the primary account holder. This may be changed at a future time. Other accounts linked to this organization may be after primary registration.
					</p>

					<div class="form-group has-feedback">
						<input type="text" class="form-control" required="true" name="contactFirstName" 
						
							<?php

							if (isset($_SESSION['Register.contactFirstName'])) {
								echo "value='" . $_SESSION['Register.contactFirstName'] . "'";
								unset($_SESSION['Register.contactFirstName']);
							} else {
								echo "placeholder='First Name'";
							}
						?>
						
						>
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control" required="true" name="contactLastName" 
						
						<?php

						if (isset($_SESSION['Register.contactLastName'])) {
							echo "value='" . $_SESSION['Register.contactLastName'] . "'";
							unset($_SESSION['Register.contactLastName']);
						} else {
							echo "placeholder='Last Name'";
						}
						?>
						
						>
					</div>

					<div class="form-group has-feedback">
						<input type="email" class="form-control" required="true" name="contactEmail" 
						
						<?php

						if (isset($_SESSION['Register.contactEmail'])) {
							echo "value='" . $_SESSION['Register.contactEmail'] . "'";
							unset($_SESSION['Register.contactEmail']);
						} else {
							echo "placeholder='Email'";
						}
						?>
						
						>
						<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
					</div>

					<div 
					
					<?php
					
					if (isset($_SESSION['Register.emailError'])) {
						echo "class='form-group has-error'";
						unset($_SESSION['Register.emailError']);
					} else {
						echo "class='form-group has-feedback'";
					}
					
					?>
					
					>
						<input type="email" class="form-control" required="true" name="contactConfirmEmail" 
						
						<?php

						if (isset($_SESSION['Register.contactConfirmEmail'])) {
							echo "value='" . $_SESSION['Register.contactConfirmEmail'] . "'";
							unset($_SESSION['Register.contactConfirmEmail']);
						} else {
							echo "placeholder='Confirm Email'";
						}
						?>
						
						>
					</div>

					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="contactPhone" 
						
						<?php

						if (isset($_SESSION['Register.contactPhone'])) {
							echo "value='" . $_SESSION['Register.contactPhone'] . "'";
							unset($_SESSION['Register.contactPhone']);
						} else {
							echo "placeholder='Phone'";
						}
						?>
						
						>
						<span class="glyphicon glyphicon-earphone form-control-feedback"></span>
					</div>

					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="contactTitle" 
						
						<?php

						if (isset($_SESSION['Register.contactTitle'])) {
							echo "value='" . $_SESSION['Register.contactTitle'] . "'";
							unset($_SESSION['Register.contactTitle']);
						} else {
							echo "placeholder='Title at Organization'";
						}
						?>
						
						>
						<span class="glyphicon glyphicon-star form-control-feedback"></span>
					</div>

					<p>
						A temporary password will be emailed to you upon registration.
					</p>
					
					<!-- TODO: Implement Captcha -->
					
					<div class="g-recaptcha form-group" data-sitekey="6Ld4_AsTAAAAAAEYmU50mCU-3ss2uUkNPLAqFvXc"></div>
				<p>
					By registering you agree that you have authorization from the organization you are signing up on behalf of.
				</p>
					<div class="row">
						<div class="col-xs-8">
							
							<div class="checkbox icheck">
								<label>
									<input type="checkbox" required="true">
									I agree to the <a href="#">terms</a> and the <a href="#">privacy policy</a></label>
							</div>
						</div><!-- /.col -->
						<div class="col-xs-4">
							<button type="submit" class="btn btn-primary btn-block btn-flat">
								Register
							</button>
						</div><!-- /.col -->
					</div>
				</form>
			</div><!-- /.form-box -->
		</div><!-- /.register-box -->

		<!-- jQuery 2.1.4 -->
		<script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
		<!-- Bootstrap 3.3.4 -->
		<script src="../../bootstrap/js/bootstrap.min.js"></script>
		<!-- iCheck -->
		<script src="../../plugins/iCheck/icheck.min.js"></script>
		<script>
			$(function() {
				$('input').iCheck({
					checkboxClass : 'icheckbox_square-blue',
					radioClass : 'iradio_square-blue',
					increaseArea : '20%' // optional
				});
			});
		</script>
	</body>
</html>
