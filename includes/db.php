<?php
// Set db properties
$db['db_host'] = "localhost";
$db['db_user'] = "root";
$db['db_pass'] = "";
$db['db_name'] = "portfolioblog";

// Loop through db properties array and set constants
foreach ($db as $key => $value) define(strtoupper($key), $value);

// Connect to MySQL database with above constants
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
?>