<?php 
$servername = "us-cdbr-iron-east-04.cleardb.net";
$username = "bbde8408e45097";
$password = "4c985d7a";
$dbname = "heroku_2e1b0ff86f97c10";

$jsondata = file_get_contents("php://input");
$data = json_decode($jsondata, true);

$serializedData = serialize($jsondata); //where '$array' is your array
#file_put_contents('latest.txt', $serializedData);

#file_put_contents('latest-raw.txt', $jsondata);

$network_id = $data['network_id'];
$node_mac = $data['node_mac'];
$version = $data['version'];

$url = parse_url(getenv('CLEARDB_DATABASE_URL'));

  // Decode urlencoded information in the db connection string
  $url['user'] = urldecode($url['user']);
  // Test if database URL has a password.
  $url['pass'] = isset($url['pass']) ? urldecode($url['pass']) : '';
  $url['host'] = urldecode($url['host']);
  $url['path'] = urldecode($url['path']);
  if (!isset($url['port'])) {
    $url['port'] = NULL;
  }


// Create connection
$conn = new mysqli($url['host'], $url['user'], $url['pass'], substr($url['path'], 1));
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$sql1 =  "CREATE TABLE IF NOT EXISTS `access_points` (
  `Access_Point_ID` int(6) DEFAULT NULL,
  `MAC_Address` varchar(17) DEFAULT NULL,
  `Access_Point_Name` varchar(27) DEFAULT NULL,
  `Access_Point_Description` varchar(33) DEFAULT NULL,
  `IP` varchar(12) DEFAULT NULL,
  `Role` varchar(8) DEFAULT NULL,
  `Firmware_Version` varchar(10) DEFAULT NULL,
  `Mesh_Version` varchar(10) DEFAULT NULL,
  `Last_Checkin` varchar(20) DEFAULT NULL,
  `Uptime` varchar(11) DEFAULT NULL,
  `Hardware_Model` varchar(7) DEFAULT NULL,
  `Free_Memory` int(5) DEFAULT NULL,
  `System_Load` decimal(3,2) DEFAULT NULL,
  `Spare` varchar(10) DEFAULT NULL,
  `Latitude` decimal(8,6) DEFAULT NULL,
  `Longitude` decimal(8,6) DEFAULT NULL,
  `Channel_2_4` varchar(2) DEFAULT NULL,
  `Channel_5` varchar(2) DEFAULT NULL,
  `Latency` decimal(4,2) DEFAULT NULL,
  `Mesh_Hops` int(1) DEFAULT NULL,
  `Number_of_Clients` int(3) DEFAULT NULL,
  `Download_Bytes` bigint(10) DEFAULT NULL,
  `Upload_Bytes` int(10) DEFAULT NULL,
  `Total_Bytes` bigint(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$sql2 = "CREATE TABLE IF NOT EXISTS `presence_detail` (
  `id_presence_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_presence_header` int(11) NOT NULL,
  `mac` varchar(64) CHARACTER SET latin1 NOT NULL,
  `count` varchar(64) CHARACTER SET latin1 NOT NULL,
  `min_signal` varchar(64) CHARACTER SET latin1 NOT NULL,
  `max_signal` varchar(64) CHARACTER SET latin1 NOT NULL,
  `avg_signal` varchar(64) CHARACTER SET latin1 NOT NULL,
  `first_seen` varchar(64) CHARACTER SET latin1 NOT NULL,
  `last_seen` varchar(64) CHARACTER SET latin1 NOT NULL,
  `associated` varchar(64) CHARACTER SET latin1 NOT NULL,
  `timestamp_detail` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presence_detail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$sql3 = "CREATE TABLE IF NOT EXISTS `presence_header` (
  `id_presence_header` int(11) NOT NULL AUTO_INCREMENT,
  `network_id` int(11) NOT NULL,
  `node_mac` varchar(64) CHARACTER SET latin1 NOT NULL,
  `version` varchar(64) CHARACTER SET latin1 NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presence_header`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";


if ($conn->query($sql1) === TRUE) {
    echo "access_points Table created successfully<br/>";
	$headerid = mysqli_insert_id($conn);
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


if ($conn->query($sql2) === TRUE) {
    echo "presence_detail Table created successfully<br/>";
  $headerid = mysqli_insert_id($conn);
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


if ($conn->query($sql3) === TRUE) {
    echo "presence_header Table created successfully<br/>";
  $headerid = mysqli_insert_id($conn);
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

echo "You can now configure your Cloudtrax Presence Reporting 'Server Location' to:<br/>";
echo 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
