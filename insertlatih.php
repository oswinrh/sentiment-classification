<?php
include('koneksi.php');

		if(empty($_POST['ntambah']) || empty($_POST['juduldoklatih']) || empty($_POST['isidoklatih']))
		{
			header("location:addlatih.php?fail=1");
		}
		else
		{
			//$uploadfile= $dir.basename($_FILES['ngambar']['name']);
			//move_uploaded_file($_FILES['ngambar']['tmp_name'],"$uploadfile");
			$id_doklatih=$_POST['id_doklatih'];
			$juduldoklatih=mysql_real_escape_string($_POST['juduldoklatih']);
			$isidoklatih= mysql_real_escape_string($_POST['isidoklatih']);
			$kelas=$_POST['kelas'];
			$tag=$_POST['tag'];

			$queryidplus2 = mysql_query("SELECT id_doklatih FROM dokumenlatih ORDER BY id_doklatih DESC LIMIT 1");
			$getidplus2 = mysql_fetch_array($queryidplus2);
			$next_id = $getidplus2['id_doklatih'] + 1;

			mysql_query("INSERT INTO dokumenlatih (id_doklatih,juduldoklatih,isidoklatih,kelas,tag) VALUES('$next_id','$juduldoklatih','$isidoklatih','$kelas','$tag')");
			
			
			header("location:addlatih.php?success=1");
		}
?>