<?php
$host = 'sql100.infinityfree.com';
$user = 'if0_41649406';
$pass = 'cr43wkef';  // ganti dengan password login InfinityFree kamu
$database = 'if0_41649406_kontak';

$conn = mysqli_connect($host, $user, $pass, $database);

if (!$conn) {
    die('Gagal terhubung MySQL: ' . mysqli_connect_error());
}
?>