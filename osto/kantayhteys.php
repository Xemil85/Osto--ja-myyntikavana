<?php
// Virheentarkistus rivit 3-5 on kommentoitava toimivassa osto ja myynti kanavassa

//ini_set('display_errors','1');
//ini_set('display_startup_errors','1');
//error_reporting(E_ALL);
?>
<?php
$servername = "";
$username = "";
$password = "";
$database = "";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $database);
mysqli_set_charset($dbconnect,'utf8');

// Check connection
if (!$dbconnect) {
  die("Yhteys tietokantaan ei toimi " . mysqli_connect_error());
}
//echo "Yhteys tietokantaan toimii";
?>