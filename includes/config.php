<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_URL', 'http://localhost/footwear/');
define('ASSETS_URL', BASE_URL . 'assets/');

$conn = mysqli_connect('localhost', 'root', '', 'footwear');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>