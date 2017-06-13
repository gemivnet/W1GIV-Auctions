<?php 

if($_GET['print'] == true) : ?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Reciept</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body onload="window.print();">
<?php 

session_start();
$number = $_GET['number'];
$sessionAuctionID = $_GET['id'];
$total = $_GET['total'];
$bought = $_GET['bought'];
$sold = $_GET['sold'];
$commission = $_GET['commission'];
$paid = $_GET['paid'];

include '../scripts/connect.php';

if ($stmt = $mysqli -> prepare("SELECT COUNT(*), cash, chk, name, address, phone, recieveRecieptEmail, additional FROM attendees WHERE auctionID = ? AND number = ?")) {
			$stmt -> bind_param("ii", $sessionAuctionID, $number);
			$stmt -> execute();
			$stmt -> bind_result($amt, $cashPaid, $checkPaid, $attendeeName, $attendeeAddress, $attendeePhone, $recieptEmail, $additional);
			$stmt -> fetch();
			$stmt -> close();
}
	if ($stmt = $mysqli -> prepare("SELECT baseCommission, minimumCommission FROM commissionSettings WHERE auctionID = ?")) {
			$stmt -> bind_param("i", $sessionAuctionID);
			$stmt -> execute();
			$stmt -> bind_result($base, $minimum);
			$stmt -> fetch();
			$stmt -> close();

			$base /= 100;

	}
if ($stmt = $mysqli -> prepare("SELECT address, phone FROM organizations WHERE id = ?")) {
					$stmt -> bind_param("i", $_SESSION['organizationID']);
					$stmt -> execute();
					$stmt -> bind_result($address, $phone);
					$stmt -> fetch();
					$stmt -> close();
			}

endif;

?>

<section class="invoice">
          <!-- title row -->
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                 <?php echo $_SESSION['organization']; ?>
                <small class="pull-right">Date: <?php echo date("m/d/Y");?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              From
              <address>
                <strong><?php echo $_SESSION['organization']; ?></strong><br>
                <?php echo $address; ?><br>
                <?php echo $phone; ?><br>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
              To
              <address>
                <strong><?php echo $attendeeName; ?></strong><br>
				  <?php if ($additional != null) { echo $additional."<br>"; } ?>
                 <?php if ($attendeeAddress != null) { echo $attendeeAddress."<br>"; } ?>
                <?php echo $attendeePhone; ?><br>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
				<br>
           
              <b>Attendee Number:</b> <?php echo $number; ?><br>
				<?php if ($paid == true) { 
	
					echo "<strong>Paid:</strong> ".date("m/d/Y")."<br><strong>Payment Method: </strong>"; 
	
					if ($cashPaid != 0 & $checkPaid != 0) {
						echo "Cash & Check, #".$checkPaid;
					}	
					elseif ($cashPaid != 0) {
						echo "Cash";
					} else {
						echo "Check, #".$checkPaid;
					}
										 
										 } ?>
				
            </div><!-- /.col -->
			  
          </div><!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-xs-6 table-responsive">
				<h4>
				Items Sold
				</h4>
              <table class="table table-striped">
                <thead>
                <tr>
                <th>Item Description</th>
                <th>Price Sold</th>
				<th>Commission</th>
				<th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                
					<?php

					if ($stmt = $mysqli -> prepare("SELECT description, price FROM items WHERE auctionID = ? AND seller = ?")) {
						$stmt -> bind_param("ii", $sessionAuctionID, $number);
						$stmt -> execute();
						$stmt -> bind_result($soldDescription, $soldPrice);
						
						while ($stmt -> fetch()) {
							
							echo "<tr><td>$soldDescription</td><td>".money_format("$%i", $soldPrice)."</td>";
							
							if ($soldPrice <= $minimum) {
								echo "<td>".money_format("$%i", $soldPrice)."</td><td>$0.00</td></tr>";
							} else if ($soldPrice * $base <= $minimum) {
								echo "<td>".money_format("$%i", $minimum)."</td><td>".money_format("$%i", $soldPrice - $minimum)."</td></tr>";
							} else {
								echo "<td>".money_format("$%i", $soldPrice * $base)."</td><td>".money_format("$%i", $soldPrice - ($soldPrice * $base))."</td></tr>";
							}
							
							
							
						}
						
						$stmt -> close();
					}
 			
					?>
					
                </tbody>
              </table>
            </div><!-- /.col -->
			   <div class="col-xs-6 table-responsive">
				<h4>
					Items Bought
				</h4>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Item Description</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                 <?php

					if ($stmt = $mysqli -> prepare("SELECT description, price FROM items WHERE auctionID = ? AND buyer = ?")) {
						$stmt -> bind_param("ii", $sessionAuctionID, $number);
						$stmt -> execute();
						$stmt -> bind_result($boughtDescription, $boughtPrice);
						
						while ($stmt -> fetch()) {
							
							echo "<tr><td>$boughtDescription</td><td>".money_format("$%i", $boughtPrice)."</td></tr>";
							
						}
						
						$stmt -> close();
					}
 			
					?>
                </tbody>
              </table>
            </div><!-- /.col -->
          </div><!-- /.row -->
			<br>
          <div class="row">
            <!-- accepted payments column -->
          
			  <div class="col-md-6">
					 <?php if ($paid != true) { echo "<h1>UNPAID</h1>"; }
					  
						
					  
					  ?>
				 
			  </div>
			  
            <div class="col-md-6">
 
              <div class="table-responsive">
                <table class="table">
										<tr>
											<th><?php echo ($base * 100)."% Commission"; ?></th>
                    <td><?php echo money_format('$%i', $commission); ?></td>
									</tr>
                  <tr>
                    <th style="width:50%">Bought Subtotal</th>
                    <td><?php echo money_format('$%i', $bought); ?></td>
                  </tr>
                  <tr>
                    <th>Sold Subtotal</th>
                    <td><?php echo money_format('$%i', $sold - $commission); ?></td>
                  </tr>
								
                  <tr>
					<th>Total:</th>
					<td><strong><?php echo money_format('$%i', $total); ?></strong></td>
					</tr>
	
                </table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
				   
				   <p>
					   Auction managed by <strong>W1GIV Auctions</strong> w1giv.com
				   </p>

          <!-- this row will not appear when printing -->
         
            </div>
          </div>
        </section><!-- /.content -->
