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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Logo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Schedule</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Clients</a>
            </li>
<!--            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>-->
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

<div class="title">
    <?php
    echo("Hello, ".$_SESSION['first_name'].' '.$_SESSION['last_name'].'!');
    ?>
</div>


<br />
<br />
<br />
<br />
<a href="/NewCoach" >Add a New Coach</a>
<br />
<br />
<a href="/Logout" >Logout</a>
</body>
</html>