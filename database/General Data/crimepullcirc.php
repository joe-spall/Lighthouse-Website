<?php

$error = null;

class Crime{
	public $date;
	public $typeCrime;
	public $lat;
	public $long;
	
	function __construct($crimeDate, $crimeType, $crimeLat, $crimeLong)
	{
		$this->$date = $crimeDate;
		$this->$typeCrime = $crimeType;
		$this->$lat = $crimeLat;
		$this->$long = $crimeLong;
		
	}
	
}

function findCity($minLat, $maxLat, $minong, $maxLong)
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


if (isset($_POST["curlatitude"]))
{
  $curlatitude = test_input($_POST["curlatitude"]);
} 
else 
{
  $error = '1000';
}

if (isset($_POST["curlongitude"]))
{
  $curlongitude = test_input($_POST["curlongitude"]);
} 
else 
{
  $error = '1001';
}

if (isset($_POST["radius"]))
{
  $radius = test_input($_POST["radius"]);
} 
else 
{
  $error = '1002';
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
		$minLat = floatval($curlatitude) - floatval($radius);
		$maxLat = floatval($curlatitude) + floatval($radius);
		$minLong = floatval($curlongitude) - floatval($radius);
		$maxLong = floatval($curlongitude) + floatval($radius);
		
		$city = findCity($minLat, $maxLat, $minLong, $maxLong);
		
		$returnHolder[] = null;
		
		if(!is_null($city))
		{
			$sql = "SELECT * FROM ".$city."Data WHERE latitude BETWEEN ".$minLat." AND ".$maxLat." AND longitude BETWEEN ".$minLong." AND ".$maxLong." AND date >= ".$year;
			$result = $conn->query($sql);
			$numResults = $result->num_rows;
			$returnHolder = array('result_num' => $numResults);
			if ($numResults > 0)
			{
				// output data of each row
				while($row = $result->fetch_assoc())
				{
					$oneCrime = new Crime($row["date"], $row["crime"], $row["latitude"], $row["longitude"]);
					$returnHolder[] = $oneCrime;
				}
			}
		}
		else
		{
			$returnHolder = array('error' => '1005');
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