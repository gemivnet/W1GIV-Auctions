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

if ($_GET['csv'] == "true") {

    header('Content-Type: text/csv; charset=utf-8');
    $t = time();
    header("Content-Disposition: attachment; filename=auction-$sessionAuctionID-$t.csv");

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Buyer Number', 'Seller Number', 'Description', 'Price'));

    $rows = $mysqli->query("SELECT buyer, seller, description, price FROM items WHERE auctionID = '$sessionAuctionID'");

    while ($row = mysqli_fetch_assoc($rows)) {

        fputcsv($output, $row);
    }

    fclose($output);

    exit();
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
                Auction Settings
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li class="active" ><a href="/home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Auction Settings</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">




        
            <div class="row">

                <div class="col col-md-6">

                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Export Items</h3>
                        </div>
                        <div class="box-body">

                            <a class="btn btn-primary" href="auctionSettings.php?id=<?php echo $sessionAuctionID; ?>&csv=true">CSV</a>

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                </div>

            </div>


        </section><!-- /.content -->
    </div><!-- /.container -->
</div><!-- /.content-wrapper -->

<?php
$mysqli->close();
include 'includes/footer.php';
?>
