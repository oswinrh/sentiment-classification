<?php
	$server="localhost";
	$user="root";
	$password="";
	$database_name="film";

	//Koneksi ke MYSQL
	mysql_connect($server, $user, $password) or die("Salah username atau password");
	//pilih databasenya
	mysql_select_db($database_name) or die("Gagal terhubung database");

?>