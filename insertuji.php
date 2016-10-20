<?php
include('koneksi.php');

if(empty($_POST['ntambah']) || empty($_POST['juduldokuji']) || empty($_POST['isidokuji']))
{
	header("location:adduji.php?fail=1");
}
else
{
	//$uploadfile= $dir.basename($_FILES['ngambar']['name']);
	//move_uploaded_file($_FILES['ngambar']['tmp_name'],"$uploadfile");
	//$id_dokuji=$_POST['id_dokuji'];
	$juduldokuji=mysql_real_escape_string($_POST['juduldokuji']);
	$isidokuji= mysql_real_escape_string($_POST['isidokuji']);
	$kelas=$_POST['kelas'];
	$tag=$_POST['tag'];

	$queryidplus2 = mysql_query("SELECT id_dokuji FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
	$getidplus2 = mysql_fetch_array($queryidplus2);
	$next_id = $getidplus2['id_dokuji'] + 1;

	mysql_query("INSERT INTO dokumenuji (id_dokuji,juduldokuji,isidokuji,kelas,tag) VALUES('$next_id','$juduldokuji','$isidokuji','$kelas','$tag')");
	header("location:adduji.php?success=1");
	
	
}
?>