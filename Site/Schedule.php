<?php
	//Loading Includes
	include('includes/session.php');
	include('includes/log.php');
	include('includes/protection.php');
	include('includes/api.php');
	if ($_SESSION['employeed']  ==  'f' || !$_SESSION['employeed']) {
		header('Location: /Login');
	}
	o_log('Page Loaded');
	$title = 'Schedule';

	$eventFeed = viewSchedule();
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- For Mobile scaling -->
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <meta name="HandheldFriendly" content="true">
        <!-- BrowserIcon -->
        <link rel="icon" type="image/ico" href="/logo.png">
        <!-- Latest compiled and minified CSS -->
        <link type="text/css" rel="stylesheet" href="/bootstrap/4.0.0/css/bootstrap.min.css">
        <!-- Moment -->
        <script type="text/javascript" src="/js/calendar/moment.min.js"></script>
        <!-- jQuery library -->
        <script type="text/javascript" src="/js/jquery/jquery-1.12.4.min.js"></script>
        <!-- bootstrap bundle -->
        <script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
        <!-- popper -->
        <script type="text/javascript" src="https://unpkg.com/popper.js@1.12.9/dist/umd/popper.js"></script>
        <!-- Latest compiled JavaScript -->
        <script type="text/javascript" src="/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <!-- Our CSS -->
        <link type="text/css" rel="stylesheet" href="/css/life-coach.css">
        <!-- Calendar CSS -->
        <link type="text/css" rel="stylesheet" href="/css/fullcalendar.min.css">
        <!-- calendar -->
        <script type="text/javascript" src="/js/calendar/fullcalendar.min.js"></script>
        <title><?php echo($title); ?></title>
        <script type="text/javascript">
			$(document).ready(function() {
				// page is now ready, initialize the calendar...
				$('#calendar').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay,listWeek'
					},
					scrollTime: '08:00:00',
					eventLimit: true, // allow "more" link when too many events
					navLinks: true,
					events: <?php echo($eventFeed); ?>
				})
				var moment = $('#calendar').fullCalendar('getDate');
				$('#current-date').text("Today is " + moment.format("dddd, MMMM Do YYYY"));
			});
			$('#today-btn').click(function() {
				$('#calendar').fullCalendar('today');
			});
		</script>
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
                    <li class="nav-item active">
                        <a class="nav-link" href="/Schedule">Schedule <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Clients">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Coaches">Coaches</a>
                    </li>
                </ul>
                <!--        I changed this to align the logout to the right-->
                <ul class="nav navbar-nav navbar-right">
                	<?php
						 if ($_SESSION['admin'] == 'true') {
							echo('<li class="nav-item">
								<a class="nav-link" href="'.getCompanyLink().'">Manage Company</a>
							</li>');
						}
						if ($_SESSION['supervisor'] == 't') {
							echo('<li class="nav-item right-marigin40p">
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
                        <div class="card-header title" id="current-date">
                        </div>
                    </div>
                </div>
            </div>
            <div class = "row">
                <div class="col-sm-12">
                    <div class="card text-center page-margin0 left right">
                        <div class="card-body" id='calendar'>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <br/>

        <p class="footerText">
            Copyright &copy; 2017 No Rights Reserved.
            <br>
            Abroad Squad + Chris
        </p>
    </body>
</html>