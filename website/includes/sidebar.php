
<aside class="main-sidebar">

        <section class="sidebar">

          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
			
            <li class="treeview">
              <a href="manage.php?id=<?php echo $sessionAuctionID;?>">
                <span>
				  
				   <?php
					if ($stmt = $mysqli->prepare("SELECT status FROM auctions WHERE id =?")) {
					$stmt -> bind_param("i", $sessionAuctionID);
					$stmt -> execute();
					$stmt -> bind_result($status);
					$stmt -> fetch();
					$stmt -> close();
				}

				echo "Auction ID: ".$sessionAuctionID." - ";

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
								break;
							}

				?>
					
				  </span>
              </a>
            
            </li>
			  
			  </li>
            <li class="header">Auction Management</li>
            <li class="treeview">
              <a href="manage.php?id=<?php echo $sessionAuctionID;?>">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              </a>
            
            </li>
          
             <li class="treeview">
              <a href="#">
                <i class="fa fa-users"></i> <span>Attendees</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li> <a href="attendees.php?id=<?php echo $sessionAuctionID;?>"><i class="fa fa-pencil"></i> Registration</a></li>
                <li> <a href="email.php?id=<?php echo $sessionAuctionID;?>"><i class="fa fa-envelope"></i> Email</a></li>
              </ul>
            </li>
            
            <li>
              <a href="items.php?id=<?php echo $sessionAuctionID; ?>">
                <i class="fa fa-th"></i> <span>Items</span>
              </a>
            </li>
            <li class="treeview">
              <a href="checkout.php?id=<?php echo $sessionAuctionID; ?>">
                <i class="fa fa-money"></i>
                <span>Checkout</span>
              </a>
            </li>
            <li class="header">Settings</li>
            <li>  <a href="auctionSettings.php?id=<?php echo $sessionAuctionID; ?>"><i class="fa fa-gear"></i> <span>Auction Settings</span></a></li>
			   <li class="header">Reports</li>
            <li><a href="reports.php?id=<?php echo $sessionAuctionID; ?>"><i class="fa fa-bar-chart-o"></i> <span>Reports</span></a></li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
