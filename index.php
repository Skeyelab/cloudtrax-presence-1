<?php
$jsondata = file_get_contents("php://input");
$data     = json_decode($jsondata, true);

$serializedData = serialize($jsondata); //where '$array' is your array
// file_put_contents('latest.txt', $serializedData);

// file_put_contents('latest-raw.txt', $jsondata);

if ($jsondata == "") {
    echo "Configure your Cloudtrax Presence Reporting 'Server Location' to:<br/>";
    echo 'https://' . $_SERVER['SERVER_NAME'] . '<br/>';
    echo '<br/>';
    echo 'You can get your Cloudtrax Presence Reporting Key from the Heroku dashboard for this app.<br/>';
    echo '<br/>';
    echo 'To access your data, you can get your MySQL credentials from your Heroku dashboard for this app.';
    exit;
}

if (hash_hmac('sha256', $jsondata, getenv('CLOUDTRAX_PR_KEY')) != $_SERVER['HTTP_SIGNATURE']) {
    header("HTTP/1.1 401 Unauthorized");
    echo "Invalid Key";
    exit(1);
}


$network_id = $data['network_id'];
$node_mac   = $data['node_mac'];
$version    = $data['version'];

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

$sql = "INSERT INTO presence_header (network_id, node_mac, version)
VALUES ('$network_id', '$node_mac', '$version')";

if ($conn->query($sql) === TRUE) {
    #echo "New record created successfully";
    $headerid = mysqli_insert_id($conn);
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

foreach ($data['probe_requests'] as $client) {
    $mac              = $client['mac'];
    $count            = $client['count'];
    $min_signal       = $client['min_signal'];
    $max_signal       = $client['max_signal'];
    $avg_signal       = $client['avg_signal'];
    $last_seen_signal = $client['last_seen_signal'];
    $first_seen       = $client['first_seen'];
    $last_seen        = $client['last_seen'];
    $associated       = $client['associated'];
    
    $sql = "INSERT INTO presence_detail (id_presence_header, mac, count, min_signal, max_signal, avg_signal, first_seen, last_seen, associated, id_presence_detail)
VALUES ('$headerid', '$mac', '$count', '$min_signal', '$max_signal', '$avg_signal', '$first_seen', '$last_seen', '$associated', '')";
    
    if ($conn->query($sql) === TRUE) {
        #echo "New detail record created successfully";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
        file_put_contents('error.txt', $error);
    }
}
$conn->close();
exit("Success");
?> 