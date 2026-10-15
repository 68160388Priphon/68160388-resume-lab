<?php
require 'db.php';
echo "เชื่อมต่อสำเร็จ<br>";

$result = mysqli_query($conn, "SELECT * FROM skill");
while ($row = mysqli_fetch_assoc($result)) {
    echo htmlspecialchars(print_r($row, true)) . "<br>";
}