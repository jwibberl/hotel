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

$db = connectdb($host, $user, $password, $dbname);

// Fetch customers
$customers = $db->query("SELECT customerid, customername FROM customer ORDER BY customername")->fetchAll(PDO::FETCH_ASSOC);

// Fetch rooms
$rooms = $db->query("SELECT roomid, roomname FROM room ORDER BY roomname")->fetchAll(PDO::FETCH_ASSOC);

// Generate next 30 days for Date From / Date To
$dates = [];
for ($i = 0; $i < 30; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days"));
}

function esc($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hotel Aurora • New Booking</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Poppins',sans-serif; background:linear-gradient(180deg,#021b33,#073d57); color:#f2f6fa; min-height:100vh; display:flex; flex-direction:column; }
header { background: rgba(255,255,255,0.05); backdrop-filter:blur(10px); padding:20px 40px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); }
.logo { font-size:24px; font-weight:700; color:#00d9d9; }

main { flex:1; display:flex; gap:24px; padding:30px 20px; width:100%; }

nav.sidebar { flex:0 0 250px; background: rgba(255,255,255,0.03); border-radius:12px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,0.3); display:flex; flex-direction: column; gap:16px; }
nav.sidebar h2 { font-size:18px; margin-bottom:12px; color:#00d9d9; }
nav.sidebar a { color:#fff; text-decoration:none; font-weight:500; padding:8px 12px; border-radius:6px; transition:background 0.2s; }
nav.sidebar a:hover { background: rgba(0,217,217,0.2); }

section.content { flex:1; background: rgba(255,255,255,0.04); padding:30px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.3); overflow-x:auto; }
h1 { font-weight:600; font-size:28px; margin-bottom:20px; color:#fff; text-align:center; }

form { display:flex; flex-direction:column; gap:20px; }
label { margin-bottom:6px; font-weight:500; }
select { padding:8px; border-radius:8px; border:none; background: rgba(255,255,255,0.1); color:#fff; font-weight:500; }
input[type="submit"] { margin-top:10px; padding:10px 16px; border:none; border-radius:10px; background:#00d9d9; color:#022; font-weight:700; cursor:pointer; transition: background 0.2s ease; }
input[type="submit"]:hover { background:#00c4c4; }

footer { text-align:center; padding:20px; font-size:14px; color:rgba(255,255,255,0.5); border-top:1px solid rgba(255,255,255,0.05); margin-top:30px; }

@media(max-width:900px){
    main { flex-direction:column; }
    nav.sidebar { flex:1; flex-direction:row; overflow-x:auto; padding:12px; }
    nav.sidebar a { white-space:nowrap; margin-right:8px; }
    section.content { margin-top:20px; }
}
</style>
</head>
<body>

<header>
    <div class="logo">Aurora Hotel</div>
    <div>New Booking</div>
</header>

<main>
    <nav class="sidebar">
        <h2>Hotel Info</h2>
        <a href="index.php">Home</a>
        <a href="customers.php">Customers</a>
        <a href="booking.php">Bookings</a>
        <a href="mailto:contact@aurora.hotel">Contact</a>
    </nav>

    <section class="content">
        <h1>New Booking</h1>

        <form action="insertbooking.php" method="post">

            <!-- Customer -->
            <label for="customer">Customer</label>
            <select name="customerid" id="customer" required>
                <option value="" disabled selected>Select customer</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?php echo esc($c['customerid']); ?>"><?php echo esc($c['customername']); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Room -->
            <label for="room">Room</label>
            <select name="roomid" id="room" required>
                <option value="" disabled selected>Select room</option>
                <?php foreach ($rooms as $r): ?>
                    <option value="<?php echo esc($r['roomid']); ?>"><?php echo esc($r['roomname']); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Date From -->
            <label for="datefrom">Date From</label>
            <select name="datefrom" id="datefrom" required>
                <option value="" disabled selected>Select start date</option>
                <?php foreach ($dates as $d): ?>
                    <option value="<?php echo esc($d); ?>"><?php echo esc($d); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Date To -->
            <label for="dateto">Date To</label>
            <select name="dateto" id="dateto" required>
                <option value="" disabled selected>Select end date</option>
                <?php foreach ($dates as $d): ?>
                    <option value="<?php echo esc($d); ?>"><?php echo esc($d); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Create Booking">
        </form>
    </section>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Aurora Hotel • All rights reserved
</footer>

</body>
</html>
