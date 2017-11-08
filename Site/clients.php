<?php
	include('includes/log.php');
	include('includes/session.php');
	include('includes/api.php');
	include('includes/protection.php');
	if (!$_SESSION['personid']) {
        header('Location: /Login');
    }
	o_log('Page Loaded');
	$title = 'Clients';

	if ($_GET['c'] == 'all') {
		$clientList = viewClients('all');
	} else if ($_GET['c'] == 'mine') {
		$clientList = viewClients('mine');
	} else {
		$clientList = viewClients('mine');
	}
	
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- For Mobile scaling -->
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <meta name="HandheldFriendly" content="true">
        <!-- Latest compiled and minified CSS -->
        <link type="text/css" rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <!-- Our CSS -->
        <link type="text/css" rel="stylesheet" href="/css/life-coach.css">
        <title><?php echo($title); ?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-blue">
            <a class="navbar-brand" href="/"><img src="/logo.png" width="50" height="50" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Schedule">Schedule</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/Clients">Clients<span class="sr-only">(current)</span></a>
                    </li>
                </ul>
                <!--        I changed this to align the logout to the right-->
                <ul class="nav navbar-nav navbar-right">
                    <li class="nav-item">
                        <a class="nav-link" href="/Profile">Profile</a>
                    </li>
                    <li class="nav-item">
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

        <div class="container">
            <div class ="row">
                <div class="col-sm-12">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-header title">
                            Client Portal
                        </div>
                    </div>
                </div>
            </div>
            <div class = "row">
                <div class="col-sm-7">
                    <div class="card text-center page-margin5 left">
                        <div class="card-header title">
                            <div class = "row">
                                <div class = "col-sm-2 right-marigin5p">
                                    <a href="/Clients/?c=mine" class="btn btn-primary">My Clients</a>
                                </div>
                                <div class = "col-sm-2 right-marigin5p">
                                    <a href="/Clients/?c=all" class="btn btn-primary">All Clients</a>
                                </div>
                                <div class = "col-sm-7">
                                    <form class="form-inline my-2 my-lg-0">
                                        <input class="form-control mr-sm-2" type="search" placeholder="Search">
                                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card text-center page-margin5 left">
                        <div class="card-header title"> Client List </div>
                        <div class="card-body">
                           <?php echo($clientList); ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="card text-center page-margin5 right">
                        <div class="card-header title">Client </div>
                        <div class="card-body">
                            I don't know if this is worth it since the others are links.<br />
                            <-----------------
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>