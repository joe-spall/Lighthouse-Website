<?php

$error = null;

class Crime{
  var $id;
  var $date;
  var $typeCrime;
  var $lat;
  var $long;
  
  function __construct($crimeId, $crimeDate, $crimeType, $crimeLat, $crimeLong)
  {
    $this->id = $crimeId;
    $this->date = $crimeDate;
    $this->typeCrime = $crimeType;
    $this->lat = $crimeLat;
    $this->long = $crimeLong;
    
  }
  
}

function findCity($minLat, $maxLat, $minLong, $maxLong)
{
  $ranges = array('atl' => array(33.6,33.9,-84.6,-84.3), 'nyc' => array(40.5,40.92,-74.26,-73.68), 'chi' => array(36.6,42.1,-91.7,-87.53));
  $cityValue = null;
  
  foreach($ranges as $city => $cityData)
  {
    if((($minLat >= $ranges[$city][0] && $minLat <= $ranges[$city][1]) || ($maxLat >= $ranges[$city][0] && $maxLat <= $ranges[$city][1])) && (($minLong >= $ranges[$city][2] && $minLong <= $ranges[$city][3]) || ($maxLong >= $ranges[$city][2] && $maxLong <= $ranges[$city][3])))
    {
      $cityValue = $city;
      break;
    }
  }
  
  return $cityValue;
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


if (isset($_POST["aLat"]))
{
  $aLat = floatval(test_input($_POST["aLat"]));
} 
else 
{
  $error = '1000';
}

if (isset($_POST["aLng"]))
{
  $aLng = floatval(test_input($_POST["aLng"]));
} 
else 
{
  $error = '1001';
}

if (isset($_POST["bLat"]))
{
  $bLat = floatval(test_input($_POST["bLat"]));
} 
else 
{
  $error = '1007';
}

if (isset($_POST["bLng"]))
{
  $bLng = floatval(test_input($_POST["bLng"]));
} 
else 
{
  $error = '1008';
}


if (isset($_POST["year"]))
{
  $year = test_input($_POST["year"]);
} 
else 
{
  $error = '1003';
}

// Create connection
if(is_null($error))
{
  $conn = new mysqli("localhost", "applight_LHUser", "mikelikesbirds1!", "applight_lighthouse");
  // Check connection
  if ($conn->connect_error)
  {
    echo json_encode(array('error' => '1004 '.$conn->connect_error));
    $conn->close();
  }
  else
  {
    $minLat = min($aLat,$bLat);
    $maxLat = max($aLat,$bLat);
    $minLong = min($aLng,$bLng);
    $maxLong = max($aLng,$bLng);
    
    $city = findCity($minLat, $maxLat, $minLong, $maxLong);
    
    $returnHolder = array();
    
    if(!is_null($city))
    {
      $sql = "SELECT * FROM ".$city."Data WHERE latitude BETWEEN ".$minLat." AND ".$maxLat." AND longitude BETWEEN ".$minLong." AND ".$maxLong." AND date >= '".$year. "'";
      $resultFromPull = $conn->query($sql);
      $numResults = $resultFromPull->num_rows;
      $results = [];

      if ($numResults > 0)
      {
        // output data of each row
        while($row = $resultFromPull->fetch_assoc())
        {
          $thisLat = floatval($row["latitude"]);
          $thisLong = floatval($row["longitude"]);
          
          $oneCrime = new Crime($row["id"], $row["date"], $row["crime"],$thisLat,$thisLong);
          $results[] = $oneCrime;
          
          
        }
      }
      $returnHolder["result_num"] = count($results);
      $returnHolder["results"] = $results; 
    }
    else
    {
      $returnHolder = array("error" => "1005");
    }
    
    echo json_encode($returnHolder);
    $conn->close();
  }
}
else
{
  echo json_encode(array('error' => $error));
}

?>