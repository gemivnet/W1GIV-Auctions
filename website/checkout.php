<?php
session_start();

// Check that the user is logged in. If not redirect them to the login page
if (!isset($_SESSION['email'])) {

    header('Location: /login.php');
}

include 'scripts/connect.php';

$sessionAuctionID = $_GET['id'];

//Check that the auction that is being managed belongs to the organization
if ($stmt = $mysqli->prepare("SELECT * FROM auctions WHERE organizationID = ? AND id = ?")) {

    $stmt->bind_param("ss", $_SESSION['organizationID'], $sessionAuctionID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {

        header('Location: home.php');
    }

    $stmt->close();
}

// Check to see if an attendee number is set. If so, gather all information on the attendee.
if ($_POST['number'] != "") {

    //Set the number equal to an easier variable to use
    $number = $_POST['number'];

    // Check to see if that attendee number exists. Also combine cash and check to get total paid.	
    if ($stmt = $mysqli->prepare("SELECT COUNT(*), cash, chk, name, address, phone, recieveRecieptEmail, additional, email FROM attendees WHERE auctionID = ? AND number = ?")) {
        $stmt->bind_param("ii", $sessionAuctionID, $number);
        $stmt->execute();
        $stmt->bind_result($amt, $cashPaid, $checkPaid, $attendeeName, $attendeeAddress, $attendeePhone, $recieptEmail, $additional, $attendeeEmail);
        $stmt->fetch();
        $stmt->close();

        if ($amt == 0) {
            $error = "Attendee number not found";
        }

        if ($cashPaid == 1 || $checkPaid > 0) {
            $paid = true;
        } else {
            $paid = false;
        }
    } // End does attendee exist and paid if
    // Set the user paid amount to 0 if the set unpaid button was pressed
    if ($_POST['unpaid'] == 'true') {

        if ($stmt = $mysqli->prepare("UPDATE attendees SET chk=0, cash=0 WHERE auctionID=? AND number=?")) {
            $stmt->bind_param("ii", $sessionAuctionID, $number);
            $stmt->execute();
            $stmt->close();
        }

        if ($stmt = $mysqli->prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
            $action = "marked number $number as unpaid.";
            $stmt->bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
            $stmt->execute();
            $stmt->close();
        }

        $paid = false;
    } // End unpaid button if
    // Check if the checkout form was filled
    if (isset($_POST['check']) || isset($_POST['cash'])) {

        // Set checkout posts to easier variables
        $cash = $_POST['cash'];
        $check = $_POST['check'];
        $checkNumber = $_POST['checkNumber'];

        if ($cash == "on") {
            $cash = 1;
        } else {
            $cash = 0;
        }

        if ($check == "on") {
            $check = $checkNumber;
        } else {
            $check = 0;
        }

        if ($stmt = $mysqli->prepare("UPDATE attendees SET cash=?, chk=? WHERE auctionID=? AND number=?")) {
            $stmt->bind_param("ddii", $cash, $check, $sessionAuctionID, $number);
            $stmt->execute();
            $stmt->close();
        }

        $paid = true;
        $cashPaid = $cash;
        $checkPaid = $check;
        $sendEmail = true;

        if ($stmt = $mysqli->prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
            $action = "marked number $number as paid.";
            $stmt->bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
            $stmt->execute();
            $stmt->close();
        }
    } // End Checkout form if
    // Get basic auction settings
    if ($stmt = $mysqli->prepare("SELECT baseCommission, minimumCommission FROM commissionSettings WHERE auctionID = ?")) {
        $stmt->bind_param("i", $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($base, $minimum);
        $stmt->fetch();
        $stmt->close();

        $base /= 100;
    } // End auction settings
} // End is auction managed by this organization if

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
    <div class="container">

        <!-- Title and Breadcrumbs-->
        <section class="content-header">
            <h1>Checkout
<?php
if (isset($number)) {
    echo " - Attendee " . $number;
}
?>
            </h1>
            <ol class="breadcrumb">
                <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Checkout</li>
            </ol>
        </section><!-- End Title and Breadcrumbs-->

        <section class="content">

            <div class="row">


                <?php
                if ($status == "In Progress") :
                    ?>

                    <!-- Enter Attendee Number -->
                    <div class="col-md-4">

                        <div class="box box-default">
                            <div class="box-body">

                                <p>Enter an attendees number to view their reciept and mark as paid, or <a href="/checkout.php?id=<?php echo $sessionAuctionID; ?>&overview=true">view overview</a>.</p>

                    <?php if (isset($error) & $number != 0) {
                        echo " <div class='callout callout-danger'>$error</div>";
                    } ?>

                                <form class="form-inline" method="post" action="checkout.php?id=<?php echo $sessionAuctionID; ?>">

                                    <div class="form-group ">
                                        <label for="number" class="control-label">Attendee Number </label>
                                        <input type="text" class="form-control" id="number" name="number" placeholder="Number" required>
                                        <button class="btn btn-primary">Lookup</button>
                                    </div>

                                </form>


                            </div>
                        </div>

<?php endif;
if ($status == "Pending") : ?>

                        <div class="callout callout-warning">
                            <p>Checkout is not available while the auction is pending.</p>
                        </div>

<?php elseif ($status == "Completed") : ?>

                        <div class="callout callout-warning">
                            <p>Checkout is not available once the auction has been completed.</p>
                        </div>

                    <?php endif; ?>

                </div><!-- End Attendee Number Column-->			


                <!-- Begin payment tools and invoice if number is valid -->				  
                    <?php if ($number != "" && !isset($error)) : ?>

    <?php
    // Get total amount attendee bought
    if ($stmt = $mysqli->prepare("SELECT SUM(price) FROM items WHERE buyer = ? AND auctionID = ?")) {
        $stmt->bind_param("ii", $number, $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($bought);
        $stmt->fetch();
        $stmt->close();

        if ($bought == "") {
            $bought = 0;
        }
    }// End total bought if
    // Get total amoutn attendee sold and commission
    if ($stmt = $mysqli->prepare("SELECT price FROM items WHERE seller = ? AND auctionID = ?")) {
        $stmt->bind_param("ii", $number, $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($soldPrice);

        while ($stmt->fetch()) {

            $sold += $soldPrice;

            if ($soldPrice <= $minimum) {
                $commission += $soldPrice;
            } else if ($soldPrice * $base <= $minimum) {
                $commission += $minimum;
            } else {
                $commission += $soldPrice * $base;
            }
        }

        $stmt->close();
    }// End total sold if

    $total = $bought - ($sold - $commission);
    ?>

                    <?php if (!$paid) : ?>		

                        <!-- Begin Amount Owed col-->
                        <div class="col-md-4">

                            <div class="box box-default">
                                <div class="box-body">

                                    <div class="info-box">
                                        <span class="info-box-icon bg-<?php if ($total < 0) {
                    echo "green";
                } else {
                    echo "red";
                } ?>"><i class="fa fa-money"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"><?php if ($total < 0) {
                    echo "We owe them";
                } else {
                    echo "They owe us";
                } ?></span>
                                            <span class="info-box-number"><?php if ($total < 0) {
                    echo money_format("$%i", -1 * $total);
                } else {
                    echo money_format("$%i", $total);
                } ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div><!-- End amount owed col-->

    <?php endif;

    if ($total != 0) :
        ?>

                        <div class="col-md-4">
                            <div class="box box-default">
                                <div class="box-body">

        <?php if ($paid == false) : ?>

                                        <!-- Checkout Form -->
                                        <form class="form" method="POST" action="checkout.php?id=<?php echo $sessionAuctionID; ?>">
                                            <input type="hidden" name="number" value="<?php echo $number; ?>">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <input type="checkbox" class="flat-red" name="cash">
                                                    <label>Cash </label>
                                                    | 
                                                    <input type="checkbox" class="flat-red" name="check">
                                                    <label> Check</label>
                                                </span>
                                                <input type="text" class="form-control" name="checkNumber" placeholder="Check Number">
                                            </div>
                                            <br>
                                            <a href="includes/invoice.php?print=true&id=<?php echo $sessionAuctionID; ?>&number=<?php echo $number; ?>&total=<?php echo $total; ?>&bought=<?php echo $bought; ?>&sold=<?php echo $sold; ?>&commission=<?php echo $commission; ?>&paid=<?php echo $paid; ?>" target="_blank" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Print</a>
                                            <button class = "btn btn-success" type="submit">Mark as Paid</button>	
                                        </form><!-- End Checkout Form-->

        <?php else: ?>

            <?php
            if ($total < 0) {
                echo "<p>This transaction is complete. " . money_format("$%i", -1 * $total) . " was paid to the attendee.</p>";
            } else {
                echo "<p>This transaction is complete. " . money_format("$%i", $total) . " was received from the attendee.</p>";
            }
            ?>


                                        <form method="post" action="checkout.php?id=<?php echo $sessionAuctionID; ?>">
                                            <input type="hidden" name="number" value="<?php echo $number; ?>">
                                            <input type="hidden" name="unpaid" value="true">

                                            <button class="btn btn-danger">Mark as Unpaid</button>               <a href="includes/invoice.php?print=true&id=<?php echo $sessionAuctionID; ?>&number=<?php echo $number; ?>&total=<?php echo $total; ?>&bought=<?php echo $bought; ?>&sold=<?php echo $sold; ?>&commission=<?php echo $commission; ?>&paid=<?php echo $paid; ?>" target="_blank" class="btn btn-primary pull-right"><i class="fa fa-print"></i> Print</a>

                                        </form>


        <?php endif; ?>

                                </div>
                            </div>
                        </div>



    <?php endif; ?>


                </div> <!-- End of top row-->

    <?php
    if ($total != 0) {
        if ($stmt = $mysqli->prepare("SELECT address, phone FROM organizations WHERE id = ?")) {
            $stmt->bind_param("i", $_SESSION['organizationID']);
            $stmt->execute();
            $stmt->bind_result($address, $phone);
            $stmt->fetch();
            $stmt->close();
        }

        include 'includes/invoice.php';
    }
    ?>

            <?php endif; ?>

            <?php if ($_GET['overview'] == "true") : ?>
        </div>
        <div class="box box-default">
            <div class="box-body">

                The following attendees have not yet paid.

                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>

    <?php
    if ($stmt = $mysqli->prepare("SELECT DISTINCT attendees.name, attendees.number FROM attendees, (SELECT buyer, seller FROM items WHERE auctionID = ?) As a WHERE attendees.auctionID = ? AND (attendees.number = a.buyer OR attendees.number = a.seller) AND (attendees.chk = 0 AND attendees.cash = 0)")) {
        $stmt->bind_param("ii", $sessionAuctionID, $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($name, $number);

        while ($stmt->fetch()) {
            echo "<tr><td>$number</td><td>$name</td></tr>";
        }
    }
    ?>

                    </tbody>
                </table>

            </div>
        </div>

                    <?php endif; ?>


                    <?php if ($number == 0 & isset($number)) : ?>
    </div>
    <div class="box box-default">
        <div class="box-body">

            <div class="row">

        <?php
        if ($stmt = $mysqli->prepare("SELECT SUM(price) FROM items WHERE auctionID=? AND seller='0'")) {
            $stmt->bind_param("i", $sessionAuctionID);
            $stmt->execute();
            $stmt->bind_result($total);
            $stmt->fetch();
            $stmt->close();
        }
        ?>

                <div class="col-md-8">

                    <table id="itemTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>

    <?php
    if ($stmt = $mysqli->prepare("SELECT buyer, description, price FROM items WHERE auctionID = ? AND seller = '0' ORDER BY id DESC")) {
        $stmt->bind_param("i", $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($buyer, $description, $price);

        while ($stmt->fetch()) {

            echo "<tr><td>$buyer</td>><td>$description</td><td>" . money_format("$%i", $price) . "</td></tr>";
        }

        $stmt->close();
    }
    ?>

                        </tbody>
                    </table>

                </div>

                <div class="col-md-4">

                    <div class="box box-default">
                        <div class="box-body">

                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Club Item Profit</span>
                                    <span class="info-box-number"><?php echo money_format("$%i", $total); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><!-- End amount owed col-->

            </div>

        </div>
    </div>

<?php endif; ?>

</section><!-- /.content -->
</div><!-- /.container -->
</div><!-- /.content-wrapper -->

<?php
if ($sendEmail & $recieptEmail) {

    include_once('scripts/PHPMailer/class.phpmailer.php');
    require_once('scripts/PHPMailer/class.smtp.php');

    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'HOST';
    $mail->SMTPAuth = true;
    $mail->Username = 'EMAIL';
    $mail->Password = 'PASSWORD';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->From = 'noreply@w1giv.com';
    $mail->FromName = 'W1GIV Auctions';
    $mail->addAddress($attendeeEmail, $attendeeName);


    $mail->isHTML(true);

    ob_start(); //STARTS THE OUTPUT BUFFER	
    include('includes/invoice.php');  //INCLUDES YOUR PHP PAGE AND EXECUTES THE PHP IN THE FILE
    $page = ob_get_contents();  //PUT THE CONTENTS INTO A VARIABLE
    ob_clean();  //CLEAN OUT THE OUTPUT BUFFER
    $mail->Subject = $_SESSION['organization'] . ' Auction Receipt';
    $mail->Body = $page;

    $mail->send();
}

$mysqli->close();
include 'includes/footer.php';
?>
