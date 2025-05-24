<?php
// deklarasi parameter koneksi database
$server   = "localhost";
$username = "root";
$password = "";
$database = "";

// koneksi database
$koneksi = mysqli_connect($server, $username, $password, $database);

// cek koneksi
if (!$koneksi) {
    die('Koneksi Database Gagal : ');
}

(isset($_GET['pg'])) ? $pg = $_GET['pg'] : $pg = '';
(isset($_GET['ac'])) ? $ac = $_GET['ac'] : $ac = '';

// SETTING WAKTU
date_default_timezone_set("Asia/Jakarta");

$uri = "http://localhost:8080/ESchool_M";

define('BASEPATH', str_replace("config", "", dirname(__FILE__)));
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
