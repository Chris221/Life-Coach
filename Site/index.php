<?php
	include('includes/log.php');
	include('includes/session.php');
	if (!$_SESSION['personid']) {
		header('Location: /Login');
	}
	o_log('Page Loaded');
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
        <link type="text/css" rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <!-- Popper -->
        <script type="text/javascript" src="/js/popper.min.js"></script>
        <!-- Our CSS -->
        <link type="text/css" rel="stylesheet" href="/css/life-coach.css">
        <!-- Calendar CSS -->
        <link type="text/css" rel="stylesheet" href="/css/calendar/calendar.css">
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Schedule">Schedule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Clients">Clients</a>
                    </li>
                </ul>
        <!--        I changed this to align the logout to the right-->
                <ul class="nav navbar-nav navbar-right">
                	<?php
						if ($_SESSION['supervisor']) {
							echo('<li class="nav-item right-marigin50p">
								<a class="nav-link" href="/NewCoach">Add New Coach</a>
							</li>');
						}
					?>
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
                            <?php
                            //no idea why this works without the closing tag, but it does
                            echo ("<h4>Hello, ".$_SESSION['first_name'].' '.$_SESSION['last_name'].'!</h5');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class= "row">
                <div class="col-sm-6">
                    <div class="card text-center page-margin5 left">
                        <div class="card-header title">Daily Tasks</div>
                        <div class="card-body">
                            <h5 class="card-title">Daily tasks, reminders, and calendar events appear here.</h5>
                        </div>
                    </div>

                    <div class="card text-center page-margin5 left">
                        <div class="card-header title"> Recent Contact</div>
                        <div class="card-body">
                            <h5 class="card-title">Information regarding the most recent client contact will appear here.</h5>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card text-center page-margin5 right">
                            <div class="card-body">
                                <div class="cal">
                                    <div class="cal__header">
                                        <button class="btn btn-action btn-link btn-lg" data-calendar-toggle="previous"><svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"></path></svg></button>
                                        <div class="cal__header__label" data-calendar-label="month">
                                            March 2017
                                        </div><button class="btn btn-action btn-link btn-lg" data-calendar-toggle="next"> <svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path></svg></button>
                                    </div>
                                    <div class="cal__week">
                                        <span>Mon</span> <span>Tue</span><span>Wed</span> <span>Thu</span> <span>Fri</span> <span>Sat</span> <span>Sun</span>
                                    </div>
                                    <div class="cal__body" data-calendar-area="month"></div>
                                </div>

                                <p class="demo-picked">
                                    Date picked: <span data-calendar-label="picked"></span>
                                </p>
                            </div>
                    </div>

                    <div class="card text-center page-margin5 right">
                        <div class="card-header title"> Upcoming Events</div>
                        <div class="card-body">
                            <div class="wrapper">
                                <div id="organizerContainerSm"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <br/>

    <!-- Calendar JavaScript if it's not at the bottom it doesn't work -->
    <script type="text/javascript" src="/js/calendar/calendar.js"></script>
        <script>
            vanillacalendar.init();
        </script>

    <p class="footerText">
        Copyright &copy; 2017 No Rights Reserved.
        <br>
        Abroad Squad + Chris
    </p>

    </body>
</html>