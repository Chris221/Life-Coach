<?php
	include('includes/session.php');
	if (!$_SESSION['personid']) {
		header('Location: /Login');
	}	
	$title = 'Home';
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<!-- For Mobile scaling -->
<meta name="viewport" content="width=device-width, user-scalable=no" />
<meta name="HandheldFriendly" content="true">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="/js/jquery/jquery-3.2.1.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Our CSS -->
<link rel="stylesheet" href="/css/life-coach.css">
<title><?php echo($title); ?></title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="navBar">
    <a class="navbar-brand" href="/index">Logo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="/index">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Schedule</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Clients</a>
            </li>
        </ul>
<!--        I changed this to align the logout to the right-->
        <ul class="nav navbar-nav navbar-right">
            <li class="van-item">
                <a class="nav-link" href="/Logout" >Logout</a>
            </li>
<!--            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>-->
        </ul>
<!--        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>-->
    </div>
</nav>
<br />

<div class="card text-center">
    <div class="card-header title">
        <?php
        echo("Hello, ".$_SESSION['first_name'].' '.$_SESSION['last_name'].'!');
        ?>
    </div>
    <div class="card-body">
        <h4 class="card-title">There will be info here</h4>
        <p class="card-text">lead into other stuff</p>
        <a href="/NewCoach" class="btn btn-primary">Add a New Coach</a>
    </div>
    <div class="card-footer text-muted">
        Footer Text
    </div>
</div>


</body>
</html>