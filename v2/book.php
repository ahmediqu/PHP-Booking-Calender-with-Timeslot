<?php
  if(isset($_GET['date'])){
    $date = $_GET['date'];
  }


    $mysqli = new mysqli('localhost','root','','test_database');
    $stmt = $mysqli->prepare("SELECT * FROM booking WHERE booking_date = ?");
    $stmt->bind_param('s',$date);
    $bookings = array();
    if($stmt->execute()){
      $result = $stmt->get_result();
      if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
          $bookings[] = $row['timeslot'];
        }
        $stmt->close();
      }
    }



  if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslot = $_POST['timeslot'];



    $stmt = $mysqli->prepare("SELECT * FROM booking WHERE booking_date = ? AND timeslot = ?");
    $stmt->bind_param('ss',$date,$timeslot);
 
    if($stmt->execute()){
      $result = $stmt->get_result();
      if($result->num_rows > 0){
         $msg = "<div class='alert alert-danger'>Already Booking</div>";
        
      }else{
        // $mysqli = new mysqli('localhost','root','','test_database');
        $stmt = $mysqli->prepare("INSERT INTO booking (name, email, booking_date , timeslot) values (?,?,?,?)");
        $stmt->bind_param('ssss',$name,$email,$date,$timeslot);
        $stmt->execute();
        $msg = "<div class='alert alert-success'>Booking Successfully</div>";
        $bookings[] =$timeslot;
        $stmt->close();
        $mysqli->close();
      }
    }




    

  }

$duration = 10;
$cleanup = 0;
$start = "09:00";
$end = "15:00";

  function timeslots($duration,$cleanup,$start,$end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();
    for($intStart = $start;$intStart<$end;$intStart->add($interval)->add($cleanupInterval)){
      $endPeriod = clone $intStart;
      $endPeriod->add($interval);
      if($endPeriod>$end){
        break;
      }

      $slots[] = $intStart->format("H:iA")."-".$endPeriod->format("H:iA");
    }
    return $slots;
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
      <?php echo isset($msg)?$msg: '';?>
      <div class="row">
        <?php
            $timeslots = timeslots($duration,$cleanup,$start,$end);
            foreach($timeslots as $ts){
          ?>
      <div class="col-md-2">
        <div class="form-group">
          <?php
            if(in_array($ts, $bookings)){
          ?>
          <button class="btn btn-danger btn-sm timeSlotBook" data-timeslot="<?php echo $ts;?>"><?php echo $ts;?></button>
          <?php
            }else{


          ?>
          <button class="btn btn-info btn-sm timeSlotBook" data-timeslot="<?php echo $ts;?>"><?php echo $ts;?></button>
        <?php } ?>
        </div>
        
      </div>
          <?php
            }
          ?>
      </div>
      <div class="row justify-content-center">
        <!-- <div class="col-md-6">
          
          <div class="jumbotron">
          <h3 class="text-center">Book for date : <?php //echo date('F d,y',strtotime($date));?></h3>
          <br>
          <form action="" method="post">
    <?php// echo isset($msg)?$msg: '';?>
            <div class="form-group">
              <label for="">Name</label>
              <input type="text" class="form-control" name="name">
            </div>

            <div class="form-group">
              <label for="">Email</label>
              <input type="email" class="form-control" name="email">
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-info btn-lg" name="submit" value="Book">
            </div>

          </form>
          </div>
        </div> -->
      </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Time Slot</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <form action="" method="post">
      <div class="modal-body">
       
    <?php echo isset($msg)?$msg: '';?>
        <div class="form-group">
          <label for="">Timeslot</label>
          <input type="text" name="timeslot" id="timeslot" class="form-control" required readonly>
        </div>
        <div class="form-group">
              <label for="">Name</label>
              <input type="text" class="form-control" name="name">
            </div>

            <div class="form-group">
              <label for="">Email</label>
              <input type="email" class="form-control" name="email">
            </div>

            
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Booking" name="submit">
      </div>
    </form>
    </div>
  </div>
</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
      $('.timeSlotBook').click(function(){
        var timeslot = $(this).attr('data-timeslot');
        $('#slot').html(timeslot);
        $('#timeslot').val(timeslot);
        $('#myModal').modal("show");

      });

     
    </script>
  </body>
</html>