<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "rpl_hololive";

$db = mysqli_connect($hostname, $username, $password, $database_name);

if ($db->connect_error){
    echo "Koneksi database rusak";
    die("error!");
}
?>