<!DOCTYPE html>
<html>
	<body>
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
		?>
			<?php
			//start session
			session_start();

			//retrieve customer id from session, name and postcode from the form POST
			$customerid = $_SESSION['customerid'];
			$customername = $_POST['formname'];
			$customerpostcode = $_POST['formpostcode'];

			echo 'customer id ' . $customerid . ' name ' . $customername . ' postcode ' . $customerpostcode;

			//sanitise inputs
			$customername = htmlspecialchars(trim($customername));
			$customerpostcode = htmlspecialchars(trim($customerpostcode));
			$customerid = (int)$customerid;

			$db = connectdb($host, $user, $password, $dbname);
			$stmt = $db->prepare('UPDATE customer SET customername= :customername,  customerpostcode = :customerpostcode WHERE customerid = :customerid');

			$stmt->bindParam(':customername', $customername);
			$stmt->bindParam(':customerpostcode', $customerpostcode);
			$stmt->bindParam(':customerid', $customerid, PDO::PARAM_INT);

			$stmt->execute();
		?>

	</body>
</html>
