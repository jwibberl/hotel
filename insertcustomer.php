<?php require_once 'config.php';
	
	function connectdb($hostname, $username, $password, $dbname) {
		try {
			$dsn = "pgsql:host=$hostname;port=5432;dbname=$dbname;";
			// make a database connection
			$pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
			// if ($pdo) {
			//	echo "Connected to the $dbname database successfully!" . "\n";
			//}
		} catch (PDOException $e) {
			die($e->getMessage());
		}
		if ($pdo) {
			return($pdo);
		}
	}
	
	$customername = $_POST['formname'];
	$customerpostcode = $_POST['formpostcode'];

	//sanitise inputs
	$customername = htmlspecialchars(trim($customername));
	$customerpostcode = htmlspecialchars(trim($customerpostcode));

	$db = connectdb($host, $user, $password, $dbname);
	$stmt = $db->prepare('INSERT INTO customer (customername, customerpostcode) values (:customername, :customerpostcode)');

	$stmt->bindParam(':customername', $customername);
	$stmt->bindParam(':customerpostcode', $customerpostcode);
	$stmt->execute();
?>

<?php
	//redirect to customers page
	header("Location: customers.php");
?>
