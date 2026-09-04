// for this test visit: http://thenest.umbrellacorp.top/db_test.php

<?php
include("_config.php");

echo "<h2>Anslutning till databasen lyckades!</h2>";

$result = $conn->query("SELECT id, employeeCode, logintimes, lastlogin FROM users");
echo "<table border='1'><tr><th>ID</th><th>Employee Code</th><th>Logins</th><th>Last Login</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['employeeCode']}</td><td>{$row['logintimes']}</td><td>{$row['lastlogin']}</td></tr>";
}
echo "</table>";

$conn->close();
?>