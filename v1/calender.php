<?php
  function build_calender($month,$year){

    $mysqli = new mysqli('localhost','root','','test_database');
    $stmt = $mysqli->prepare("SELECT * FROM booking WHERE MONTH(booking_date) = ? AND YEAR(booking_date) = ?");
    $stmt->bind_param('ss',$month,$year);
    $bookings = array();
    if($stmt->execute()){
      $result = $stmt->get_result();
      if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
          $bookings[] = $row['booking_date'];
        }
        $stmt->close();
      }
    }
    // $msg = "<div class='alert alert-success'>Booking Successfully</div>";
    // $stmt->close();
    // $mysqli->close();


    // first of all we'll create an array containing names of all day in a week
    $daysOfweek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saterday');

    // Then we'll get the first  day of the month  that is in the argument of this function
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    // now geting the number of day this month containes
    $numberOfDays = date('t',$firstDayOfMonth);

    //get some information about the first day of this month
    $dateComponents =  getdate($firstDayOfMonth);

    // geting the name of month

    $monthName = $dateComponents['month'];

    // geting the index  value 0-6 of the first day of month
    $dayOfweek = $dateComponents['wday'];

    //geting the current date
    $dateToday = date('Y-m-d');

    // now creating the html table

    $calender = "<table class='table table-bordered table-hover'>";
    $calender .= "<center><h2 class='display-4'>$monthName,$year</h2>";

    // next and previews month 

    $calender .= "<a class='btn btn-info btn-sm mx-3' href='?month=".date('m',mktime(0,0,0,$month-1,1,$year))."&year=".date('Y',mktime(0,0,0,$month-1,1,$year))."'>Previews Month</a>";

    $calender .= "<a class='btn btn-info btn-sm' href='?month=".date('m')."&year=".date('Y')."'>Current Month</a>";


    $calender .= "<a class='btn btn-info btn-sm mx-3' href='?month=".date('m',mktime(0,0,0,$month+1,1,$year))."&year=".date('Y',mktime(0,0,0,$month+1,1,$year))."'>Next Month</a> <br><br>";

    $calender .= "<tr>";

    // createing the calender headers
    foreach($daysOfweek as $day){
      $calender .="<th class='header bg-info text-white'>$day</th>";
    }
    $calender .= "</tr><tr>";

    // The variable dayofweek will make sure that there must be only 7 columns on our table 

    if($dayOfweek > 0){
      for($k = 0;$k<$dayOfweek;$k++){
        $calender .= "<td></td";
      }
    }

    // initialing  the day counter 
    $currentDay = 1;

    // gettin the month number 

    $month = str_pad($month, 2,'0',STR_PAD_LEFT);

    while ($currentDay <= $numberOfDays) {

      // if seven  column ( saterday ) reached ,start a new row
      if($dayOfweek == 7){
        $dayOfweek = 0;
        $calender .= "<tr></tr>";
      }


      $currentDayRel = str_pad($currentDay,2,"0",STR_PAD_LEFT);
      $date = "$year-$month-$currentDayRel";


      $dayname = strtolower(date('l',strtotime($date)));
      $eventNum = 0;

      $today = $date == date('Y-m-d')?"today bg-warning" : "";
      if($date<date('Y-m-d')){
        $calender .= "<td class='today  '><h4>$currentDay</h4> <button class='btn btn-danger btn-sm'>N/A</button></td>";
      }elseif(in_array($date, $bookings)){
        $calender .= "<td class='$today '><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btn-primary btn-sm'>Already Booking</a></td>";
      }else{
        $calender .= "<td class='$today '><h4>$currentDay</h4> <a href='book.php?date=".$date."' class='btn btn-success btn-sm'>Book</a></td>";
      }
      // if($dateToday == $date){
      //   $calender .= "<td class='today bg-warning text-white'><h4>$currentDay</h4></td>";
      // }else{
      //   $calender .= "<td><h4>$currentDay</h4></td>";
      // }

      //$calender .="<td><h4>$currentDay</h4>";
      $calender .="</td>";

      // increment the counter

      $currentDay++;
      $dayOfweek++;

    }

    // completing the row of the last week in month,if necessary

    if($dayOfweek != 7){
      $remainingDays = 7-$dayOfweek;
      for($i=0;$i<$remainingDays;$i++){
        $calender .= "<td></td>";
      } 
    }
    echo $calender;

  }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container mt-5">
      <div class="row">
        <div class="col-md-12">
          
          <?php

            $dateComponents = getdate();
            if(isset($_GET['month']) && isset($_GET['year'])){
              $month = $_GET['month'];
              $year = $_GET['year'];

            }else{
              $month = $dateComponents['mon'];
              $year = $dateComponents['year'];
            }
            echo build_calender($month,$year);
          ?>
        </div>
      </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>