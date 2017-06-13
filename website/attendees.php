<?php
session_start();                                // Begin the session so session variables can be used

if (!isset($_SESSION['email'])) {               // If the session vairable 'email' is not set, then redirect the user to the login page
    header('Location: /login.php');
}

include 'scripts/connect.php';

$sessionAuctionID = $_GET['id'];

// Check if the user's organization has access to this auction, if not redirect the user to their homepage
if ($stmt = $mysqli->prepare("SELECT * FROM auctions WHERE organizationID = ? AND id = ?")) {
    $stmt->bind_param("ss", $_SESSION['organizationID'], $sessionAuctionID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        header('Location: home.php');
    }

    $stmt->close();
}

$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$additional = $_POST['additional'];
$emailAddress = $_POST['emailAddress'];
$email = $_POST['email'];
$reciept = $_POST['reciept'];

$added = false;

// If adding a previous attendee
if (isset($_GET['add'])) {
    
    // Check if the attendee belongs to this organization
    $added = true;
    if ($stmt = $mysqli->prepare("SELECT organizationID FROM auctions WHERE id = (SELECT auctionID FROM attendees WHERE id = ?)")) {
        $stmt -> bind_param("s", $_GET['add']);
        $stmt -> execute();
        $stmt -> bind_result($oA);
        $stmt -> fetch();
        $stmt -> close();
        
        if ($oA == $_SESSION['organizationID']) {
            
            if ($stmt = $mysqli -> prepare("SELECT name, address, phone, email, recieveClubEmail, recieveRecieptEmail, additional FROM attendees WHERE id = ?")) {
                $stmt -> bind_param("s", $_GET['add']);
                $stmt -> execute();
                $stmt -> bind_result($name, $address, $phone, $emailAddress, $email, $reciept, $additional);
                $stmt -> fetch();
                $stmt -> close();
            }
            
            if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM attendees WHERE auctionID = ?")) {
                $stmt->bind_param("i", $sessionAuctionID);
                $stmt->execute();
                $stmt->bind_result($number);
                $stmt->fetch();
                $stmt->close();

                $number++;
           
            }
         
            if ($stmt = $mysqli->prepare("INSERT INTO attendees (auctionID, name, address, phone, email, recieveClubEmail, recieveRecieptEmail, additional, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $stmt->bind_param("issssiisi", $sessionAuctionID, $name, $address, $phone, $emailAddress, $email, $reciept, $additional, $number);
                $stmt->execute();
                $stmt->close();
                
                $added = true;
                
            }

            if ($added && $stmt = $mysqli->prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
                $action = "registered $name as number $number";
                $stmt->bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
                $stmt->execute();
                $stmt->close();
            }
            
        }
        
    }
   
}

// If the form has been submitted
if (isset($_POST['submit'])) {
    $id = $sessionAuctionID;
    
    if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM attendees WHERE auctionID = ?")) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($number);
        $stmt->fetch();
        $stmt->close();

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

    if ($stmt = $mysqli->prepare("INSERT INTO attendees (auctionID, name, address, phone, email, recieveClubEmail, recieveRecieptEmail, additional, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
        $stmt->bind_param("issssiisi", $id, $name, $address, $phone, $emailAddress, $email, $reciept, $additional, $number);
        $stmt->execute();
        $stmt->close();
    }
    
    $added = true;

    if ($stmt = $mysqli->prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
        $action = "registered $name as number $number";
        $stmt->bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
        $stmt->execute();
        $stmt->close();
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
                <small>Manage Attendees</small>
            </h1>
            <ol class="breadcrumb">
                <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Attendees</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <?php
            if ($status == "In Progress") :
                ?>

                <div class="row">

                    <div class="col-md-8">

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Register Manually</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">

                                <?php
                                if ($added) {
                                    echo " <div class='callout callout-success'> <p><strong>$name</strong> has been registered with number $data <strong>$number</strong></p> </div>";
                                }
                                ?>

                                <form  method="POST" action ="/attendees.php?id=<?php echo $sessionAuctionID; ?>">

                                    <div class="form-horizontal">

                                        <div class="form-group">
                                            <label for="name" class="col-sm-3 control-label">Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Name" <?php if (isset($_POST['lookup'])) { echo "value='$name'"; }?>>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label">Full Address</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" <?php if (isset($_POST['lookup'])) { echo "value='$address'"; }?>>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone" class="col-sm-3 control-label">Phone</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" <?php if (isset($_POST['lookup'])) { echo "value='$phone'"; }?>>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email" class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="email" class="form-control" id="email" name="emailAddress" placeholder="Email" <?php if (isset($_POST['lookup'])) { echo "value='$emailAddress'"; }?>>
                                            </div>
                                        </div>


                                        <?php
                                        if ($stmt = $mysqli->prepare("SELECT additional FROM commissionSettings WHERE auctionID = ?")) {
                                            $stmt->bind_param("i", $sessionAuctionID);
                                            $stmt->execute();
                                            $stmt->bind_result($add);
                                            $stmt->fetch();
                                            $stmt->close();
                                        }

                                        if ($add != "") :
                                            ?>

                                            <div class="form-group">
                                                <label for="additional" class="col-sm-3 control-label"><?php echo $add; ?></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="additional" name="additional" placeholder="<?php echo $add; ?>" <?php if (isset($_POST['lookup'])) { echo "value='$additional'"; }?>>
                                                </div>
                                            </div>

                                        <?php endif; ?>

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
                                                    <input type="checkbox" name="reciept" class="flat-red"> Receive a receipt by email
                                                </label>
                                            </div>
                                        </div>


                                    </div> 
                                    <div class="box-footer">
                                        <button type="submit" name="lookup" value="lookup" class="btn btn-warning pull-left">Look Up</button>
                                         <button type="submit" name="submit" value="submit" class="btn btn-info pull-right">Register</button>
                                    </div><!-- /.box-footer -->
                                </form>

                                <?php
                                if (isset($_POST['lookup'])) {

                                    $queryName = "%$name%";
                                    $queryAddress = "%$address%";
                                    $queryPhone = "%$phone%";
                                    $queryEmail = "%$emailAddress%";
                                    $queryAdditional = "%$additional%";


                                    if ($stmt = $mysqli->prepare("SELECT attendees.id, attendees.name, attendees.address, attendees.phone, attendees.email, attendees.additional, attendees.auctionID, auctions.startDate FROM attendees, auctions WHERE ((attendees.name LIKE ? AND ? <> '%%') OR (attendees.address LIKE ? AND ? <> '%%') OR (attendees.phone LIKE ? AND ? <> '%%') OR (attendees.email LIKE ? AND ? <> '%%') OR (attendees.additional LIKE ? AND ? <> '%%')) AND attendees.auctionID = auctions.id AND attendees.auctionID IN (SELECT id FROM auctions WHERE organizationID = ?) ")) {
                                        $stmt->bind_param("ssssssssssi", $queryName, $queryName, $queryAddress, $queryAddress, $queryPhone, $queryPhone, $queryEmail, $queryEmail, $queryAdditional, $queryAdditional, $_SESSION['organizationID']);
                                        $stmt->execute();
                                        $stmt->bind_result($resultID, $resultName, $resultAddress, $resultPhone, $resultEmail, $resultAdditional, $auctionID, $auctionDate);
                                        
                                           
                                        
                                            ?>
                                
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <?php
                                                        if ($add != "") {
                                                            echo "<th>$add</th>";
                                                        }
                                                        ?>
                                                        <th>Auction</th>
                                                      
                                                        <th>Add</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            
                                        <?php
                                      


                                        while ($stmt->fetch()) {
                                            
                                            if ($auctionID != $sessionAuctionID) {
                                            
                                            echo "<tr><td>$resultName</td><td>$resultAddress</td><td>$resultPhome</td><td>$resultEmail</td>";
                                            if ($add) {
                                             echo "<td>$resultAdditional</td>";   
                                            } else {
                                                echo "<td></td>";
                                            }
                                            echo "<td>";
                                            
                                            $date = new DateTime($auctionDate);
                                            echo $date->format('F d, Y');
                                            
                                            echo "</td><td><a class='btn btn-primary' href='attendees.php?id=$sessionAuctionID&add=$resultID'>Add</a></td></tr>";
                                        }
                                        }

                                       ?>
                                                </tbody>
                                            </table>
                                        <?php 
                                        
                                     
                                        
                                        $stmt->close();
                                    }
                                }
                                ?>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->



                    </div> <!-- /col -->
                    <div class="col-md-4">

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Remote Registration</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <p>

                                    <a href="attend.php?id=<?php echo $sessionAuctionID; ?>">Click Here </a> to open up the self registration window. 

                                </p>

                                <hr>

                                <p>
                                    To have people sign up for the auction with their own devices direct them to the following URL. The auction ID for this auction is <strong><?php echo $sessionAuctionID; ?></strong>
                                </p>

                                <div class="well well-sm">http://www.w1giv.com/attend.php</div>

                                <p>
                                    <strong>Registrants will automatically be assigned an Buyer/Seller number.</strong>
                                </p>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->



                    </div> <!-- /col -->
                </div>

            <?php endif;
            if ($status == "Pending") :
                ?>

                <div class="callout callout-warning">
                    <p>Registration is not available while the auction is pending.</p>
                </div>

            <?php elseif ($status == "Completed") : ?>

                <div class="callout callout-warning">
                    <p>Registration is not available once the auction has been completed.</p>
                </div>

            <?php endif; ?>


            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Registrants</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <?php
                                if ($add != "") {
                                    echo "<th>$add</th>";
                                }
                                ?>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if ($stmt = $mysqli->prepare("SELECT id, name, address, phone, email, number, additional FROM attendees WHERE auctionID = ? ORDER BY number ASC")) {
                                $stmt->bind_param("i", $sessionAuctionID);
                                $stmt->execute();
                                $stmt->bind_result($id, $attendeeName, $attendeeAddress, $attendeePhone, $attendeeEmail, $attendeeNumber, $attendeeAdditional);

                                while ($stmt->fetch()) {
                                    echo "<tr><td>$attendeeNumber</td><td>$attendeeName</td><td>$attendeeAddress</td><td>$attendeePhone</td><td>$attendeeEmail</td>";

                                    if ($add != "") {
                                        echo "<td>$attendeeAdditional</td>";
                                    }

                                    echo "<td><a href='edit.php?id=$sessionAuctionID&attendee=$id'><i class='fa fa-pencil'></i></td></a></tr>";
                                }
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
