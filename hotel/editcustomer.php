<?php
require_once 'config.php';
session_start();

function connectdb($hostname, $username, $password, $dbname) {
    try {
        $dsn = "pgsql:host=$hostname;port=5432;dbname=$dbname;";
        $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . htmlspecialchars($e->getMessage()));
    }
}

// Get customer ID from GET parameter
$customerid = isset($_GET['customerid']) ? (int)$_GET['customerid'] : null;
if (!$customerid) { die("No customer ID provided."); }

$_SESSION['customerid'] = $customerid;

$db = connectdb($host, $user, $password, $dbname);

// Fetch customer data
$stmt = $db->prepare('SELECT customerid, customername, customerpostcode FROM customer WHERE customerid = :id');
$stmt->execute(['id' => $customerid]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) { die("Customer not found."); }

function esc($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hotel Aurora • Edit Customer</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(180deg,#021b33,#073d57);
    color: #f2f6fa;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
header { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom:1px solid rgba(255,255,255,0.1); }
.logo { font-size: 24px; font-weight:700; color:#00d9d9; }

main { flex:1; display:flex; gap:24px; padding:30px 20px; width:100%; }

nav.sidebar { flex:0 0 250px; background: rgba(255,255,255,0.03); border-radius:12px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,0.3); display:flex; flex-direction: column; gap:16px; }
nav.sidebar h2 { font-size:18px; margin-bottom:12px; color:#00d9d9; }
nav.sidebar a { color:#fff; text-decoration:none; font-weight:500; padding:8px 12px; border-radius:6px; transition: background 0.2s; }
nav.sidebar a:hover { background: rgba(0,217,217,0.2); }

section.content { flex:1; background: rgba(255,255,255,0.04); padding:30px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.3); overflow-x:auto; }
h1 { font-weight:600; font-size:28px; margin-bottom:20px; color:#fff; }
label { display:block; margin-top:12px; font-weight:600; color:#d8eef6; }
input[type="text"] { width:100%; padding:10px 12px; margin-top:6px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02); color:inherit; }
input[type="submit"] { margin-top:20px; padding:10px 16px; border:none; border-radius:10px; background:#00d9d9; color:#022; font-weight:700; cursor:pointer; transition: background 0.2s ease; }
input[type="submit"]:hover { background:#00c4c4; }

footer { text-align:center; padding:20px; font-size:14px; color:rgba(255,255,255,0.5); border-top:1px solid rgba(255,255,255,0.05); margin-top:30px; }

@media(max-width:900px){
    main { flex-direction: column; }
    nav.sidebar { flex:1; flex-direction:row; overflow-x:auto; padding:12px; }
    nav.sidebar a { white-space:nowrap; margin-right:8px; }
    section.content { margin-top:20px; }
}
</style>
</head>
<body>

<header>
    <div class="logo">Aurora Hotel</div>
    <div>Edit Customer</div>
</header>

<main>
    <!-- LEFT SIDEBAR -->
    <nav class="sidebar">
        <h2>Hotel Info</h2>
        <a href="index.php">Home</a>
        <a href="customers.php">Customers</a>
        <a href="booking.php">Bookings</a>
        <a href="mailto:contact@aurora.hotel">Contact</a>
    </nav>

    <!-- RIGHT CONTENT -->
    <section class="content">
        <h1>Edit Customer</h1>

        <form action="updatecustomer.php?customerid=<?php echo esc($customer['customerid']); ?>" method="post">
            <label for="formname">Name:</label>
            <input type="text" id="formname" name="formname" value="<?php echo esc($customer['customername']); ?>" required>

            <label for="formpostcode">Postcode:</label>
            <input type="text" id="formpostcode" name="formpostcode" value="<?php echo esc($customer['customerpostcode']); ?>" required>

            <input type="submit" value="Save changes">
        </form>
    </section>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Aurora Hotel • All rights reserved
</footer>

</body>
</html>
