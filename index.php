<?php

// Load Stripe
require('lib/Stripe.php');

// Load configuration settings
$config = require('config.php');

// Force https
//if ($config['test-mode'] && $_SERVER['HTTPS'] != 'on') {
    //header('HTTP/1.1 301 Moved Permanently');
    //header('Location: http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    //exit;
//}

if ($_POST) {
    Stripe::setApiKey($config['secret-key']);

    // POSTed Variables
    $token      = $_POST['stripeToken'];
    $first_name = $_POST['first-name'];
    $last_name  = $_POST['last-name'];
    $name       = $first_name . ' ' . $last_name;
    $address    = $_POST['address'] . "\n" . $_POST['city'] . ', ' . $_POST['state'] . ' ' . $_POST['zip'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $amount     = (float) $_POST['amount'];

    try {
        if ( ! isset($_POST['stripeToken']) ) {
            throw new Exception("The Stripe Token was not generated correctly");
        }

        // Charge the card
        $donation = Stripe_Charge::create(array(
            'card'        => $token,
            'description' => 'Donation by ' . $name . ' (' . $email . ')',
            'amount'      => $amount * 100,
            'currency'    => 'usd'
        ));

        // Build and send the email
        $headers = 'From: ' . $config['emaily-from'];
        $headers .= "\r\nBcc: " . $config['emaily-bcc'] . "\r\n\r\n";

        // Find and replace values
        $find    = array('%name%', '%amount%');
        $replace = array($name, '$' . $amount);

        $message = str_replace($find, $replace , $config['email-message']) . "\n\n";
        $message .= 'Amount: $' . $amount . "\n";
        $message .= 'Address: ' . $address . "\n";
        $message .= 'Phone: ' . $phone . "\n";
        $message .= 'Email: ' . $email . "\n";
        $message .= 'Date: ' . date('M j, Y, g:ia', $donation['created']) . "\n";
        $message .= 'Transaction ID: ' . $donation['id'] . "\n\n\n";

        $subject = $config['email-subject'];

        // Send it
        if ( !$config['test-mode'] ) {
            mail($email,$subject,$message,$headers);
        }

        // Forward to "Thank You" page
        header('Location: ' . $config['thank-you']);
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ElissaSoft</title>

    <!-- Bootstrap Core CSS - Uses Bootswatch Flatly Theme: http://bootswatch.com/flatly/ -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/ellisa.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="https://js.stripe.com/v2"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript">
        Stripe.setPublishableKey('<?php echo $config['publishable-key'] ?>');
    </script>
    <script type="text/javascript" src="script_donation.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top" class="index">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#page-top">ElissaSoft</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="page-scroll">
                        <a href="#page-top"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <li class="page-scroll">
                        <a href="app.php"><i class="fa fa-android"></i> Products</a>
                    </li>
                   
                   
					
					 <li class="page-scroll">
                        <a href="#"><i class="fa fa-tachometer"></i> Investors Corner</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 page-scroll legal-auto">
				 <div class="intro-text">
                        <span class="name">About Us</span>
                        <hr class="star-light">
                        <span class="skills">We are a young startup driven by big ambitions from young, smart  and strongly motivated engineers and designers. Our goal is to make a life changing impact in the world of mobile and desktop applications. We have what we strongly believe are big ideas and we are determined to bring them to reality.  But any help from you with good feedback or financial support, no matter how small it might be, will deeply appreciated. Please help us achieve our goals and our promise to you is we will help others achieves theirs as you did for us.</span>
                    </div>
                   
                   
                </div>
				<div class="col-lg-6 page-scroll">
				 <div class="intro-text">
                        <span class="name">Make Donation</span>
                        <hr class="star-light">
                        <span class="skills">Please select your donation method.</span>
                    </div>
					<div class="col-md-12 donations-buttons">
					<div class="col-md-4 col-xs-4 col-sm-4 donations">
                   <a href="#">
				   <img class="img-responsive paymenttype" data-toggle="modal" data-target="#myModal" data-type="visa" src="img/visa.png" alt="Visa"  >
				  </a>
				  </div>
				  <div class="col-md-4 col-xs-4 col-sm-4 donations">
				   <a href="#">
				  <img class="img-responsive paymenttype" data-toggle="modal" data-target="#myModal" data-type="mastercard" src="img/mastercard.png" alt="Master card">
				  </a>
				  </div>
				  <div class="col-md-4 col-xs-4 col-sm-4 donations">
				  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" id="pappal_form">
					
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="Z9727WQ2327SQ">
					</form>
					<a href="#">
					<img class="img-responsive paymenttype pappal_submit" data-type="pappal"  src="img/paypal.png" alt="Paypal">
					</a>
					</div>
                   </div>
                </div>
			</div>
        </div>
    </header>


  
<?php 
include "donation_model.php";
?>
   

    <!-- Footer -->
    <footer class="text-center">
        <div class="footer-above">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-4">
                        <h3>Location</h3>
                        <p>Place<br></p>
                    </div>
                    <div class="footer-col col-md-4">
                        <h3>Get In touch</h3>
                        <ul class="list-inline">
                            <li>
                                <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-facebook"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-google-plus"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-linkedin"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn-social btn-outline"><i class="fa fa-fw fa-dribbble"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-col col-md-4">
                        <h3>About ElissaSoft</h3>
                        <p> <a href=""></a>about</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-below">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        Copyright &copy;ElissaSoft 2016
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
    <div class="scroll-top page-scroll visible-xs visible-sm">
        <a class="btn btn-primary" href="#page-top">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="js/classie.js"></script>
    <script src="js/cbpAnimatedHeader.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/elissasoft.js"></script>
	 <script>
	 $(function() {
	 $('.pappal_submit').click(function(){
		 $("#pappal_form").submit();
	 });
    });
	$(document).on("input", ".numeric", function() {
		this.value = this.value.replace(/[^0-9\.]/g,'');
	});
	 </script>
 <script>if (window.Stripe) $('.donation-form').show()</script>
    <noscript><p>JavaScript is required for the donation form.</p></noscript>
</body>

</html>
