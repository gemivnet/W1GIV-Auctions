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


include 'includes/header.php';
include 'includes/sidebar.php';
?>
		
      <!-- Full Width Column -->
      <div class="content-wrapper">
        <div class="container">
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
              Edit
              
            </h1>
            <ol class="breadcrumb">
              <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
			<li>Edit</li>
            </ol>
          </section>

          <!-- Main content -->
          <section class="content">
         
			  <?php if (isset($_GET['attendee'])) : ?>

			  <div class="box box-primary">
				  
				<div class="box-header with-border">
							  <h3 class="box-title">Edit Attendee</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					
					<?php

						if ($stmt = $mysqli->prepare("SELECT name, address, phone, email, recieveClubEmail, recieveRecieptEmail, additional FROM attendees WHERE id = ?")) {
							$stmt->bind_param("i", $_GET['attendee']);
							$stmt->execute();
							$stmt->bind_result($name, $address, $phone, $email, $recieveClubEmail, $recieveRecieptEmail, $additionalValue);
							$stmt->fetch();
							$stmt->close();
						}

					?>
					
						<form  method="POST" action ="scripts/auctionControl.php?id=<?php echo $sessionAuctionID; ?>&action=3&attendee=<?php echo $_GET['attendee'] ?>">
								   
							<div class="form-horizontal">
								
							 <div class="form-group">
                     			<label for="name" class="col-sm-3 control-label">Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                     			 </div>
                   			 </div>

							 <div class="form-group">
                     			<label for="address" class="col-sm-3 control-label">Full Address</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>">
                     			 </div>
                   			 </div>
									
							 <div class="form-group">
                     			<label for="phone" class="col-sm-3 control-label">Phone</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                     			 </div>
                   			 </div>
										 
							 <div class="form-group">
                     			<label for="email" class="col-sm-3 control-label">Email</label>
                      			<div class="col-sm-8">
                       				 <input type="email" class="form-control" id="email" name="emailAddress" value="<?php echo $email; ?>">
                     			 </div>
                   			 </div>
	 
									
							<?php

							if ($stmt = $mysqli -> prepare("SELECT additional FROM commissionSettings WHERE auctionID = ?")) {
								$stmt -> bind_param("i", $sessionAuctionID);
								$stmt -> execute();
								$stmt -> bind_result($additional);
								$stmt -> fetch();
								$stmt -> close();
							}
		
							if ($additional != "") : ?>
										 			 
							<div class="form-group">
                     			<label for="additional" class="col-sm-3 control-label"><?php echo $additional; ?></label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="additional" name="additional" value="<?php echo $additionalValue; ?>">
                     			 </div>
                   			 </div>
										 
							<?php endif; ?>
			
                      <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="email" class="flat-red" <?php if($recieveClubEmail == 1) { echo "checked"; } ?>> Allow organization to send me emails
                          </label>
                        </div>
                      </div>
							
										  
					<div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="reciept" class="flat-red" <?php if($recieveRecieptEmail == 1) { echo "checked"; } ?>> Recieve a reciept by email
                          </label>
                        </div>
                      </div>
							
							
								</div>
	  			 <div class="box-footer">
						 
                    <button type="submit" class="btn btn-info pull-right">Update</button>
                  </div><!-- /.box-footer -->
							</form>
					
				</div>
				  
			  </div>
			  
			  <?php elseif (isset($_GET['item'])) : ?>
			  
			    <div class="box box-primary">
				  
				<div class="box-header with-border">
							  <h3 class="box-title">Edit Item</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					
					<?php

						if ($stmt = $mysqli->prepare("SELECT buyer, seller, description, price FROM items WHERE id = ?")) {
							$stmt->bind_param("i", $_GET['item']);
							$stmt->execute();
							$stmt->bind_result($buyer, $seller, $description, $price);
							$stmt->fetch();
							$stmt->close();
						}

					?>
					
						<form  method="POST" action ="scripts/auctionControl.php?id=<?php echo $sessionAuctionID; ?>&action=4&item=<?php echo $_GET['item'] ?>">
								   
							<div class="form-horizontal">
								
							 <div class="form-group">
                     			<label class="col-sm-3 control-label">Buyer</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" name="buyer" value="<?php echo $buyer; ?>" required>
                     			 </div>
                   			 </div>

							 <div class="form-group">
                     			<label class="col-sm-3 control-label">Seller</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" name="seller" value="<?php echo $seller; ?>" required>
                     			 </div>
                   			 </div>
									
							 <div class="form-group">
                     			<label class="col-sm-3 control-label">Description</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" name="description" value="<?php echo $description; ?>" required>
                     			 </div>
                   			 </div>
									
										 
							 <div class="form-group">
                     			<label class="col-sm-3 control-label">Price</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" name="price" value="<?php echo $price; ?>" required>
                     			 </div>
                   			 </div>
	
							
								</div>
	  			 <div class="box-footer">
						 
                    <button type="submit" class="btn btn-info pull-right">Update</button>
                  </div><!-- /.box-footer -->
							</form>
					
				</div>
				  
			  </div>
			  
			  <?php endif; ?>
			  
          </section><!-- /.content -->
        </div><!-- /.container -->
      </div><!-- /.content-wrapper -->	  
			

<?php
$mysqli -> close();
include 'includes/footer.php';
?>
