<?php
session_start();
if (!isset($_SESSION['email'])) {
	
	header('Location: /login.php');
	
}
include 'includes/header.php';
?>
		
      <!-- Full Width Column -->
      <div class="content-wrapper">
        <div class="container">
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <h1>
              Create Auction
    			<small>Choose Auction Type</small>
            </h1>
            <ol class="breadcrumb">
              <li ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active" ><a href="/createType.php">Create Auction</a></li>
				<li class="active" ><a href="/createType.php">Auction Type</a></li>
            </ol>
          </section>
			
			  <section class="content">
            <div class="box box-default">
              
              <div class="box-body">
				  
				   <div class="row ">
        <div class="col-xs-12 col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Commission</h3>
                </div>
                <div class="panel-body">
                    <div class="the-price">
                       <p>
						   A commission auction is for when people are going to bring in items to the auction and stay for the duration of the auction. The sellers may buy items at the auction. Your organization then takes a percentage of the sales and gives the rest to the sellers.
						</p>
                    </div>
                 
                </div>
                <div class="panel-footer">
                    <a href="createCommission.php" class="btn btn-success" role="button">Create</a>
                    </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="panel panel-success">
                
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Consignment</h3>
                </div>
                <div class="panel-body">
                    <div class="the-price">
                       <p>
						   A consignment auction is for when people will bring items prior to the start of the auction and will not be present for the auction. The organization then takes apercantage of the sales and gives the rest to the sellers at the end of the auction.
						</p>
                    </div>
                  
                </div>
                <div class="panel-footer">
                    <a href="createConsignment.php" class="btn btn-success" role="button">Create</a>
                    </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Organization</h3>
                </div>
                <div class="panel-body">
                    <div class="the-price">
                        <p>
							An organization auction is for when the organization owns the items being sold.
						</p>
                    </div>
                   
                </div>
                <div class="panel-footer">
                    <a href="createOrganization.php" class="btn btn-success" role="button">Create</a></div>
            </div>
        </div>
    </div>
		  </div>
					
				
              </div><!-- /.box-body -->
            </div><!-- /.box -->

   


          </section><!-- /.content -->
        </div><!-- /.container -->
      </div><!-- /.content-wrapper -->

<?php
include 'includes/footer.php';
?>
