<?php
session_start();
if (!isset($_SESSION['email'])) {
	
	header('Location: /login.php');
	
}

include 'scripts/connect.php';

if (isset($_POST['firstName'])) {
    
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$title = $_POST['title'];
	
        include_once('scripts/PHPMailer/class.phpmailer.php');
	require_once('scripts/PHPMailer/class.smtp.php');
	
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
	$mail -> addAddress($email, $firstName." ".$lastName);


	$mail -> isHTML(true);
        
        if ($stmt = $mysqli -> prepare ("SELECT COUNT(*), password FROM contacts WHERE email =?")) {
            $stmt -> bind_param("s", $email);
            $stmt -> execute();
            $stmt -> bind_result($amt, $pword);
            $stmt -> fetch();
            $stmt -> close();
        }
        
        if ($amt == 0) {
            
            $chars = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
            $pass = substr( str_shuffle( $chars ), 0, 8);
            $encryptPass = crypt($pass, '$6$rounds=25000$' . md5(uniqid(rand(), true)));

            if ($stmt = $mysqli -> prepare("INSERT INTO contacts (organizationID, firstName, lastName, email, phone, title, password) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                    $stmt -> bind_param("issssss", $_SESSION['organizationID'], $firstName, $lastName, $email, $phone, $title, $encryptPass);
                    $stmt -> execute();
                    $stmt -> close();
            }


            $mail -> Subject = $_SESSION['organization'].' has created you a W1GIV Auctions account.';
            $mail -> Body = $firstName.", <br><br>You have been created a W1GIV Auctions account by your organization. Please <a href='https://w1giv.com/login.php'>Click Here</a> to log into your account. Upon logging in you will be requested to change your password. <br><br> Your temp password is <b>".$pass."</b><br><br>Thank you!";
            
        } else {
            
            if ($stmt = $mysqli -> prepare("INSERT INTO contacts (organizationID, firstName, lastName, email, phone, title, password) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                    $stmt -> bind_param("issssss", $_SESSION['organizationID'], $firstName, $lastName, $email, $phone, $title, $pword);
                    $stmt -> execute();
                    $stmt -> close();
            }
            
            $mail -> Subject = $_SESSION['organization'].' has added you to their W1GIV Auctions organization.';
            $mail -> Body = $firstName.", <br><br>Your W1GIV Auctions account has been added to ".$_SESSION['organization'].".<br><br>Since you already have an account you may log in as normal and choose the organization you wish to manage.</b><br><br>Thank you!";
            
        }
        
	$mail -> send();
	
	$success = "Added <strong>$firstName $lastName</strong> to ".$_SESSION['organization'];
	
}

include 'includes/nodashHeader.php';
?>
		
      <!-- Full Width Column -->
      <div class="content-wrapper">
        <div class="container">
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
              Home
              <small>Manage Auctions, Payment, and Organization</small>
            </h1>
            <ol class="breadcrumb">
              <li><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
			  <li class="active" ><a href="/organization.php">Organization</a></li>
            </ol>
          </section>

          <!-- Main content -->
          <section class="content">
			  
			  <div class="row">
				  
				  <div class="col-md-6">
					  
					   <div class="box box-default">
				<div class="box-header with-border">
                <h3 class="box-title">Organization Contacts</h3>
              </div>
              <div class="box-body">
				
					<p>
						Add a new contact to <?php echo $_SESSION['organization']; ?>
				  </p>
				  
				  <?php if(isset($success)) {echo " <div class='callout callout-success'>$success</div>";} ?>
				  
				  <form  method="POST" action ="organization.php">
								   
							<div class="form-horizontal">
								
							 <div class="form-group">
                     			<label for="firstName" class="col-sm-3 control-label">First Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
                     			 </div>
                   			 </div>

							 <div class="form-group">
                     			<label for="lasstName" class="col-sm-3 control-label">Last Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Address" required>
                     			 </div>
                   			 </div>
									
							 <div class="form-group">
                     			<label for="email" class="col-sm-3 control-label">Email</label>
                      			<div class="col-sm-8">
                       				 <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                     			 </div>
                   			 </div>
										 
							 <div class="form-group">
                     			<label for="phone" class="col-sm-3 control-label">Phone</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                     			 </div>
                   			 </div>
								
								<div class="form-group">
                     			<label for="title" class="col-sm-3 control-label">Title</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="title" name="title" placeholder="Title" required>
                     			 </div>
                   			 </div>
								
								<p>
									A password will be emailed to the new contact. They will be requested to change their password upon login.
								</p>

				
								</div>
	  				 <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">Add Contact</button>
                  </div><!-- /.box-footer -->
							</form>
				  
				
              </div><!-- /.box-body -->
            </div><!-- /.box -->

					  
				  </div>
				  
			  </div>
			  
			    <div class="box box-default">
				<div class="box-header with-border">
                <h3 class="box-title">Organization Contacts</h3>
              </div>
              <div class="box-body">
				
					
				    <table id="organizationTable" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Title</th>
				
					
                      </tr>
                    </thead>
                    <tbody>
                     
						<?php

						if ($stmt = $mysqli -> prepare("SELECT id, firstName, lastName, email, phone, title FROM contacts WHERE organizationID = ?")) {
							$stmt -> bind_param("i", $_SESSION['organizationID']);
							$stmt -> execute();
							$stmt -> bind_result($id, $firstName, $lastName, $email, $phone, $title);
							
							while ($stmt -> fetch() ) {
								
								echo "<tr><td>$firstName $lastName</td><td>$email</td><td>$phone</td><td>$title</td>";
								
							
								echo "</tr>";
								
							}
							
							$stmt -> close();
							
						}
	
						?>
						
                    </tbody>
                  </table>
				  
				  
				
								</div>

              </div><!-- /.box-body -->
            </div><!-- /.box -->
          
          </section><!-- /.content -->
        </div><!-- /.container -->
      </div><!-- /.content-wrapper -->

<?php
$mysqli -> close();
include 'includes/footer.php';
?>
