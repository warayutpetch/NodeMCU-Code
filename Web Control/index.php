<!doctype html>
<html><head>
<meta charset="utf-8">
<title>Login Solenoid Web Control</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="Carlos Alvarez - Alvarez.is">

<!-- Le styles -->
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />

<link href="css/login.css" rel="stylesheet">

<script type="text/javascript" src="js/jquery.js"></script>    
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

<style type="text/css">
	body {
		padding-top: 30px;
	}

	pbfooter {
		position:relative;
	}
</style>

</head>

<style>

	.pbfooter {
		position:relative;
	}

	span {
		font-size: 100%;

	}
	h3{

		color: white;
	}


</style>

<body style="background:url('images/bg.JPG') no-repeat center center; height:700px;">


	<div class="container">
		<div class="row">
			<div class="col-lg-offset-4 col-lg-4" style="margin-top:100px">
				<div class="block-unit" style="text-align:center; padding:8px 8px 8px 8px;">
					<img src="images/rt.gif" alt="" class="img-circle">
					<br></br><h3 >Web Control Selonoid</h3>
					<br>
					<br>
					<form class="frmlogin" method="post" action="login_ck.php">
						<fieldset>
							<p>
								<input id="Username" name="Username" type="text" placeholder="Username">
								<input id="Password" name="Password" type="password" placeholder="Password">
							</p>
							<input class="submit btn-success btn btn-large" type="submit" value="Login">
							<input class="reset btn-success btn btn-large" type="reset" value="Reset">
						</fieldset>
					</form>
				</div>

			</div>


		</div>
	</div>



    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="js/bootstrap.js"></script>
    

</body></html>