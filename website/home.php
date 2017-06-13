<?php

session_start();

if (!isset($_SESSION['email'])) {
	
	header('Location: /login.php');
	
}

include 'scripts/connect.php';
include 'includes/nodashHeader.php';

?>

<div class="content-wrapper">
	<div class="container">
		
		<section class="content-header">

			<h1>Home<small>Manage Auctions, Payment, and Organization</small></h1>

			<ol class="breadcrumb">
				<li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
			</ol>
			
		</section>

		<section class="content">
			
			<div class="box box-default">
				<div class="box-header with-border">
				   
					<div class="pull-right box-tools">
						<a href="/createType.php" class="btn btn-primary btn-normal pull-right">Create Auction</a>
					</div>
					
				<h3 class="box-title">Auctions</h3>
					
				</div>

				<div class="box-body">
				  
					<p>Click on an auction to manage.</p>
				 
						<div class="box-body table-responsive no-padding">
							<table class="table table-hover">
								<tr>
									<th>Auction ID</th>
									<th>Start Date</th>
									<th>End Date</th>
									<th>Status</th>
									<th>Location</th>
									<th>Attendees</th>
                                                                        <th>Items</th>
									<th>Manage</th>
								</tr>
                
							<?php

								if ($stmt = $mysqli -> prepare ("SELECT auctions.id, auctions.startDate, auctions.endDate, auctions.status, locations.name, (SELECT COUNT(*) FROM attendees WHERE attendees.auctionID = auctions.id) as attendeesCount, (SELECT COUNT(*) FROM items WHERE items.auctionID = auctions.id) as itemsCount FROM auctions, locations WHERE auctions.locationID = locations.id AND auctions.organizationID = ? ORDER BY auctions.id DESC")) {
									$stmt -> bind_param("i", $_SESSION['organizationID']);
									$stmt -> execute();
									$stmt -> bind_result($id, $startDate, $endDate, $status,  $locationName, $attendeesCount, $itemsCount);
						
									while ($stmt -> fetch()) {
							
										echo "<tr class='clickable-row' date-href='url://http://w1giv.com'><td>$id</td>";
                                                                                
                                                                                $date = new DateTime($startDate);
                                                                                $date = $date -> format("F d, Y g:i A");
                                                                                echo "<td>$date</td>";
                                                                                
                                                                                $date = new DateTime($endDate);
                                                                                $date = $date -> format("F d, Y g:i A");
                                                                                echo "<td>$date</td>";
                                                                                
							
										switch ($status) {
											case "Pending":
												echo "<td><span class='label label-warning'>Pending</span></td>";
												break;
											case "In Progress":
												echo "<td><span class='label label-primary'>In Progress</span></td>";
												break;
											case "Completed":
												echo "<td><span class='label label-danger'>Completed</span></td>";
												break;
										}
							
										echo "<td>$locationName</td>";
								
										
										echo "<td>$attendeesCount</td><td>$itemsCount</td><td><a class='btn btn-primary' href='manage.php?id=$id'>Manage</a></td>";
						
										echo "</tr>";
							
									}
											
									$stmt -> close();
								}
						
							?>
                   
						</table>
					
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>

		</section><!-- /.content -->
	</div><!-- /.container -->
</div><!-- /.content-wrapper -->



<?php
$mysqli -> close();
include 'includes/footer.php';
?>
