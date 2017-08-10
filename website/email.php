<?php
session_start();
if (!isset($_SESSION['email'])) {
	
	header('Location: /login.php');
	
}

include 'scripts/connect.php';

$sessionAuctionID = $_GET['id'];

if ($stmt = $mysqli -> prepare("SELECT * FROM auctions WHERE organizationID = ? AND id = ?")) {
	$stmt -> bind_param("ss", $_SESSION['organizationID'], $sessionAuctionID);
	$stmt -> execute();
	$stmt -> store_result();
	
	if ($stmt -> num_rows == 0) {
	
		header('Location: home.php');
	}
	
	$stmt -> close();
}

if (isset($_POST['subject'])) {

	$subject = $_POST['subject'];
	$message = $_POST['message'];

  include_once('scripts/PHPMailer/class.phpmailer.php');
	require_once('scripts/PHPMailer/class.smtp.php');
	
	$mail = new PHPMailer;
	
	$mail -> isSMTP();
	$mail -> Host = $emailHost;
	$mail -> SMTPAuth = true;
	$mail -> Username = $emailUsername;
	$mail -> Password = $emailPassword;
	$mail -> SMTPSecure = 'ssl';
	$mail -> Port = $emailPort;

	$mail -> From = $emailFrom;
	$mail -> FromName = $_SESSION['organization'];
	
//	$mail -> AddAddress('noreply@w1giv.com', 'Attendee');
	$mail -> AddReplyTo($reply, $_SESSION['organization']);
		
	$mail -> isHTML(true);

	$mail -> Subject = $subject;
	
	if ($stmt = $mysqli -> prepare("SELECT name, email FROM attendees WHERE recieveClubEmail = TRUE AND auctionID = ?")) {
		$stmt -> bind_param("i", $sessionAuctionID);
		$stmt -> execute();
		$stmt -> bind_result($name, $email);
		
		while ($stmt -> fetch()) {
			$mail -> ClearAllRecipients();
			
			$mail -> AddAddress($email, $name);
			
			$tmp = str_replace('{NAME}', $name, $message);
			
			$key = "w1givauctions";
			$cryp = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $email, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
			
			$mail -> Body = $tmp."<br><br><hr><font size='1'>You are receiving this email because an auction which you attended used W1GIV Auctions and you opted to recieve emails upon registration.<br>You can <a href='www.w1giv.com/unsubscribe.php?id=$sessionAuctionID&e=$cryp'>unsubscribe</a> from ".$_SESSION['organization']." emails, or you can <a href='www.w1giv.com/unsubscribe.php?e=$cryp'>unsubscribe</a> from all W1GIV auctions emails.</font>";
	
		$mail -> send();
		}
		$stmt -> close();
		
	}


	

	if ($stmt = $mysqli -> prepare("INSERT INTO emails (auctionID, contactID, subject, text, reply) VALUES (?, ?, ?, ?, ?)")) {
		$stmt -> bind_param("iisss", $sessionAuctionID, $_SESSION['id'], $subject, $message, $_POST['reply']);
		$stmt -> execute();
		$stmt -> close();
	}
	
	if ($stmt = $mysqli -> prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
									$action = "sent an email to auction attendees";
									$stmt -> bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
									$stmt -> execute();
									$stmt -> close();
								}	
	
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
		
      <!-- Full Width Column -->
      <div class="content-wrapper">
        <div class="container">
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
            Attendees
              <small>Email</small>
            </h1>
            <ol class="breadcrumb">
              <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
			<li>Email Attendees</li>
            </ol>
          </section>

          <!-- Main content -->
          <section class="content">
        
			  
			<?php if (isset($_GET['msg'])) : ?>
						
						   <div class="box box-default">
              <div class="box-header with-border">

              <h3 class="box-title">View Email</h3>
              </div>
              <div class="box-body">
								
									<?php
									
										if ($stmt = $mysqli -> prepare("SELECT subject, text, reply FROM emails WHERE id = ? AND auctionID = ?")) {
											$stmt -> bind_param("ii", $_GET['msg'], $sessionAuctionID);
											$stmt -> execute();
											$stmt -> bind_result($subject, $text, $reply);
											$stmt -> fetch();
											$stmt -> close();
										}
									?>
                 
									<div class="form-group">
                    <input type="text" class="form-control" name="subject" value="<?php echo $subject; ?>" disabled>
                  </div>
									
									<div class="form-group">
                    <input type="text" class="form-control" name="reply" value="<?php echo $reply; ?>" disabled>
                  </div>
									
                  <div class="form-group">
										<textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" disabled><?php echo $text; ?> </textarea>
                  </div>
               
                  
								
								 </div>
								 <div class="panel-footer"><a class='btn btn-primary' href='email.php?id=<?php echo $sessionAuctionID; ?>'>
									 Back
									</a></div>
						</div>
						
						<?php else : ?>
			  
			   <div class="box box-default">
              <div class="box-header with-border">

              <h3 class="box-title">Send Email</h3>
              </div>
              <div class="box-body">
			
								
								<p>
								 Emails will only be sent to attendees who opted to recieve emails from the organization upon reigstration.
								</p>
	
								<form  method="POST" action ="/email.php?id=<?php echo $sessionAuctionID; ?>">
                 
									<div class="form-group">
                    <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                  </div>
									
									<div class="form-group">
                    <input type="text" class="form-control" name="reply" placeholder="Reply Email Address" required>
                  </div>
									
                  <div class="form-group">
										<textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" required></textarea>
                  </div>
               
                  <div class="pull-right">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
                  </div>
								</form>
            
									<p>
									This page may take time to submit if there are a large amount of recipients. Please do not leave or refresh the page or else the email will not be successful.<br><br><b>{NAME}</b> will insert the attendee's name.
								</p>
								
              </div><!-- /.box-body -->
            </div><!-- /.box -->
						
							  
			   <div class="box box-default">
              <div class="box-header with-border">

              <h3 class="box-title">Email History</h3>
              </div>
              <div class="box-body">

								  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
						 						<th>Contact</th>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Reply Address</th>
												<th>Message</th>
                      </tr>
                    </thead>
                    <tbody>
                     
						<?php

						if ($stmt = $mysqli -> prepare("SELECT emails.id, contacts.firstName, contacts.lastName, emails.time, emails.subject, emails.reply FROM contacts, emails WHERE contacts.id = emails.contactID AND emails.auctionID = ? ORDER BY id DESC")) {
							$stmt -> bind_param("i", $sessionAuctionID);
							$stmt -> execute();
							$stmt -> bind_result($id, $firstName, $lastName, $time, $subject, $reply);
							
							while ($stmt -> fetch() ) {
								echo "<tr><td>$firstName $lastName</td><td>$time</td><td>$subject</td><td>$reply</td><td><a class='btn btn-primary' href='email.php?id=$sessionAuctionID&msg=$id'>View</a></td></tr>";
							}
							
							$stmt -> close();
							
						}
	
						?>
						
                    </tbody>
                  </table>
								
								   </div><!-- /.box-body -->
            </div><!-- /.box -->
								
						<?php endif; ?>
						
          </section><!-- /.content -->
        </div><!-- /.container -->
      </div><!-- /.content-wrapper -->

<?php
$mysqli -> close();
include 'includes/footer.php';
?>
