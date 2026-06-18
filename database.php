<!DOCTYPE html>
<html>
	<body>
		<?php

			require_once 'config.php';

			try {
				$dsn = "pgsql:host=$host;port=5432;dbname=$db;";
				// make a database connection
				$pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

				if ($pdo) {
					echo "Connected to the $db database successfully!" . "\n";
				}
			} catch (PDOException $e) {
				die($e->getMessage());
			}
					
			$bookingid = isset($_GET['bookingid']) ? (int)$_GET['bookingid'] : 0;
			$stmt = $pdo->query("SELECT customer.customername, room.roomname, booking.datefrom, booking.dateto FROM booking FULL JOIN customer ON booking.customerid = customer.customerid FULL JOIN room ON booking.roomid = room.roomid WHERE booking.bookingid = " . $bookingid);

		?>
			<table border="1">
				<th>Name</th>
				<th>Room</th>
				<?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
					<tr>
		
						<td>
							<?php echo $row['customername'] . "\n"; ?>
						</td>
						<td>
							<?php echo $row['roomname'] . "\n"; ?>
						</td>
					</tr>
				<?php } ?>
			</table>
	</body>		
</html>

