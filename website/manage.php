<?php
session_start();
if (!isset($_SESSION['email'])) {

    header('Location: /login.php');
}

include 'scripts/connect.php';

$sessionAuctionID = $_GET['id'];

if ($stmt = $mysqli->prepare("SELECT * FROM auctions WHERE organizationID = ? AND id = ?")) {
    $stmt->bind_param("ss", $_SESSION['organizationID'], $sessionAuctionID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {

        header('Location: home.php');
    }

    $stmt->close();
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
                Dashboard
                <small>Manage Auction</small>
            </h1>
            <ol class="breadcrumb">
                <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Manage</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            
                  <div class="row">

                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>

                                        <?php
                                        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM attendees WHERE auctionID =?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($attendees);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

                                        echo $attendees;
                                        ?>
                                    </h3>

                                    <p>Attendees</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-stalker"></i>
                                </div>
                                <a href="/attendees.php?id=<?php echo $sessionAuctionID; ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>


                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>  <?php
                                        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM items WHERE auctionID =?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($items);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

                                        echo $items;
                                        ?></h3>

                                    <p>Items</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-cube"></i>
                                </div>
                                <a href="/items.php?id=<?php echo $sessionAuctionID; ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3><sup style="font-size: 20px">$</sup>
                                        <?php
                                        if ($stmt = $mysqli->prepare("SELECT entryFee FROM auctions WHERE id =?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($entryFee);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

// Get basic auction settings
                                        if ($stmt = $mysqli->prepare("SELECT baseCommission, minimumCommission FROM commissionSettings WHERE auctionID = ?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($base, $minimum);
                                            $stmt->fetch();
                                            $stmt->close();

                                            $base /= 100;
                                        } // End auction settings

                                        if ($stmt = $mysqli->prepare("SELECT price FROM items WHERE auctionID =? AND seller != 0")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($price);

                                            while ($stmt->fetch()) {

                                                if ($price <= $minimum) {
                                                    $commission += $price;
                                                } else if ($price * $base <= $minimum) {
                                                    $commission += $minimum;
                                                } else {
                                                    $commission+= $price * $base;
                                                }
                                            }

                                            $stmt->close();
                                        }

                                        if ($stmt = $mysqli->prepare("SELECT SUM(price) FROM items WHERE auctionID = ?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($total);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

                                        if ($stmt = $mysqli->prepare("SELECT price FROM items WHERE auctionID =? AND seller = 0")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($price);

                                            while ($stmt->fetch()) {

                                                $club += $price;
                                            }

                                            $stmt->close();
                                        }

                                        $fee += $attendees * $entryFee;

                                        echo money_format("%i", $commission + $fee + $club);
                                        ?>

                                    </h3>

                                    <p>Cumulative Profit</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-cash"></i>
                                </div>
                                <a class="small-box-footer">Entry Fee + Commmission + Donations</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>

                                        <?php
                                        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT attendees.name FROM attendees, (SELECT buyer, seller FROM items WHERE auctionID = ?) As a WHERE attendees.auctionID = ? AND (attendees.number = a.buyer OR attendees.number = a.seller) AND (attendees.chk != 0 OR attendees.cash != 0)) AS B")) {
                                            $stmt->bind_param("ii", $sessionAuctionID, $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($attendeesPaid);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

                                        if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT attendees.name FROM attendees, (SELECT buyer, seller FROM items WHERE auctionID = ?) As a WHERE attendees.auctionID = ? AND (attendees.number = a.buyer OR attendees.number = a.seller)) AS B")) {
                                            $stmt->bind_param("ii", $sessionAuctionID, $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($attendeesNeed);
                                            $stmt->fetch();
                                            $stmt->close();
                                            ?>
                                            <sup><?php echo $attendeesPaid; ?></sup>&frasl;<sub><?php echo $attendeesNeed; ?></sub>
                                            <?php
                                            echo round((($attendeesPaid / $attendeesNeed) * 100));
                                        }
                                        ?>

                                        <sup style="font-size: 20px">%</sup></h3>

                                    <p>Attendees Paid</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-cart"></i>
                                </div>
                                <a href="/checkout.php?id=<?php echo $sessionAuctionID; ?>&overview=true" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>


                  </div>

            <div class="row">

                <div class="col-md-5">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Auction Status</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">

                            <p>
                                <strong>Current Auction Status: </strong>

<?php
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
                                <br><br>
                                <a href="/scripts/auctionControl.php?id=<?php echo $sessionAuctionID; ?>&action=0" class="btn btn-warning">Mark as Pending</a>
                                <a href="/scripts/auctionControl.php?id=<?php echo $sessionAuctionID; ?>&action=1" class="btn btn-primary">Mark as In Progress</a>
                                <a href="/scripts/auctionControl.php?id=<?php echo $sessionAuctionID; ?>&action=2" class="btn btn-success">Mark as Completed</a>

                            </p>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                </div>

                <div class="col-md-7">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Auction Information</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">

                            <div class="row">

                                <div class="col-md-4">
                                    <p><strong>Base Commission: </strong><?php echo $base * 100; ?>%<br>
                                        <strong>Min Commission: </strong><?php echo money_format("$%i", $minimum); ?><br>
                                        <strong>Entry Fee: </strong><?php echo money_format("$%i", $entryFee); ?>
                                    </p>
                                </div>

                                <div class="col-md-4">
                                    <p>
                                        <strong>Entry Fee Profit: </strong><?php echo money_format("$%i", $fee); ?><br>
                                        <strong>Commission Profit: </strong><?php echo money_format("$%i", $commission); ?><br>
                                        <strong>Club Item Profit: </strong><?php echo money_format("$%i", $club); ?><br>
                                        <strong>Gross Sales: </strong><?php echo money_format("$%i", $total); ?><br>
                                    </p>
                                </div>

                            </div>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                </div>

            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Log</h3>
                </div><!-- /.box-header -->
                <div class="box-body">

                    <p>
                        These are the last 10 events that has happened for this auction.
                    </p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>

<?php
if ($stmt = $mysqli->prepare("SELECT contacts.firstName, contacts.lastName, a.time, a.actn FROM contacts, (SELECT contactID, time, actn FROM logs WHERE auctionID = ?) AS a WHERE contacts.id = a.contactID ORDER BY time DESC LIMIT 10")) {
    $stmt->bind_param("i", $sessionAuctionID);
    $stmt->execute();
    $stmt->bind_result($contactFirstName, $contactLastName, $date1, $actn);

    while ($stmt->fetch()) {

        echo "<tr><td>$contactFirstName $contactLastName $actn</td>";

        $datetime1 = new DateTime();
        $datetime2 = new DateTime($date1);
        $interval = $datetime1->diff($datetime2);
        $years = $interval->format('%y');
        $months = $interval->format('%m');
        $days = $interval->format('%a');
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $seconds = $interval->format('%S');

        if ($years > 0) {
            echo "<td>$years y</td>";
        } else if ($months > 0) {
            echo "<td>$months months</td>";
        } else if ($days > 0) {
            echo "<td>$days d</td>";
        } else if ($hours > 0) {
            echo "<td>$hours h</td>";
        } else if ($minutes > 0) {
            echo "<td>$minutes m</td>";
        } else {
            echo "<td>$seconds s</td>";
        }

        echo "</tr>";
    }

    $stmt->close();
}
?>

                        </tbody>
                    </table>


                </div><!-- /.box-body -->
            </div><!-- /.box -->

        </section><!-- /.content -->
    </div><!-- /.container -->
</div><!-- /.content-wrapper -->

<?php
$mysqli->close();
include 'includes/footer.php';
?>
