<?php
// $target_dir = "uploads/";
// $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
// $uploadOk = 1;
// $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
session_start();
require('db_connect.php');

if (isset($_POST["import"])) {
  $fileName = $_FILES["csv_file"]["tmp_name"];
  $sensor = $_POST['sensor'];

  if ($_FILES["csv_file"]["size"] > 0) {
    $row = 1;
    $field_data;

    function determine_colour ($value) {
      if ($value>=0 && $value<=256) {
        echo "green <br />\n";
      } else if ($value>=257 && $value<=512) {
        echo "yellow <br />\n";
      } else if ($value>=513 && $value<=768) {
        echo "orange <br />\n";
      } else if ($value>=769 && $value<=1025) {
        echo "red <br />\n";
      }

    }

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

            // upload content into srever here

            echo "Date: $date Time: $time <br />\n";

            // sensor value
            echo "Sensor value: $csv_data[1] <br />\n";

            // sensor value's corresponding colour
            determine_colour($csv_data[1]);
            echo "<br />\n";

            $stmt = $pdo->prepare("INSERT INTO `sensor_data`(`user_id`, `date`, `time`, `value`, `sensor_no`) VALUES ('".$_SESSION['UserID']."','". $date ."','" . $time ."','". $csv_data[1] ."','". $sensor."')");
            $stmt->execute();

          }
        }
        fclose($handle);
    }
  }
} else {
  echo "Import Error. Try Again.";
}

// Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//   if($check !== false) {
//     echo "File is an image - " . $check["mime"] . ".";
//     $uploadOk = 1;
//   } else {
//     echo "File is not an image.";
//     $uploadOk = 0;
//   }
// }
?>