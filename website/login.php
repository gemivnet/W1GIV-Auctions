<?php
session_start();

// Redirect the user to the homepage if they are already logged in
if (isset($_SESSION['email'])) {
    header('Location: home.php');
}

include 'scripts/connect.php';

// Check for multiple and the user has selected which organization

if (isset($_POST['forgotEmail'])) {

    $email = $_POST['forgotEmail'];

    if ($stmt = $mysqli->prepare("SELECT COUNT(*), firstName, lastName FROM contacts WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($cnt, $firstName, $lastName);
        $stmt->fetch();
        $stmt->close();
    }

    if ($cnt > 0) {

        $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789!@#$%^&*()_-=+;:,.?";
        $pass = substr(str_shuffle($chars), 0, 8);
        $encryptPass = crypt($pass, '$6$rounds=25000$' . md5(uniqid(rand(), true)));

        if ($stmt = $mysqli->prepare("UPDATE contacts SET password = ?, shouldReset = 1 WHERE email = ?")) {
            $stmt->bind_param("ss", $encryptPass, $email);
            $stmt->execute();
            $stmt->close();
        }

        include_once('scripts/PHPMailer/class.phpmailer.php');
        require_once('scripts/PHPMailer/class.smtp.php');

        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@w1giv.com';
        $mail->Password = 'card2G5pQ';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->From = 'noreply@w1giv.com';
        $mail->FromName = 'W1GIV Auctions';
        $mail->addAddress($email, "$firstName $lastName");

        $mail->isHTML(true);

        $mail->Subject = 'Your W1GIV Auctions account password has been reset';
        $mail->Body = $firstName . ", The password to your W1GIV Auctions account has been reset. Your temporary password is <b>$pass</b><br><br>Upon logging in you will be requested to change your password.";

        $mail->send();
    }

    $success = "A new password has been emailed to you";
}

if (isset($_POST['organName'])) {

    if ($stmt = $mysqli->prepare("SELECT organizationID, email, firstName, lastName, title FROM contacts WHERE id=?")) {
        $stmt->bind_param("s", $_POST['contactID']);
        $stmt->execute();
        $stmt->bind_result($organizationID, $email, $firstName, $lastName, $title);
        $stmt->fetch();
        $stmt->close();
    }

    $_SESSION['id'] = $_POST['contactID'];
    $_SESSION['email'] = $email;
    $_SESSION['organizationID'] = $organizationID;
    $_SESSION['firstName'] = $firstName;
    $_SESSION['lastName'] = $lastName;
    $_SESSION['title'] = $title;
    $_SESSION['organization'] = $_POST['organName'];

    header('Location: home.php');
} else

// If the form has been submitted
if (isset($_POST['email'])) {

    // Check if the user is updating their password
    if (isset($_POST['confirmPass'])) {

        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $confirmPass = $_POST['confirmPass'];

        if ($pass != $confirmPass) {

            $error = "Your passwords do not match";
            $reset = true;
        } else {

            $encryptPass = crypt($pass, '$6$rounds=25000$' . md5(uniqid(rand(), true)));

            if ($stmt = $mysqli->prepare("UPDATE contacts SET password = ?, shouldReset = 0 WHERE email = ?")) {
                $stmt->bind_param("ss", $encryptPass, $email);
                $stmt->execute();
                $stmt->close();
            }

            $success = "Your password has been changed. You may now log in.";
        }
    } else {

        // The user is trying a regular login

        $email = $_POST['email'];
        $pass = $_POST['pass'];

        // Get the constant information about the user. # of organizations and password
        if ($stmt = $mysqli->prepare("SELECT COUNT(*), password, shouldReset FROM contacts WHERE email =?")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($count, $pword, $shouldReset);
            $stmt->fetch();
            $stmt->close();
        }

        // Before trying anything check if their password is valid
        if (crypt($pass, "$pword") == "$pword") {

            // Next see if they need to reset their password

            if ($shouldReset == 1) {
                $reset = true;
            } else {

                if ($count > 1) {
                    $multiple = true;
                } else {

                    // If the uot part of multiple organizations log them in normally
                    if ($stmt = $mysqli->prepare("SELECT id, organizationID, firstName, lastName, title FROM contacts WHERE email=?")) {
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->bind_result($id, $organizationID, $firstName, $lastName, $title);
                        $stmt->fetch();
                        $stmt->close();
                    }

                    $_SESSION['id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['organizationID'] = $organizationID;
                    $_SESSION['firstName'] = $firstName;
                    $_SESSION['lastName'] = $lastName;
                    $_SESSION['title'] = $title;

                    if ($stmt = $mysqli->prepare("SELECT name FROM organizations WHERE id=?")) {
                        $stmt->bind_param("s", $organizationID);
                        $stmt->execute();
                        $stmt->bind_result($organization);
                        $stmt->fetch();
                        $stmt->close();
                    }

                    $_SESSION['organization'] = $organization;

                    header('Location: home.php');
                }
            }
        } else {
            $error = "Username or password is invalid";
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>W1GIV Auctions</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">

                <a href="/"><b>W1GIV</b> Auctions</a>
            </div>

<?php
// Begin login HTML. First check if the user needs to reset their password

if (isset($_GET['f'])) :
    ?>

                <div class="login-box-body">
                    <p class="login-box-msg">Please enter your email address </p>

                    <form action="/login.php" method="post">

                        <div class="form-group has-feedback">
                            <input type="email" class="form-control" placeholder="Email" name="forgotEmail" required>
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        </div>

                        <div class="row">
                            <div class="col-xs-offset-8 col-xs-4">
                                <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
                            </div>
                        </div>
                    </form>

<?php endif;

if ($reset) :
    ?>

                    <div class="login-box-body">
                        <p class="login-box-msg">Please enter a new password <?php if (isset($error)) {
        echo "<div class='alert alert-danger' role='alert'>$error</div>";
    } ?> </p>

                        <form action="/login.php" method="post">
                            <div class="form-group has-feedback">
                                <input type="password" class="form-control" placeholder="Password" name="pass">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>
                            <div class="form-group has-feedback">
                                <input type="password" class="form-control" placeholder="Confirm Password" name="confirmPass">
                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            </div>

                            <input type="hidden" name="email" value="<?php echo $email; ?>">

                            <div class="row">
                                <div class="col-xs-offset-8 col-xs-4">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
                                </div>
                            </div>
                        </form>

<?php
endif;

//Next check for multiple



if ($multiple) :
    ?>

                        <div class="login-box-body">
                            <p class="login-box-msg">Choose the organization you wish to log in as

                        <?php
                        if ($stmt = $mysqli->prepare("SELECT organizations.name, contacts.id FROM organizations, contacts WHERE contacts.organizationID = organizations.id AND contacts.email = ?")) {
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $stmt->bind_result($organName, $contactID);
                            ?>


                                    <?php
                                    while ($stmt->fetch()) {
                                        ?>

                                    <form class="form-inline" method="post" action="login.php">

                                        <input type="hidden" name="organName" value="<?php echo $organName; ?>">
                                        <input type="hidden" name="contactID" value="<?php echo $contactID; ?>">

                                        <div class="row">
                                            <div class="form-group col-xs-9">
                                                <label><?php echo $organName; ?></label>
                                            </div>
                                            <div class="form-group col-xs-3">
                                                <button type="submit" class="btn btn-default">Select</button>
                                            </div>
                                        </div>
                                    </form>
            <?php
        }

        $stmt->close();
    }
    ?>

                        </div><!-- /.login-box-body -->

<?php
endif;

// Display normal login

if (!$reset & !$multiple & empty($_GET['f'])) :
    ?>

                        <div class="login-box-body">
                            <p class="login-box-msg">Sign in to view your auction control panel

                            <?php
                            if (isset($error)) {
                                echo "<div class='alert alert-danger' role='alert'>$error</div>";
                            }
                            if (isset($success)) {
                                echo " <div class='callout callout-success'>$success</div>";
                            }
                            ?>

                            </p>

                            <form action="login.php" method="post">
                                <div class="form-group has-feedback">
                                    <input type="email" class="form-control" placeholder="Email" name="email">
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="password" class="form-control" placeholder="Password" name="pass">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <div class="col-xs-offset-8 col-xs-4">
                                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                                    </div><!-- /.col -->
                                </div>
                            </form>

                            <p><a href="register.php">Register</a> a new organization</p>
                            <p>
                                <a href="login.php?f=t">Forgot Password?</a>
                            </p>
                        </div><!-- /.login-box-body -->

<?php endif; ?> 

                </div><!-- /.login-box -->

                <!-- jQuery 2.1.4 -->
                <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
                <!-- Bootstrap 3.3.5 -->
                <script src="../../bootstrap/js/bootstrap.min.js"></script>
                <!-- iCheck -->
                <script src="../../plugins/iCheck/icheck.min.js"></script>
                <script>
                    $(function () {
                        $('input').iCheck({
                            checkboxClass: 'icheckbox_square-blue',
                            radioClass: 'iradio_square-blue',
                            increaseArea: '20%' // optional
                        });
                    });
                </script>
                </body>
                </html>