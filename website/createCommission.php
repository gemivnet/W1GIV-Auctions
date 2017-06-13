<?php
session_start();
if (!isset($_SESSION['email'])) {

    header('Location: /login.php');
}

include 'scripts/connect.php';

if (isset($_POST['dates'])) {

    $dates = $_POST['dates'];
    $location = $_POST['location'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zipcode = $_POST['zipcode'];
    $state = $_POST['state'];
    $save = $_POST['save'];
    $additional = $_POST['additional'];
    $additionalName = $_POST['additionalName'];
    $baseCommission = $_POST['baseCommission'];
    $minimumCommission = $_POST['minimumCommission'];
    $entry = $_POST['entry'];

    if (!strpos($dates, "/") || !strpos($dates, "M")) {
        $datesError = "Invalid Date";
        $do = true;
    }
    if ($location == "new") {

        if ($name == "" || $address == "" || $city == "" || $zipcode == "") {
            $locationError = "Please complete all fields for new location.";
            $do = true;
        }
    }
    if ($additional == "on" && $additionalName == "") {
        $additionalError = "Please enter a name for additional registration.";
        $do = true;
    }
    if ($baseCommission == "" || $baseCommission < 0 || $baseCommission > 100) {
        $baseCommissionError = "Invalid base commission";
        $do = true;
    }
    if ($minimumCommission == "" || $minimumCommission < 0) {
        $minimumCommissionError = "Invalid minimum commission";
        $do = true;
    }

    if (!$do) {

        // Add New Location

        if ($location == "new") {

            if ($save == "on") {
                $save = 1;
            } else {
                $save = 0;
            }

            if ($stmt = $mysqli->prepare("INSERT INTO locations (organizationID, name, address, city, zipcode, state, save) VALUES (?, ?, ?, ?, ?, ?, ?)")) {

                $stmt->bind_param("isssssi", $_SESSION['organizationID'], $name, $address, $city, $zipcode, $state, $save);
                $stmt->execute();
                $stmt->close();

                $location = $mysqli->insert_id;
            }
        }

        // Create New Auction

        $endDate = substr($dates, strpos($dates, "-") + 2);
        $date = DateTime::createFromFormat('m/d/Y G:i a', $endDate);
        $endDate = $date->format('Y-m-d H:i:s');

        $datess = substr($dates, 0, 19);
        $date = DateTime::createFromFormat('m/d/Y G:i a', $datess);
        $datess = $date->format('Y-m-d H:i:s');

        if ($stmt = $mysqli->prepare("INSERT INTO auctions (organizationID, startDate, endDate, locationID, auctionType, entryFee) VALUES (?, ?, ?, ?, 'commission', ?)")) {
            $stmt->bind_param("issid", $_SESSION['organizationID'], $datess, $endDate, $location, $entry);
            $stmt->execute();
            $stmt->close();

            $auctionID = $mysqli->insert_id;

            if ($stmt = $mysqli->prepare("INSERT INTO commissionSettings (auctionID, additional, baseCommission, minimumCommission) VALUES (?, ?, ?, ?)")) {
                $stmt->bind_param("isss", $auctionID, $additionalName, $baseCommission, $minimumCommission);
                $stmt->execute();
                $stmt->close();

                header('Location: home.php');
            }
        }
    }
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
                <small>Commission Auction</small>
            </h1>
            <ol class="breadcrumb">
                <li ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active" ><a href="/createType.php">Create Auction</a></li>
                <li class="active" ><a href="/createCommission.php">Create Commission</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <form method="post" action="/createCommission.php">

                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Date</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->

                            <div class="box-body">

                                <p>
                                    Please choose the start time/date and the end time start/date. These do not need to be exact.
                                </p>
                                <div class="form-group">

<?php if (isset($datesError)) {
    echo " <div class='callout callout-danger'>$datesError</div>";
} ?>

                                    <label>Auction Start Time/Date and End Time/Date:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="reservationtime" <?php if ($do) {
    echo "value='$dates'";
} ?> name="dates" required>
                                    </div><!-- /.input group -->
                                </div><!-- /.form group -->
                            </div><!-- /.box-body -->

                        </div><!-- /.box -->

                        <!-- Form Element sizes -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Location</h3>
                            </div>
                            <div class="box-body">
                                <p>
                                    If this is your first auction please create a new location. If you have created an auction before you may used a stored location.
                                </p>
<?php if ($do & $location != "new") {
    echo " <div class='callout callout-danger'>Please choose location</div>";
} ?>
                                <div class="form-group">
                                    <label>Location</label>
                                    <select class="form-control" name="location">
                                        <option value="new">New Location</option>

<?php
if ($stmt = $mysqli->prepare("SELECT id, name FROM locations WHERE organizationID=? AND save='1'")) {

    $stmt->bind_param("s", $_SESSION['organizationID']);
    $stmt->execute();
    $stmt->bind_result($locationID, $locationName);

    while ($stmt->fetch()) {
        echo "<option value='" . $locationID . "'>" . $locationName . "</option>";
    }

    $stmt->close();
}
?>


                                    </select>
                                </div>

                                <p>
                                    The following only needs to be filled out if you have selected New Location.
                                </p>

<?php if (isset($locationError)) {
    echo " <div class='callout callout-danger'>$locationError</div>";
} ?>

                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-2 control-label">Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" <?php if ($do) {
    echo "value='$name'";
} ?> id="name" name="name" placeholder="Location Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address" class="col-sm-2 control-label">Address</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="address" <?php if ($do) {
    echo "value='$address'";
} ?> name="address" placeholder="Address">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="city" class="col-sm-2 control-label">City</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="city" <?php if ($do) {
    echo "value='$city'";
} ?> name="city" placeholder="City">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="zipcode" class="col-sm-2 control-label">Zip Code</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="zipcode" <?php if ($do) {
    echo "value='$zipcode'";
} ?> name="zipcode" placeholder="Zip Code">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="state" class="col-sm-2 control-label">State</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="state" name="state">
<?php if ($do) {
    echo "<option value='$state'>$state</option>";
} ?>
                                                <option value="Alabama">Alabama</option>
                                                <option value="Alaska">Alaska</option>
                                                <option value="Arizona">Arizona</option>
                                                <option value="Arkansas">Arkansas</option>
                                                <option value="California">California</option>
                                                <option value="Colorado">Colorado</option>
                                                <option value="Connecticut">Connecticut</option>
                                                <option value="Deleware">Deleware</option>
                                                <option value="Florida">Florida</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Hawaii">Hawaii</option>
                                                <option value="Idaho">Idaho</option>
                                                <option value="Illinois">Illinois</option>
                                                <option value="Indiana">Indiana</option>
                                                <option value="Iowa">Iowa</option>
                                                <option value="Kansas">Kansas</option>
                                                <option value="Kentucky">Kentucky</option>
                                                <option value="Louisiana">Louisiana</option>
                                                <option value="Maine">Maine</option>
                                                <option value="Maryland">Maryland</option>
                                                <option value="Massachusetts">Massachusetts</option>
                                                <option value="Michigan">Michigan</option>
                                                <option value="Minnesota">Minnesota</option>
                                                <option value="Mississippi">Mississippi</option>
                                                <option value="Missouri">Missouri</option>
                                                <option value="Montana">Montana</option>
                                                <option value="Nebraska">Nebraska</option>
                                                <option value="Nevada">Nevada</option>
                                                <option value="New Hampshire">New Hampshire</option>
                                                <option value="New Jersey">New Jersey</option>
                                                <option value="New Mexico">New Mexico</option>
                                                <option value="New York">New York</option>
                                                <option value="North Carolina">North Carolina</option>
                                                <option value="North Dakota">North Dakota</option>
                                                <option value="Ohio">Ohio</option>
                                                <option value="Oklahoma">Oklahoma</option>
                                                <option value="Oregon">Oregon</option>
                                                <option value="Pennsylvania">Pennsylvania</option>
                                                <option value="Rhode Island">Rhode Island</option>
                                                <option value="South Carolina">South Carolina</option>
                                                <option value="South Dakota">South Dakota</option>
                                                <option value="Tennessee">Tennessee</option>
                                                <option value="Texas">Texas</option>
                                                <option value="Utah">Utah</option>
                                                <option value="Vermont">Vermont</option>
                                                <option value="Virginia">Virginia</option>
                                                <option value="Washington">Washington</option>
                                                <option value="West Virginia">West Virginia</option>
                                                <option value="Wisconsin">Wisconsin</option>
                                                <option value="Wymoning">Wyoming</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="save" <?php if ($do && $save == "on") {
    echo "checked";
} ?> class="flat-red"> Save this location
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                    </div><!--/.col (left) -->
                    <!-- right column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Auction Settings</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <h4>Registration
                                </h4>
                                <p>
                                    Additional registration info allows your organization to collect additional information about auction attendees during registration. This may range from member ID to call sign. If you choose to enable additional registration info enter the name of the additional info into the box below. For example if you enter "Call Sign" during registration, registrants will be prompted for their "Call Sign".
                                </p>

<?php if (isset($additionalError)) {
    echo " <div class='callout callout-danger'>$additionalError</div>";
} ?>

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" class="flat-red" name="additional" <?php if ($do && $additional == "on") {
    echo "checked";
} ?>>
                                        <label> Enable Additional Registration Info</label>
                                    </span>
                                    <input type="text" class="form-control" name="additionalName" placeholder="Name" <?php if ($do) {
    echo "value='$additionalName'";
} ?>>
                                </div><!-- /input-group -->


                                <h4>
                                    Commissions
                                </h4>


                                <p>
                                    The base comission is the amount that your organization takes from each sale. If your organization does not take any cut of sales enter a "0". Please enter a numerical value from 0-100 with no percent sign. Decimal values are allowed.
                                </p>

<?php if (isset($baseCommissionError)) {
    echo " <div class='callout callout-danger'>$baseCommissionError</div>";
} ?>

                                <div class="input-group">
                                    <span class="input-group-addon">%</span>
                                    <input type="text" class="form-control" name="baseCommission" required placeholder="Base Commission" <?php if ($do) {
    echo "value='$baseCommission'";
} ?>>
                                </div>

                                <br>	   
                                <p>
                                    Minimum amount for commission is the minimum amount that an item needs to sell for in order for the seller to recieve money. If the item sells for less than this amount the organization will recieve all the money. If this does not apply for your organization enter $0.00
                                </p>

<?php if (isset($minimumCommissionError)) {
    echo " <div class='callout callout-danger'>$minimumCommissionError</div>";
} ?>

                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" name="minimumCommission" required class="form-control" <?php if ($do) {
    echo "value='$minimumCommission'";
} ?>>
                                    <span class="input-group-addon">.00</span>
                                </div>

                                <h4>
                                    Entry Fee
                                </h4>


                                <p>
                                    The entry fee is the amount it costs to recieve a buyer and seller number. This will be factored into the profit of the organization.
                                </p>

<?php if (isset($entryError)) {
    echo " <div class='callout callout-danger'>$entryError</div>";
} ?>

                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" name="entry" required class="form-control" <?php if ($do) {
    echo "value='$entry'";
} ?>>
                                    <span class="input-group-addon">.00</span>
                                </div>

                            </div><!-- /.box-body -->


                        </div><!-- /.box -->
                        <!-- general form elements disabled -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Payment Options</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <p>
                                    Payment options not available at this time
                                </p>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!--/.col (right) -->
                </div>   <!-- /.row -->

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Agreements</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required class="flat-red"> I agree to the <a href="#">Auction Creation Terms and Conditions</a>
                                </label>
                            </div>

                            <p>
                                Clicking "Create Auction" creates a legally binding contract between your organization and W1GIV Auctions to pay based on the agreed upon plan.	   
                            </p>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required class="flat-red"> I agree to the <a href="#">Payment Terms and Conditions</a>
                                </label>
                            </div>

                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <a class="btn btm-primary" href="/home.php">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right">Create Auction</button>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->
            </form>
        </section><!-- /.content -->
    </div><!-- /.container -->
</div><!-- /.content-wrapper -->

<?php
$mysqli->close();

include 'includes/footer.php';
?>
