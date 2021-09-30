<?php

if (isset($_POST["import"])) {
  $fileName = $_FILES["csv_file"]["tmp_name"];
  $sensor_choice = filter_input(INPUT_POST, 'sensor_options', FILTER_SANITIZE_STRING);
  
  session_start();
  require('../db_connect.php');
  $sensor = $_POST['sensor'];

  if ($_FILES["csv_file"]["size"] > 0) {
    // to check if a sensor option was picked
    if ($sensor_choice) {
      $chosen_sensor;
      if ($sensor_choice=="one") {
        $chosen_sensor = 1;
      } else if ($sensor_choice=="two") {
        $chosen_sensor = 2;
      } else if ($sensor_choice=="three") {
        $chosen_sensor = 3;
      } else if ($sensor_choice=="four") {
        $chosen_sensor = 4;
      }

      echo "Chosen sensor:  $chosen_sensor <br>\n";
      $row = 1;
      $field_data;

      // this is to generate the hex values for the colours
      // based on the sensor value position in it's respective colour range
      // according to the sensor value in order to help with the
      // heatmap visualisation
      function colour_hex_val_gen ($current_colour, $percent) {
        $hex_val;

        if ($current_colour=="green") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FEFB01";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "CEFB02";
          } else if ($percent>=0.41 && $percent<=0.4) {
            $hex_val = "87FA00";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "3AF901";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "00ED01";
          }
        } else if ($current_colour=="yellow") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FFF600";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "FFCF07";
          } else if ($percent>=0.41 && $percent<=0.4) {
            $hex_val = "FA80F";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "FE8116";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "FE5A1D";
          }
        } else if ($current_colour=="orange") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FA6F01";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "F55301";
          } else if ($percent>=0.41 && $percent<=0.4) {
            $hex_val = "F03801";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "EB1C01";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "E60001";
          }
        } else if ($current_colour=="red") {
          if ($percent>=0 && $percent<=0.25) {
            $hex_val = "FF0000";
          } else if ($percent>=0.26 && $percent<=0.5) {
            $hex_val = "BF0000";
          } else if ($percent>=0.51 && $percent<=0.75) {
            $hex_val = "800000";
          } else if ($percent>=0.76 && $percent<=1) {
            $hex_val = "400000";
          }
        }

        return $hex_val;
      }

      $colour;
      // variable to contain the colour percent to aid hex colour generation
      $colour_percent;

      function determine_colour ($value) {
        $colour_hex_values = "0x";
        $colour_code= "Colour code: ";

        if ($value >=0 && $value <= 256) {
          $colour_percent= $value/256;
          $colour="green";

          // the generated hex value for the colours
          $colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);


          echo "$colour <br />\n";
          echo "$colour_code $colour_hex_values <br />\n";
        } else if ($value >= 257 && $value <= 512) {
          $colour_percent= (512-$value)/256;
          $colour="yellow"; 

          // the generated hex value for the colours
          $colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

          echo "$colour <br />\n";
          echo "$colour_code $colour_hex_values <br />\n";
        } else if ($value >= 513 && $value <= 768) {
          $colour_percent = (768-$value)/256;
          $colour="orange";

          // the generated hex value for the colours
          $colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

          echo "$colour <br />\n";
          echo "$colour_code $colour_hex_values <br />\n";
        } else if ($value >= 769 && $value <= 1025) {
          $colour_percent = (1025 - $value)/256;
          $colour="red";

          // the generated hex value for the colours
          $colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

          echo "$colour <br />\n";
          echo "$colour_code $colour_hex_values <br />\n";
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

              //$stmt = $pdo->prepare("INSERT INTO `sensor_data`(`user_id`, `date`, `time`, `value`, `sensor_no`) VALUES ('1','". $date ."','" . $time ."','". $csv_data[1] ."','PUT SENSOR ID HERE')");
              //$stmt->execute();
               $stmt = $pdo->prepare("INSERT INTO `sensor_data`(`user_id`, `date`, `time`, `value`, `sensor_no`) VALUES ('".$_SESSION['UserID']."','". $date ."','" . $time ."','". $csv_data[1] ."','". $sensor."')");
               $stmt->execute();

            }
          }
          fclose($handle);
      }

    } else {
      echo "No sensor option chosen. Try Again. <br>\n";
      echo "<a href='test.php'>Return to form</a>";
    }
  } else {
    echo "Import Error. Try Again.  <br>\n";
    echo "<a href='test.php'>Return to form</a>";
  }
} else {
  echo "Import Error. Try Again.";
}
?>
