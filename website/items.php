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

if ($_GET['dupe']) {

    if ($stmt = $mysqli->prepare("SELECT description, price FROM items WHERE auctionID = ? ORDER BY id DESC LIMIT 1")) {
        $stmt->bind_param("i", $sessionAuctionID);
        $stmt->execute();
        $stmt->bind_result($description, $price);
        $stmt->fetch();
        $stmt->close();
        $error = "dupe";
    }
}

if (isset($_POST['submit'])) {

    $buyer = $_POST['buyer'];
    $seller = $_POST['seller'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if ($stmt = $mysqli->prepare("SELECT COUNT(*), chk, cash FROM attendees WHERE auctionID = ? AND number = ?")) {
        $stmt->bind_param("ii", $sessionAuctionID, $buyer);
        $stmt->execute();
        $stmt->bind_result($amt, $chk, $chk);
        $stmt->fetch();
        $stmt->close();

        if ($amt != 1) {

            $error = "Invalid Buyer Number";
        }

        if ($chk != 0 || $cash != 0) {
            $error = "Buyer has already checked out";
        }
    }

    if ($stmt = $mysqli->prepare("SELECT COUNT(*), chk, cash FROM attendees WHERE auctionID = ? AND number = ?")) {
        $stmt->bind_param("ii", $sessionAuctionID, $seller);
        $stmt->execute();
        $stmt->bind_result($amt, $chk, $chk);
        $stmt->fetch();
        $stmt->close();

        if ($amt != 1 & $seller != 0) {

            $error = "Invalid Seller Number";
        }

        if ($chk != 0 || $cash != 0) {
            $error = "Seller has already checked out";
        }
    }

    if ($price < 0) {
        $error = "Invalid Price";
    }

    if (!isset($error)) {

        if ($stmt = $mysqli->prepare("INSERT INTO items (auctionID, buyer, seller, description, price) VALUES (?, ?, ?, ?, ?)")) {
            $stmt->bind_param("iiiss", $sessionAuctionID, $buyer, $seller, $description, $price);
            $stmt->execute();
            $stmt->close();
        }

        $success = "Added <strong>'$description'</strong>, seller <strong>$seller</strong>, buyer <strong>$buyer</strong>, for <strong>" . money_format("$%i", $price) . "</strong>";

        if ($stmt = $mysqli->prepare("INSERT INTO logs (contactID, organizationID, auctionID, actn) VALUES (?, ?, ?, ?)")) {
            $action = "added item '$description', seller $seller, buyer $buyer for " . money_format("$%i", $price) . ".";
            $stmt->bind_param("iiis", $_SESSION['id'], $_SESSION['organizationID'], $sessionAuctionID, $action);
            $stmt->execute();
            $stmt->close();
        }
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
                Items
                <small>Manage Items</small>
            </h1>
            <ol class="breadcrumb">
                <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Items</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

<?php
if ($status == "In Progress") :
    ?>

                <div class="box box-default">
                    <div class="box-header with-border">

                        <h3 class="box-title">Add Items</h3>
                    </div>
                    <div class="box-body">
                <?php if (isset($success)) {
                    echo " <div class='callout callout-success'><h4>$success</h4></div>";
                } ?>
    <?php if (isset($error) & $error != "dupe") {
        echo " <div class='callout callout-danger'><h4>$error</h4></div>";
    } ?>
                        <form class="form-inline" method="post" action="items.php?id=<?php echo $sessionAuctionID; ?>">
                            <div class="form-group">
                                <label for="description">Description </label>
                                <input type="text" class="form-control" id="description" name="description" <?php if (isset($error)) {
        echo "value='$description'";
    } ?> placeholder="Description" required <?php if (!$_GET['dupe']) {
                        echo "autofocus";
                    } ?>>
                            </div>
                            <div class="form-group">
                                <label for="seller">Seller </label>
                                <input type="text" class="form-control" id="seller" name="seller" <?php if (isset($error)) {
                        echo "value='$seller'";
                    } ?> placeholder="Seller" required <?php if ($_GET['dupe']) {
                        echo "autofocus";
                    } ?>>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="price">Price</label>
                                <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    <input type="text" class="form-control" id="price" name="price" <?php if (isset($error)) {
                        echo "value='$price'";
                    } ?> placeholder="Price" required>
                                </div>
                                <div class="form-group">
                                    <label for="buyer">Buyer  </label>
                                    <input type="text" class="form-control" id="buyer" name="buyer" <?php if (isset($error)) {
                        echo "value='$buyer'";
                    } ?> placeholder="Buyer" required>
                                </div>



                                <button type="submit" name="submit" value="submit" class="btn btn-primary">Add Item</button>
                                <a href="/items.php?id=<?php echo $sessionAuctionID; ?>" class="btn btn-danger">Clear</a>
                                <a href="/items.php?id=<?php echo $sessionAuctionID; ?>&dupe=true" class="btn btn-success">Duplicate</a>

                        </form>

                    </div>	  
                    <br>
                    <p>
                        Enter a '0' as the seller for the organization to be the seller.
                    </p>

                </div><!-- /.box-body -->

        </div><!-- /.box -->

    <?php endif;
    if ($status == "Pending") : ?>

        <div class="callout callout-warning">
            <p>Adding items is not available while the auction is pending.</p>
        </div>

<?php elseif ($status == "Completed") : ?>

        <div class="callout callout-warning">
            <p>Adding items is not available once the auction has been completed.</p>
        </div>

<?php endif; ?>

    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Items</h3>
        </div>
        <div class="box-body">

            <table id="itemTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Buyer</th>
                        <th>Seller</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if ($stmt = $mysqli->prepare("SELECT id, buyer, seller, description, price FROM items WHERE auctionID = ? ORDER BY id DESC")) {
                        $stmt->bind_param("i", $sessionAuctionID);
                        $stmt->execute();
                        $stmt->bind_result($id, $buyer, $seller, $description, $price);

                        while ($stmt->fetch()) {

                            echo "<tr><td>$buyer</td><td>$seller</td><td>$description</td><td>" . money_format("$%i", $price) . "</td><td><a href='edit.php?id=$sessionAuctionID&item=$id'><i class='fa fa-pencil'></i></a></td></tr>";
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
