<?php

require_once 'config.php';

$username = 'ivan';
$password = 'pass';

echo $password . "<br>";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "encoded pass: " . $hashed_password;


$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";

$run = $conn->prepare($sql);
$run->bind_param("ss", $username, $hashed_password);
$run->execute();