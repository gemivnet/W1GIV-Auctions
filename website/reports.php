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

if (isset($_POST['name'])) {
	
	$name = $_POST['name'];
	$auctionInfo = (isset($_POST['auctionInfo'])) ? 1 : 0;
	$attendees = (isset($_POST['attendees'])) ? 1 : 0;
	$items = (isset($_POST['items'])) ? 1 : 0;
	$commission = (isset($_POST['commission'])) ? 1 : 0;
	$organizationItems = (isset($_POST['organizationItems'])) ? 1 : 0;
	
	//if ($stmt = $mysqli -> prepare("INSERT INTO reports (auctionID, auctionInfo, attendeeList, itemList, commissionOverview, organizationItemOverview, name) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
	//	$stmt -> bind_param("issssss", $sessionAuctionID, $auctionInfo, $attendees, $items, $commission, $organizationItems, $name);
	//	$stmt -> execute();
	//	$id = $stmt -> insert_id;
	//	$stmt -> close();
	//}

	
		require('scripts/fpdf/fpdf.php');

	class PDF extends FPDF
{
// Page header
function Header()
{
  	$o = $_SESSION['organization']." Auction Report";
    // Arial bold 15
    $this->SetFont('Times','B',18);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(30,10,$o, 0, 0, "C");
    // Line break
    $this->Ln(10);
		$this->SetFont('Times','',15);
		$this->Cell(80);
	 	$this->Cell(30,10,"date", 0, 0, "C");
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Auction report generated by W1GIV Auctions - Page '.$this->PageNo().' / {nb}',0,0,'C');
}
}
	
	$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);


//$filename="reports/".md5($sessionAuctionID."".$id).".pdf";
$pdf->Output("test.pdf",'F');

	$success = "Your report, <b>$name</b>, has been generated";
	
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
             Reports
             
            </h1>
            <ol class="breadcrumb">
              <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
			        <li>Reports</li>
            </ol>
          </section>

          <!-- Main content -->
          <section class="content">
          <div class="box box-default">
              <div class="box-header with-border">

                <h3 class="box-title">Generate Report</h3>
              </div>
              <div class="box-body">

								<?php   if(isset($success)) {echo " <div class='callout callout-success'>$success</div>"; } ?>
								
            <form  method="POST" action ="/reports.php?id=<?php echo $sessionAuctionID; ?>">

							<div class="form-horizontal">
							
							 <div class="form-group">
                     		
                  <div class="row form-horizontal">
                    
                    <div class="col-md-5 col-md-offset-1">
                    
                      	 <div class="form-group">
                     			<label for="name" class="col-sm-3 control-label">Report Name</label>
                      			<div class="col-sm-8">
                       				 <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                     			 </div>
                   			 </div>
                      
                        <div class="form-group">
                        <div class="checkbox">
                          <label>
                             <input type="checkbox" name="auctionInfo" class="flat-red"> Auction Information
                          </label>
                        </div>
                           <div class="checkbox">
                          <label>
                             <input type="checkbox" name="attendees" class="flat-red"> Attendee List
                          </label>
                        </div>
                           <div class="checkbox">
                          <label>
                             <input type="checkbox" name="items" class="flat-red"> Item List
                          </label>
                        </div>
                           <div class="checkbox">
                          <label>
                             <input type="checkbox" name="commission" class="flat-red"> Commission Overview
                          </label>
                        </div>
                           <div class="checkbox">
                          <label>
                             <input type="checkbox" name="organizationItems" class="flat-red"> Organization Item Overview
                          </label>
                        </div>
													
													 <div class="pull-right">
                    <button type="submit" class="btn btn-primary">Generate</button>
                 				 </div>
													
                      </div>                     
                    </div>     
                 </div>     
								</div>
							</div>
								</form>
						</div>
						</div>
								 
								   <div class="box box-default">
              <div class="box-header with-border">

                <h3 class="box-title">Reports</h3>
              </div>
              <div class="box-body">

								 <table id="example2" class="table table-bordered table-hover">
                    <thead>
                      <tr>
												<th>Name</th>
						 						<th>Date</th>
                        <th>Auction Information</th>
                        <th>Items</th>
                        <th>Commission</th>
												<th>Organization Items</th>
												<th>View</th>
                      </tr>
                    </thead>
                    <tbody>
                     
						<?php

						if ($stmt = $mysqli -> prepare("SELECT id, name, created, auctionInfo, attendeeList, itemList, commissionOverview, organizationItemOverview FROM reports WHERE auctionID = ? ORDER BY id DESC")) {
							$stmt -> bind_param("i", $sessionAuctionID);
							$stmt -> execute();
							$stmt -> bind_result($id, $name, $created, $auctionInfo, $attendeeList, $itemList, $commissionOverview, $organizationItemOverview);
							
							while ($stmt -> fetch() ) {
								echo "<tr><td>$name</td><td>$created</td><td>$auctionInfo</td><td>$attendeeList</td><td>$itemList</td><td>$commissionOverview</td><td>$organizationItemOverview</td><td><a class='btn btn-primary' target='_blank' href='/reports/".md5($sessionAuctionID."".$id).".pdf'>View</a></td></tr>";
							}
							
							$stmt -> close();
							
						}
	
						?>
						
                    </tbody>
                  </table>
								
                 </div>   
			  
          </section><!-- /.content -->
        </div><!-- /.container -->
      </div><!-- /.content-wrapper -->

<?php
$mysqli -> close();
include 'includes/footer.php';
?>
