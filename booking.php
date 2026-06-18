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

// Connect to DB
$db = connectdb($host, $user, $password, $dbname);

// Determine month/year from GET params or default to current month
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Compute previous and next months
$prevMonth = $month - 1; $prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1; $nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

// First day and number of days in month
$firstDayOfMonth = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDayOfMonth);
$monthName = date('F', $firstDayOfMonth);
$startWeekDay = date('N', $firstDayOfMonth); // 1=Mon, 7=Sun

// Fetch bookings
$stmt = $db->prepare("
    SELECT b.datefrom, b.dateto, c.customername, r.roomname, b.bookingid
    FROM booking b
    JOIN customer c ON b.customerid = c.customerid
    JOIN room r ON b.roomid = r.roomid
    WHERE (b.datefrom <= :lastDay AND b.dateto >= :firstDay)
");
$firstDayStr = date('Y-m-d', $firstDayOfMonth);
$lastDayStr = date('Y-m-t', $firstDayOfMonth);
$stmt->execute(['firstDay' => $firstDayStr, 'lastDay' => $lastDayStr]);

// Organize bookings by full date (YYYY-MM-DD)
$bookingsByDay = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = strtotime($row['datefrom']);
    $end = strtotime($row['dateto']);
    for ($d = max($start, $firstDayOfMonth); $d <= min($end, strtotime($lastDayStr)); $d += 86400) {
        $dayKey = date('Y-m-d', $d);
        $bookingsByDay[$dayKey][] = [
            'bookingid' => $row['bookingid'],
            'customername' => $row['customername'],
            'roomname' => $row['roomname']
        ];
    }
}

// Helper to escape HTML
function esc($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hotel Aurora • Booking Calendar</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Poppins',sans-serif; background:linear-gradient(180deg,#021b33,#073d57); color:#f2f6fa; min-height:100vh; display:flex; flex-direction:column; }
header { background:rgba(255,255,255,0.05); backdrop-filter:blur(10px); padding:20px 40px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); }
.logo { font-size:24px; font-weight:700; color:#00d9d9; }
main { flex:1; display:flex; gap:24px; padding:30px 20px; width:100%; }
nav.sidebar { flex:0 0 250px; background: rgba(255,255,255,0.03); border-radius:12px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,0.3); display:flex; flex-direction: column; gap:16px; }
nav.sidebar h2 { font-size:18px; margin-bottom:12px; color:#00d9d9; }
nav.sidebar a { color:#fff; text-decoration:none; font-weight:500; padding:8px 12px; border-radius:6px; transition:background 0.2s; }
nav.sidebar a:hover { background: rgba(0,217,217,0.2); }
section.content { flex:1; background: rgba(255,255,255,0.04); padding:30px; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.3); overflow-x:auto; }
h1 { font-weight:600; font-size:28px; margin-bottom:20px; color:#fff; text-align:center; }
.calendar-nav { display:flex; justify-content:center; align-items:center; gap:20px; margin-bottom:20px; font-weight:600; }
.calendar-nav a { color:#00d9d9; text-decoration:none; }
.calendar-nav a:hover { text-decoration:underline; }
.calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
.calendar div { padding:10px; min-height:60px; border-radius:6px; background: rgba(255,255,255,0.02); display:flex; flex-direction:column; justify-content:flex-start; }
.calendar .header { font-weight:600; text-align:center; background: rgba(0,217,217,0.2); color:#00d9d9; }
.calendar .day { cursor:pointer; transition: background 0.2s; }
.calendar .day:hover { background: rgba(0,217,217,0.15); }
.booking { font-size:0.8em; margin-top:4px; color:#fff; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
footer { text-align:center; padding:20px; font-size:14px; color:rgba(255,255,255,0.5); border-top:1px solid rgba(255,255,255,0.05); margin-top:30px; }
@media(max-width:900px){ main { flex-direction:column; } nav.sidebar { flex:1; flex-direction:row; overflow-x:auto; padding:12px; } nav.sidebar a { white-space:nowrap; margin-right:8px; } section.content { margin-top:20px; } }
.booking a {
    color: #00d9d9;          /* Make booking links stand out */
    text-decoration: none;    /* No underline */
    font-weight: 600;         /* Slightly bolder */
    font-size: 0.85em;        /* Smaller than day number */
    display: block;           /* Makes the clickable area fill the div */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.booking a:hover {
    text-decoration: underline;
    color: #00ffff;
}
</style>
</head>
<body>

<header>
    <div class="logo">Aurora Hotel</div>
    <div>Booking Calendar</div>
</header>

<main>
    <!-- Sidebar -->
    <nav class="sidebar">
        <h2>Hotel Info</h2>
        <a href="index.php">Home</a>
        <a href="customers.php">Customers</a>
        <a href="booking.php">Bookings</a>
        <a href="mailto:contact@aurora.hotel">Contact</a>
    </nav>

    <!-- Content -->
    <section class="content">
        <div class="calendar-nav">
            <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>">&lt; <?php echo esc(date('F Y', strtotime("$prevYear-$prevMonth-01"))); ?></a>
            <span><?php echo esc($monthName . " " . $year); ?></span>
            <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>"><?php echo esc(date('F Y', strtotime("$nextYear-$nextMonth-01"))); ?> &gt;</a>
        </div>

        <div class="calendar">
            <?php
            // Weekday headers
            $weekdays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
            foreach ($weekdays as $wd) {
                echo '<div class="header">' . esc($wd) . '</div>';
            }

            // Empty cells before first day
            for ($i = 1; $i < $startWeekDay; $i++) { echo '<div></div>'; }

            // Days of the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayDate = date('Y-m-d', strtotime("$year-$month-$day"));
                echo '<div class="day">';
                echo '<strong>' . $day . '</strong>';

                if (!empty($bookingsByDay[$dayDate])) {
                    foreach ($bookingsByDay[$dayDate] as $booking) {
                        echo '<div class="booking">';
                        echo '<a href="viewbooking.php?bookingid=' . urlencode($booking['bookingid']) . '">'
                             . esc($booking['customername']) . ' (' . esc($booking['roomname']) . ')</a>';
                        echo '</div>';
                    }
                }

                echo '</div>';
            }

            // Trailing empty cells to complete last week
            $endWeekDay = ($startWeekDay + $daysInMonth - 1) % 7;
            if ($endWeekDay != 0) {
                for ($i = $endWeekDay + 1; $i <= 7; $i++) {
                    echo '<div></div>';
                }
            }
            ?>
        </div>
    </section>
</main>

<footer>
    &copy; <?php echo date('Y'); ?> Aurora Hotel • All rights reserved
</footer>

</body>
</html>
