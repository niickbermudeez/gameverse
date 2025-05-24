<?php
// $host = "10.0.3.153";
// $user = "gameverse"; 
// $pass = "Educem00."; 
// $dbname = "gameverse"; 

$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "gameverse";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
