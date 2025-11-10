<?php
require_once 'config.php';

function connectdb($hostname, $username, $password, $dbname) {
    try {
        $dsn = "pgsql:host=$hostname;port=5432;dbname=$dbname;";
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . htmlspecialchars($e->getMessage()));
    }
}

// Connect to database
$db = connectdb($host, $user, $password, $dbname);

// Validate POST data
if (!isset($_POST['customerid'], $_POST['roomid'], $_POST['datefrom'], $_POST['dateto'])) {
    die("Missing required booking data.");
}

$customerid = (int)$_POST['customerid'];
$roomid = (int)$_POST['roomid'];
$datefrom = $_POST['datefrom'];
$dateto = $_POST['dateto'];

// Optional: validate date order
if (strtotime($dateto) < strtotime($datefrom)) {
    die("End date cannot be before start date.");
}

// Insert booking into the database
$stmt = $db->prepare("
    INSERT INTO booking (customerid, roomid, datefrom, dateto)
    VALUES (:customerid, :roomid, :datefrom, :dateto)
");
$stmt->execute([
    ':customerid' => $customerid,
    ':roomid' => $roomid,
    ':datefrom' => $datefrom,
    ':dateto' => $dateto
]);

// Redirect back to booking.php
header("Location: booking.php");
exit;
