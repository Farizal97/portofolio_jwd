<?php
// cspell:disable

$host     = "localhost";
$username = "root";
$password = "root";
$database = "db_portofolio";


$koneksi = mysqli_connect($host, $username, $password, $database);


if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
