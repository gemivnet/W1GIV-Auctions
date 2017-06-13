<?php
session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <title>W1GIV Auction</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="../../plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker-bs3.css">
        <link rel="stylesheet" href="../../plugins/iCheck/all.css">
        <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="../../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        
        <style>
            body

            .panel
            {
                text-align: center;
            }
            .panel:hover { box-shadow: 0 1px 5px rgba(0, 0, 0, 0.4), 0 1px 5px rgba(130, 130, 130, 0.35); }
            .panel-body
            {
                padding: 0px;
                text-align: center;
            }

            .the-price
            {
                background-color: rgba(220,220,220,.17);
                box-shadow: 0 1px 0 #dcdcdc, inset 0 1px 0 #fff;
                padding: 20px;
                margin: 0;
            }

            .the-price h1
            {
                line-height: 1em;
                padding: 0;
                margin: 0;
            }

            .subscript
            {
                font-size: 25px;
            }

            /* CSS-only ribbon styles    */
            .cnrflash
            {
                /*Position correctly within container*/
                position: absolute;
                top: -9px;
                right: 4px;
                z-index: 1; /*Set overflow to hidden, to mask inner square*/
                overflow: hidden; /*Set size and add subtle rounding  		to soften edges*/
                width: 100px;
                height: 100px;
                border-radius: 3px 5px 3px 0;
            }
            .cnrflash-inner
            {
                /*Set position, make larger then 			container and rotate 45 degrees*/
                position: absolute;
                bottom: 0;
                right: 0;
                width: 145px;
                height: 145px;
                -ms-transform: rotate(45deg); /* IE 9 */
                -o-transform: rotate(45deg); /* Opera */
                -moz-transform: rotate(45deg); /* Firefox */
                -webkit-transform: rotate(45deg); /* Safari and Chrome */
                -webkit-transform-origin: 100% 100%; /*Purely decorative effects to add texture and stuff*/ /* Safari and Chrome */
                -ms-transform-origin: 100% 100%;  /* IE 9 */
                -o-transform-origin: 100% 100%; /* Opera */
                -moz-transform-origin: 100% 100%; /* Firefox */
                background-image: linear-gradient(90deg, transparent 50%, rgba(255,255,255,.1) 50%), linear-gradient(0deg, transparent 0%, rgba(1,1,1,.2) 50%);
                background-size: 4px,auto, auto,auto;
                background-color: #aa0101;
                box-shadow: 0 3px 3px 0 rgba(1,1,1,.5), 0 1px 0 0 rgba(1,1,1,.5), inset 0 -1px 8px 0 rgba(255,255,255,.3), inset 0 -1px 0 0 rgba(255,255,255,.2);
            }
            .cnrflash-inner:before, .cnrflash-inner:after
            {
                /*Use the border triangle trick to make  				it look like the ribbon wraps round it's 				container*/
                content: " ";
                display: block;
                position: absolute;
                bottom: -16px;
                width: 0;
                height: 0;
                border: 8px solid #800000;
            }
            .cnrflash-inner:before
            {
                left: 1px;
                border-bottom-color: transparent;
                border-right-color: transparent;
            }
            .cnrflash-inner:after
            {
                right: 0;
                border-bottom-color: transparent;
                border-left-color: transparent;
            }
            .cnrflash-label
            {
                /*Make the label look nice*/
                position: absolute;
                bottom: 0;
                left: 0;
                display: block;
                width: 100%;
                padding-bottom: 5px;
                color: #fff;
                text-shadow: 0 1px 1px rgba(1,1,1,.8);
                font-size: 0.95em;
                font-weight: bold;
                text-align: center;
            }
        </style>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body class="hold-transition skin-blue sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="home.php" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->

                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>W1GIV</b> Auctions</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="home.php">Home</a></li>
                            <li class="active"><a href="organization.php">Organization</a></li>
                        </ul>
                        </li>
                        </ul>

                    </div><!-- /.navbar-collapse -->

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->

                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                                    <span><?php echo $_SESSION['firstName'] . " " . $_SESSION['lastName']; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">

                                        <p>
                                            <?php echo $_SESSION['firstName'] . " " . $_SESSION['lastName'] . " - " . $_SESSION['title']; ?>
                                            <small><?php echo $_SESSION['organization']; ?></small>
                                        </p>
                                    </li>

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <form method="post" action="login.php">
                                                <button type="submit" class="btn btn-default btn-flat">Switch Organization</button>
                                            </form>
                                        </div>
                                        <div class="pull-right">
                                            <a href="scripts/logout.php" class="btn btn-default btn-flat">Log Out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->          
                        </ul>
                    </div>
                </nav>
            </header>
