<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>My go at the interface</title>
    <link rel="stylesheet" href="css/project.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <!-- <link rel="manifest" href="manifest.json"> -->
  </head>
  <body>

    <div class="page_heading">
      <h1>Your recovery tracker</h1>
    </div>
    <!-- background for the app -->
    <div class="body_background">
      <!-- contains all the main content of the page -->
      <div class="content_container">
        <!-- test form for importing csv using js -->
        <div title="form run on js" class="js_form">
          <!-- import button- to import csv -->
          <button onclick="readCsvFile()" id="">Import File</button>
          <br>
          <div class="generated_video">

          </div>
          <!-- optional download button- to download generated video -->
          <button onclick="download_video">Download your video</button>
        </div>

        <!-- test canvas for sample heatmap for the imported csv via js -->
        <div class="canvas_container">
          <canvas id="test_canvas" width="60" height="60"></canvas>
        </div>

        <!-- test form for importing csv using php -->
        <div title="form run on php" class="php_form">
          <form class="" enctype="multipart/form-data" action="upload.php" method="post">
            <label for="file">Select CSV File:</label><br>
            <input type="file" id="file_to_import" name="csv_file" accept=".csv" ><br>

            <br>
            <input class="form-control" type="submit" id="submitFile" value="Upload File" name="import">
          </form>
        </div>

        <?php
        // $row = 1;
        // $field_data;
        //
        // function determine_colour ($value) {
        //   if ($value>=0 && $value<=256) {
        //     echo "green <br />\n";
        //   } else if ($value>=257 && $value<=512) {
        //     echo "yellow <br />\n";
        //   } else if ($value>=513 && $value<=768) {
        //     echo "orange <br />\n";
        //   } else if ($value>=769 && $value<=1025) {
        //     echo "red <br />\n";
        //   }
        //
        // }
        //
        // if (($handle = fopen("data/sensor4.csv", "r")) !== FALSE) {
        //     // to help skip the first line
        //     fgetcsv($handle);
        //     while (($csv_data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //       if ($csv_data[0]=="") {
        //         break;
        //       } else {
        //         // handle csv date here
        //         // $num = count($csv_data);
        //         // echo "<p> $num fields in line $row: <br /></p>\n";
        //         $row++;
        //         $field_data= explode("T",$csv_data[0]);
        //         // date and time separated
        //         $date= $field_data[0];
        //         $time= substr($field_data[1],0,strpos($field_data[1],"Z"));
        //
        //         echo "Date: $date Time: $time <br />\n";
        //         // sensor value
        //         echo "Sensor value: $csv_data[1] <br />\n";
        //         // sensor value corresponding colour
        //         determine_colour($csv_data[1]);
        //         echo "<br />\n";
        //         // for ($c=0; $c < $num; $c++) {
        //         //     echo $csv_data[$c] . "<br />\n";
        //         // }
        //       }
        //     }
        //     fclose($handle);
        // }
        ?>

        <!-- test canvas for sample heatmap for the imported csv via php -->
        <div class="canvas_container">
          <canvas id="test_canvas" width="60" height="60"></canvas>
        </div>

      </div>

    </div>


    <!-- <?php  ?> -->

    <script src="js/project.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    <!-- <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener("load", ()=>{
          navigator.serviceWorker.register('service-worker.js')
        })

      }
    </script> -->
  </body>
</html>
