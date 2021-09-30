// self invoked function to run immediately page opens
(function() {
    // document.getElementById('file_upload_box').addEventListener("");
    // let drop_box = document.getElementById('file_upload_box');
    //
    // drop_box.addEventListener('dragenter', handlerFunction, false);
    // drop_box.addEventListener('dragleave', handlerFunction, false);
    // drop_box.addEventListener('dragover', handlerFunction, false);
    // drop_box.addEventListener('drop', handlerFunction, false);

}());

var csvContent = [{
  date: 0,
  time: null,
  sensor_value: null
}];
//adds the new tag
function addTag(date,time,sensor_value,csvContentArray) {
  var csvContents={date,time,sensor_value};
  csvContentArray.push(csvContents);
}
//removes all the stored tags except for the default
function deleteArrayContents(array) {
  while (array.length) {
    array.pop();
  }
}
//gets the most recent tag
function currentSensorReading(array) {
  var current=array.length;
  return current-1;
}

function generateHeatMap(){

}

function generateVideo() {

}

function readCsvFile(fileName){

}
