<?php
//start session and connect to database
session_start();
require('db_connect.php');
?>

<?php
//get the latest session number, so can add new sessions following on from this number.
$query = "SELECT MAX(session_id) FROM sensor_data WHERE user_id =". $_SESSION['UserID']. ";";
$stmt = $pdo->prepare($query);
$stmt->execute();
$row = $stmt->fetch();
$result = $row["MAX(session_id)"];

$session = $result+1;

$prev_time = 0;


if (isset($_POST["import"])) {
  //for loop iterates 4 times as we are uploading 4 files
  for ($i=0; $i < 4; $i++) { 
    $fileName = $_FILES["csv_file"]["tmp_name"][$i];

    $sensor = $i + 1;
    $session = $result+1; 

    if ($_FILES["csv_file"]["size"][$i] > 0) {
      $row = 1;
      $field_data;

      $fileEndPosition = filesize($fileName);

      if (($handle = fopen($fileName, "r")) !== FALSE) {
          // to help skip the first line
          fgetcsv($handle);
          while (($csv_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($csv_data[0]=="") {
              break;
            } else {
              // handle csv date here

              $row++;
              // split the date and time into an array
              $field_data= explode("T",$csv_data[0]);

                         
              // date and time separated
              $date= $field_data[0];
              $time= substr($field_data[1],0,strpos($field_data[1],"Z"));

              $current_time = (int)substr(substr($field_data[1],0,strpos($field_data[1],"Z")),3,2);

              if($row == 2){
                $prev_time = $current_time;
              }

              if((int)$current_time == 0){
                if(((((int)$current_time + 59) - (int)$prev_time)) >=1 ){
                    $session++;
                  }

              }
              else if(  ((int)$current_time - (int)$prev_time) >= 2 ){
                  $session++;
              }

              
              $stmt = $pdo->prepare("INSERT INTO `sensor_data`(`user_id`, `date`, `time`, `value`, `sensor_no`, `session_id`) VALUES ('".$_SESSION['UserID']."','". $date ."','" . $time ."','". $csv_data[1] ."','". $sensor."','". $session."')");
              $stmt->execute();
              $prev_time = $current_time;
              
            }
          }
          fclose($handle);
      }
    }
  }

  //sets the timezone for last upload
  date_default_timezone_set("Europe/London");
  //get current time
  $currentTime = date_create()->format('Y-m-d H:i:s');

  //query which updates the database with the time that the user uploaded their sessions at
  $query = "UPDATE user SET lastLogin=:currenttime WHERE user_id = :userid;";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(":currenttime", $currentTime, PDO::PARAM_STR);
  $stmt->bindParam(":userid", $_SESSION['UserID'], PDO::PARAM_STR);
  $stmt->execute();
} else {
  echo "Import Error. Try Again.";
}



header("location: index.php");
exit;
?>
