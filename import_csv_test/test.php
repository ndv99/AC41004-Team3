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

        <!-- test form for importing csv using php -->
        <div title="form run on php" class="php_form">
          <form class="" enctype="multipart/form-data" action="upload.php" method="post">
            <label for="file">Select CSV File:</label><br>
            <input type="file" id="file_to_import" name="csv_file" accept=".csv" ><br>
            <br>
            <!-- sensor option to choose from -->
            <label for="sensor_options">Choose sensor to upload imported data for:</label><br>
            <select id="s_options" name="sensor_options">
              <option value="">-----Choose Sensor-----</option>
              <option value="one">Sensor 1</option>
              <option value="two">Sensor 2</option>
              <option value="three">Sensor 3</option>
              <option value="four">Sensor 4</option>
            </select>
            <br>

            <br>
            <input class="form-control" type="submit" id="submitFile" value="Upload File" name="import">
          </form>
        </div>

        <!-- test canvas for sample heatmap for the imported csv via php -->
        <div class="canvas_container">
          <canvas id="test_canvas" width="60" height="60"></canvas>
        </div>

        <!-- JS -->
        <!-- test form for importing csv using js -->
        <div title="form run on js" class="js_form">
          <!-- import button- to import csv -->
          <!-- <button onclick="readCsvFile()" id="">Import File</button> -->
          <br>
          <!-- generate video button- to generate video from imported data -->
          <!-- <button onclick="generateVideo()" title="to compile visualised data into a video" id="">Generate Video</button> -->
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

      </div>

    </div>

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
