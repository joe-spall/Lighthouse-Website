<?php

class Crime{
	public $date = "";
	public $typeCrime = "";
	public $lat = "";
	public $long = "";
	
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if (isset($_POST["servername"]))
{
  $servername = test_input($_POST["servername"]);
} 
else 
{
  $servername = null;
  echo "no servername supplied";
}

if (isset($_POST["username"]))
{
  $username = test_input($_POST["username"]);
} 
else 
{
  $username = null;
  echo "no username supplied";
}

if (isset($_POST["password"]))
{
  $password = test_input($_POST["password"]);
} 
else 
{
  $password = null;
  echo "no password supplied";
}

if (isset($_POST["dbname"]))
{
  $dbname = test_input($_POST["dbname"]);
} 
else 
{
  $dbname = null;
  echo "no dbname supplied";
}

if (isset($_POST["alatitude"]))
{
  $alatitude = test_input($_POST["alatitude"]);
} 
else 
{
  $alatitude = null;
  echo "no a latitude supplied";
}

if (isset($_POST["alongitude"]))
{
  $alongitude = test_input($_POST["alongitude"]);
} 
else 
{
  $alongitude = null;
  echo "no a longitude supplied";
}

if (isset($_POST["blatitude"]))
{
  $blatitude = test_input($_POST["blatitude"]);
} 
else 
{
  $blatitude = null;
  echo "no b latitude supplied";
}

if (isset($_POST["blongitude"]))
{
  $blongitude = test_input($_POST["blongitude"]);
} 
else 
{
  $blongitude = null;
  echo "no b longitude supplied";
}

if (isset($_POST["city"]))
{
  $city = test_input($_POST["city"]);
} 
else 
{
  $city = null;
  echo "no city supplied";
}


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$alatitude = floatval($alatitude);
$alongitude = floatval($alongitude);
$blatitude = floatval($blatitude);
$blongitude = floatval($blongitude);
$minLat = min($alatitude,$blatitude);
$maxLat = max($alatitude, $blatitude);
$minLong = min($alongitude, $blongitude);
$maxLong = max($alongitude, $blongitude);

$sql = "SELECT * FROM ".$city."Data WHERE latitude ".$minLat." AND ".$maxLat." AND longitude ".$minLong." AND ".$maxLong;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	// output data of each row
	$returnHolder = [];
	while($row = $result->fetch_assoc()) {

		$oneCrime = new Crime();
		$oneCrime->date = $row["date"];
		$oneCrime->typeCrime = $row["crime"];
		$oneCrime->lat = $row["latitude"];
		$oneCrime->long = $row["longitude"];
		$returnHolder[] = $oneCrime;
		
		
	}
	echo json_encode($returnHolder);
} else {
	echo "0 results";
}

$conn->close();
?>