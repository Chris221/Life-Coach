<!--/**
 * Created by PhpStorm.
 * User: Marisa.Proscia1
 * Date: 11/5/2017
 * Time: 9:09 PM
 */-->
<!--/* not sure if this is right */-->

<?php
include('includes/log.php');
include('includes/session.php');
if (!$_SESSION['personid']) {
    header('Location: /Login');
}
o_log('Page Loaded');
$title = 'Schedule';
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
            <a class="navbar-brand" href="/index"><img src="/logo.png" width="50" height="50" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/index">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/schedule">Schedule <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/clients">Clients</a>
                    </li>
                </ul>
                <!--        I changed this to align the logout to the right-->
                <ul class="nav navbar-nav navbar-right">
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">Profile</a>
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
                            Today is Wednesday, November 8th.
                        </div>
                    </div>
                </div>
            </div>

            <div class = "row">
                <div class="col-sm-9">
                    <div class="card text-center page-margin5 left right">
                        <div class="card-body">
                            <div class="month roundCornerTop">
                                <ul>
                                    <li class="prev">&#10094;</li>
                                    <li class="next">&#10095;</li>
                                    <li>
                                        November<br>
                                        <span style="font-size:18px">2017</span>
                                    </li>
                                </ul>
                            </div>
                            <ul class="weekdays weekdaysLg">
                                <li>Mo</li>
                                <li>Tu</li>
                                <li>We</li>
                                <li>Th</li>
                                <li>Fr</li>
                                <li>Sa</li>
                                <li>Su</li>
                            </ul>

                            <ul class="days daysLg roundCornerBottom">
                                <li>  </li>
                                <li>  </li>
                                <li>  </li>
                                <li>1</li>
                                <li>2</li>
                                <li>3</li>
                                <li>4</li>
                                <li>5</li>
                                <li>6</li>
                                <li>7</li>
                                <li><span class="active">8</span></li>
                                <li>9</li>
                                <li>10</li>
                                <li>11</li>
                                <li>12</li>
                                <li>13</li>
                                <li>14</li>
                                <li>15</li>
                                <li>16</li>
                                <li>17</li>
                                <li>18</li>
                                <li>19</li>
                                <li>20</li>
                                <li>21</li>
                                <li>22</li>
                                <li>23</li>
                                <li>24</li>
                                <li>25</li>
                                <li>26</li>
                                <li>27</li>
                                <li>28</li>
                                <li>29</li>
                                <li>30</li>
                                <li>  </li>
                                <li>  </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="card text-center page-margin5 left right">
                        <div class="card-header title">
                            <div class="row">
                            <div class="col-sm-6">
                                <a href="#" class="btn btn-primary">Today</a>
                            </div>
                            <div class="col-sm-6">
                                <a href="#" class="btn btn-primary">New Event</a>
                            </div>
                            </div>


                        </div>
                        <div class="card-body">
                            <h4> Calendar Events will Appear Here in Chronological Order. </h4>
                            <br />
                            Today will bring user back to events for current date. New Event will allow user to add something to their calendar.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>