<?php

include 'scripts/connect.php';

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>W1GIV Auction</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
	  
    <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker-bs3.css">
	   <link rel="stylesheet" href="../../plugins/iCheck/all.css">
    <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
	
	<body>
		
	  <div class="container">
          <!-- Content Header (Page header) -->
       
          <!-- Main content -->
          <section class="content">
         
			  <div class="row">
				  
				  
				   <div class="col-md-6 col-md-offset-3">
					  
					     <div class="box box-primary">
							<div class="box-header with-border">
							  <h3 class="box-title">Auction Registration</h3>
							</div><!-- /.box-header -->
						<div class="box-body">
							
							
							<?php

							if ($_POST['name'] != "") {
								
								if (isset($_GET['id'])) {
									$id = $_GET['id'];
								} else {
									$id = $_POST['id'];
								}
								
								$name = $_POST['name'];
								$address = $_POST['address'];
								$phone = $_POST['phone'];
								$additional = $_POST['additional'];
								$emailAddress = $_POST['emailAddress'];
								$email = $_POST['email'];
								$reciept = $_POST['reciept'];
								
								if ($stmt = $mysqli -> prepare ("SELECT COUNT(*) FROM attendees WHERE auctionID = ?")) {
									$stmt -> bind_param("i", $id);
									$stmt -> execute();
									$stmt -> bind_result($number);
									$stmt -> fetch();
									$stmt -> close();
									
									$number++;
									
								}
								
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

								if ($stmt = $mysqli -> prepare("INSERT INTO attendees (auctionID, name, address, phone, email, recieveClubEmail, recieveRecieptEmail, additional, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
									$stmt -> bind_param("issssiisi", $id, $name, $address, $phone, $emailAddress, $email, $reciept, $additional, $number);
									$stmt -> execute();
									$stmt -> close();
								}

								
								if ($_GET['id'] == "") {
									echo "<p>Thank you for registering. Your number is <strong>$number</strong>.";
									
							
								} else {
									
								}
								
								
							} 
				
							if (isset($_GET['id'])) {

								if ($stmt = $mysqli -> prepare("SELECT organizations.name, auctions.status, commissionSettings.additional, commissionSettings.baseCommission, commissionSettings.minimumCommission FROM organizations, auctions, commissionSettings WHERE auctions.id =? AND auctions.organizationID = organizations.ID AND commissionSettings.auctionID = auctions.id LIMIT 1")) {
										$stmt -> bind_param("i", $_GET['id']);
										$stmt -> execute();
										$stmt -> bind_result($clubName, $status, $additional, $base, $minimum);
										$stmt -> fetch();
										$stmt -> close();
									
										if ($stmt = $mysqli -> prepare("SELECT entryFee FROM auctions WHERE id = ?")) {
											$stmt -> bind_param("i", $_GET['id']);
											$stmt -> execute();
											$stmt -> bind_result($entryFee);
											$stmt -> fetch();
											$stmt -> close();
										}
										
										if ($clubName == "") {
											echo "An auction with this ID can not be found";
										} else if ($status != "In Progress") {
											echo "This auction is currently not accepting new registrations.";
										} 
									
										if ($clubName != "" && $status == "In Progress") : ?>
							
									<?php
									if ($name != "") {
										 echo " <div class='callout callout-success'> <p><strong>$name</strong> has been registered with number <strong>$number</strong></p> </div>";
									}
									?>
							
							<p><h3>
									<strong><?php echo $clubName; ?></strong> Auction Registration
							</h3></p>
							
							<form  method="POST" action ="/attend.php?id=<?php echo $_GET['id']; ?>">
								     <div class="form-horizontal">
							 <div class="form-group">
                     			<label for="name" class="col-sm-3 control-label">Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                     			 </div>
                   			 </div>

							 <div class="form-group">
                     			<label for="address" class="col-sm-3 control-label">Full Address</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                     			 </div>
                   			 </div>
									
							 <div class="form-group">
                     			<label for="phone" class="col-sm-3 control-label">Phone</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                     			 </div>
                   			 </div>
										 
							 <div class="form-group">
                     			<label for="email" class="col-sm-3 control-label">Email</label>
                      			<div class="col-sm-8">
                       				 <input type="email" class="form-control" id="email" name="emailAddress" placeholder="Email" required>
                     			 </div>
                   			 </div>
										 
							<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">		 
										 
							<?php

							if ($additional != "") : ?>
										 			 
							<div class="form-group">
                     			<label for="additional" class="col-sm-3 control-label"><?php echo $additional; ?></label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="additional" name="additional" placeholder="<?php echo $additional; ?>">
                     			 </div>
                   			 </div>
										 
							<?php endif; ?>
										 
									  <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="email" class="flat-red"> Allow organization to send me emails
                          </label>
                        </div>
                      </div>
										  
					<div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="reciept" class="flat-red"> Recieve a receipt by email
                          </label>
                        </div>
                      </div>
										 
										 
									
								</div>
										 
										
										 
										   <div class="box-footer">
											    <p>
											This auction charges the seller a <strong><?php echo $base; ?>%</strong> commission on each item they sell.<br>
											The minimum charge for each item sold is <strong><?php echo money_format("$%i", $minimum); ?></strong><br><br>
											There is a <strong><?php echo money_format("$%i", $entryFee); ?></strong> registration fee for this auction.
											<br><br>
											<strong>Please note your number upon registration. You will need this to pick up your bidding card.</strong>
										  </p>
                    <button type="submit" class="btn btn-info pull-right">Register</button>
                  </div><!-- /.box-footer -->
								
							</form>
									<?php endif;
										
									}
								
							} else if ($name == "") {
								
								if (!$_POST['id'] == "") {
									if ($stmt = $mysqli -> prepare("SELECT organizations.name, auctions.status, commissionSettings.additional, commissionSettings.baseCommission, commissionSettings.minimumCommission FROM organizations, auctions, commissionSettings WHERE auctions.id =? AND auctions.organizationID = organizations.ID AND commissionSettings.auctionID = auctions.id LIMIT 1")) {
										$stmt -> bind_param("i", $_POST['id']);
										$stmt -> execute();
										$stmt -> bind_result($clubName, $status, $additional, $base, $minimum);
										$stmt -> fetch();
										$stmt -> close();
										
										if ($stmt = $mysqli -> prepare("SELECT entryFee FROM auctions WHERE id = ?")) {
											$stmt -> bind_param("i", $_POST['id']);
											$stmt -> execute();
											$stmt -> bind_result($entryFee);
											$stmt -> fetch();
											$stmt -> close();
										}
										
										if ($clubName == "") {
											$error = "An auction with this ID can not be found";
										} else 
										if ($status != "In Progress") {
											 $error = "This auction is currently not accepting new registrations.";
										}
									}
									
							}

							if (!$_POST['id'] == "" && !isset($error)) : ?>
							
									
							
							<p>
									You are registering for the <strong><?php echo $clubName; ?></strong> auction. If this is not correct <a href="attend.php">Click Here</a> to enter the ID again.
							</p>
							
							<form  method="POST" action ="/attend.php">
								     <div class="form-horizontal">
							 <div class="form-group">
                     			<label for="name" class="col-sm-3 control-label">Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                     			 </div>
                   			 </div>

							 <div class="form-group">
                     			<label for="address" class="col-sm-3 control-label">Full Address</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="address" name="address" placeholder="Address">
                     			 </div>
                   			 </div>
									
							 <div class="form-group">
                     			<label for="phone" class="col-sm-3 control-label">Phone</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                     			 </div>
                   			 </div>
										 
							 <div class="form-group">
                     			<label for="email" class="col-sm-3 control-label">Email</label>
                      			<div class="col-sm-8">
                       				 <input type="email" class="form-control" id="email" name="emailAddress" placeholder="Email" required>
                     			 </div>
                   			 </div>
										 
							<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">		 
										 
							<?php

							if ($additional != "") : ?>
										 			 
							<div class="form-group">
                     			<label for="additional" class="col-sm-3 control-label"><?php echo $additional; ?></label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="additional" name="additional" placeholder="<?php echo $additional; ?>">
                     			 </div>
                   			 </div>
										 
							<?php endif; ?>
										 
									  <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="email" class="flat-red"> Allow organization to send me emails
                          </label>
                        </div>
                      </div>
										  
					<div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="reciept" class="flat-red"> Recieve a receipt by email
                          </label>
                        </div>
                      </div>
              
									
								</div>
										 
										   <div class="box-footer">
											     <p>
											This auction charges the seller a <strong><?php echo $base; ?>%</strong> commission on each item they sell.<br>
											The minimum charge for each item sold is <strong><?php echo money_format("$%i", $minimum); ?></strong><br><br>
											There is a <strong><?php echo money_format("$%i", $entryFee); ?></strong> registration fee for this auction.
													 <br><br>
											<strong>Please note your number upon registration. You will need this to pick up your bidding card.</strong>
										  </p>
                    <button type="submit" class="btn btn-info pull-right">Register</button>
                  </div><!-- /.box-footer -->
								
							</form>
							
							<?php else : ?>
							
							<form method="POST" action ="/attend.php">
								<p>
									Please enter the ID of the auction for which you are registering. This number will be given to you by auction staff.
								</p>
								
								<?php if(isset($error)) {echo " <div class='callout callout-danger'>$error</div>";} ?>
								
								  <div class="form-group">
                     				 <label for="id" class="col-sm-3 control-label">Auction ID</label>
                      				 <div class="col-sm-3">
                       				 <input type="text" class="form-control" id="id" name="id" placeholder="ID" required>
                     			 </div>
                   			 </div>
								
								<button class="btn btn-primary" type="submit">
									Next
								</button>
								
							</form>


							<?php endif; 
								
							}

							?>
					
               			 </div><!-- /.box-body -->
							
             			 </div><!-- /.box -->
					  
					  
					  
				  </div> <!-- /col -->
			  </div>
		
			  
			
			
			
			  
          </section><!-- /.content -->
        </div><!-- /.container -->
	
    <!-- jQuery 2.1.4 -->
    <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="../../plugins/input-mask/jquery.inputmask.js"></script>
    <script src="../../plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="../../plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="../../plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
    <!-- bootstrap time picker -->
    <script src="../../plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <script src="../../plugins/iCheck/icheck.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
		
		 <script>
      $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();

        //Datemask dd/mm/yyyy
        $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //Datemask2 mm/dd/yyyy
        $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
        //Money Euro
        $("[data-mask]").inputmask();

        //Date range picker
        $('#reservation').daterangepicker();
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        //Date range as a button
        $('#daterange-btn').daterangepicker(
            {
              ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              },
              startDate: moment().subtract(29, 'days'),
              endDate: moment()
            },
        function (start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        );

        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
          checkboxClass: 'icheckbox_minimal-blue',
          radioClass: 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
          checkboxClass: 'icheckbox_minimal-red',
          radioClass: 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });

        //Colorpicker
        $(".my-colorpicker1").colorpicker();
        //color picker with addon
        $(".my-colorpicker2").colorpicker();

        //Timepicker
        $(".timepicker").timepicker({
          showInputs: false
        });
      });
    </script>
		
  </body>
</html>
	
<?php

$mysqli -> close();

?>