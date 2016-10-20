<?php
include('koneksi.php');
include('capitalization.php');
ini_set('max_execution_time',0);

if(!empty($_POST['addnewreview']))
{
	mysql_query("DELETE FROM klasifikasi");
	$idreview = $_GET['review'];

	$showmovie = mysql_query("SELECT * FROM film WHERE idfilm = '$idreview'");
	$hasilmovie = mysql_fetch_array($showmovie);
	$judulreview = $hasilmovie['judulfilm'];

	$captain = mysql_query("SELECT * FROM dokumenlatih WHERE juduldoklatih = '$judulreview'");
	$getcaptain = mysql_fetch_array($captain);
	$juduldokuji=$getcaptain['juduldokuji'];
	$isidokuji= mysql_real_escape_string($_POST['isidokuji']);
	//$kelas=$_POST['kelas'];
	//$tag=$_POST['tag'];
	$queryidplus2 = mysql_query("SELECT id_dokuji FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
	$getidplus2 = mysql_fetch_array($queryidplus2);
	$next_id = $getidplus2['id_dokuji'] + 1;
	mysql_query("INSERT INTO dokumenuji(id_dokuji,juduldokuji,isidokuji,kelas,tag) VALUES('$next_id','$judulreview','$isidokuji','','uji')");



	$inputpreprouji = mysql_query("SELECT * FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
	//PREPROCESSING : LOWER CASE, NON-ALPHANUMERIC REMOVAL
	while($hasiluji = mysql_fetch_array($inputpreprouji))
	{

		$iduji1 = $hasiluji['id_dokuji'];
		$kelasuji=$hasiluji['kelas'];
		$juduluji=$hasiluji['juduldokuji'];
		$hasil2= capitalization($hasiluji['isidokuji']);
		$labeluji=$hasiluji['tag'];
		mysql_query("INSERT INTO preprocessinguji(id_preuji,kelas,judul_preuji,isipreuji,tag) VALUES('$iduji1','$kelasuji','$juduluji','$hasil2','$labeluji')");
	}



	mysql_query("CREATE TEMPORARY TABLE bobot SELECT * FROM token");
	mysql_query("DELETE FROM bobot");
	

	mysql_query("CREATE TEMPORARY TABLE uji SELECT * FROM preprocessing");
	mysql_query("DELETE FROM uji");

	$output = mysql_query("SELECT * FROM preprocessinguji ORDER BY id_preuji DESC LIMIT 1");
	while($hasil = mysql_fetch_array($output))
	{
		$iduji = $hasil['id_preuji'];
		$kls = $hasil['kelas'];
		$jdl =$hasil['judul_preuji'];
		$isi =$hasil['isipreuji'];
		$tags =$hasil['tag'];
		mysql_query("INSERT INTO uji(id_pre,kelas,judul_pre,isipre,tag) VALUES('$iduji','$kls','$jdl','$isi','$tags')");
		//echo "INSERT INTO uji (id_pre,kelas,judul_pre,isipre,tag) VALUES('$iduji','$kls','$jdl','$isi','$tags')"."<br>";
	}

	$string = mysql_query("SELECT * FROM uji ");
	$delimiter =" ";
	$idtok=0;
	$tempterm = array();
	$tempstop = array();
	$tempstopnama = array();
	//$temptoken = array();

	//TOKENISASI DENGAN DELIMITER SPASI - TAMPUNG TOKEN KE ARRAY $tempterm
	while($hasiltoken = mysql_fetch_assoc($string))
	{
	$idp=$hasiltoken['id_pre'];
	$isip=$hasiltoken['isipre'];
	$kls=$hasiltoken['kelas'];
	$tagg=$hasiltoken['tag'];

	$tok = explode($delimiter, $isip);

	for ($i=0; $i < count($tok) ; $i++)
	{ 
		
		if($tok[$i] != '')
		{
			//$idtok ++;
			//mysql_query("INSERT INTO token(id_token,id_dok,id_kelas,token) VALUES('$idtok','$idp','$kls','$tok[$i]')");
			array_push($tempterm, $tok[$i]);
		}
	}

	$tempterm2 = array();
	$hapus = -1;

	foreach($tempterm as $key => $value)
	{
		if($key != $hapus)
		{
			if ($value == "dont" || $value == "doesnt" || $value == "not" || $value == "cant" || $value == "less" || $value == "non" || $value == "never" || $value == "isnt" || $value == "no")
			{
				$temp = $value." ".$tempterm[$key+1];
				//array_splice($tempterm, $key+1, 1);
				$hapus = $key + 1;
			}
			else
			{
				$temp = $value;
			}
			array_push($tempterm2, $temp);
		}	
	}


	//STOPWORD REMOVAL DENGAN array_diff
	$stop = mysql_query("SELECT stopword FROM stopword");
	while($hasilstop = mysql_fetch_assoc($stop))
	{
		array_push($tempstop, $hasilstop['stopword']);
	}

	$tempstopremoval = array_diff($tempterm2, $tempstop);

	$stopnama = mysql_query("SELECT nama FROM stopwordnama");
	while($hasilstopnama = mysql_fetch_assoc($stopnama))
	{
		array_push($tempstopnama, $hasilstopnama['nama']);
	}

	$tempstopremoval2 = array_diff($tempstopremoval, $tempstopnama);


	//HITUNG JUMLAH ELEMEN ARRAY SETELAH DI HILANGI STOPWORD
	$frek = array_count_values($tempstopremoval2);

	//UNTUK TIAP ELEMEN, HITUNG TF DAN MASUKAN TOKEN (distinct), frek kemunculan DAN NILAI TF KE DATABASE
	foreach($frek as $key => $value)
	{
		$idtok++;
		
		//$tfperkelas = mysql_query("SELECT id_token, id_dok, id_kelas, token, frek FROM token WHERE id_kelas=1 ");
		
		//$totaldoctoken = count($tempstopremoval2);
		//$tf = $value / $totaldoctoken;
		$tf = $value;
		mysql_query("INSERT INTO bobot(id_token,id_dok,id_kelas,token,tf,tag) VALUES ('$idtok','$idp','$kls','$key','$tf','$tagg')");
		//echo "INSERT INTO bobot(id_token,id_dok,id_kelas,token,frek,tf,tag) VALUES ('$idtok','$idp','$kls','$key','$value','$tf','$tagg')"."<br>";
	}

	//HAPUS KEY DAN VALUE ARRAY
	foreach ($tempterm as $i => $value) 
	{
		unset($tempterm[$i]);
	}

	foreach ($tempterm2 as $i => $value) 
	{
		unset($tempterm2[$i]);
	}

	foreach ($tempstop as $i => $value) 
	{
		unset($tempstop[$i]);
	}

	foreach ($tempstopnama as $i => $value) 
	{
		unset($tempstopnama[$i]);
	}

	foreach ($frek as $i => $value) 
	{
		unset($frek[$i]);
	}


	}
	//mysql_query("DELETE FROM token WHERE token IN(SELECT stopword FROM stopword)");
	//mysql_query("DELETE FROM token WHERE token IN(SELECT nama FROM stopwordnama)");



	//PEMBOBOTAN IDF
	//tampung hasil dalam array, index ke 0 diisi spy index mulai dari index ke 1 karena id mulai dr 1
	$temptoken = array("0" => " ");
	$tempdf = array("0" => " ");
	$tempidf = array("0" => " ");
	$tempw = array("0" => " ");

	$qtoken = mysql_query("SELECT id_token,token FROM bobot");
		
	while ($t = mysql_fetch_array($qtoken))
	{
		array_push($temptoken, $t['token']);
	}
	unset($temptoken[0]);

	foreach($temptoken as $key => $value)
	{
		$qdf = mysql_query("SELECT id_dok FROM bobot WHERE token = '$value'");
		$df = mysql_num_rows($qdf);
		array_push($tempdf, $df);

	}
	unset($tempdf[0]);


	foreach($tempdf as $key => $value)
	{
		$qn = mysql_query("SELECT id_dok FROM bobot GROUP BY id_dok");
		$n = mysql_num_rows($qn);
		$idf = log10(($n / $value)+1);
		array_push($tempidf, $idf);

	}
	unset($tempidf[0]);

	foreach ($tempidf as $key => $value)
	{
		$qtf = mysql_query("SELECT tf FROM bobot WHERE id_token = '$key'");
		$retrievetf = mysql_fetch_assoc($qtf);
		$tf = $retrievetf['tf'];
		$tfidf = $tf * $value;
		array_push($tempw, $tfidf);

	}
	unset($tempw[0]);

	foreach ($tempw as $key => $value)
	{
		
		$min = min($tempw);
		$max = max($tempw);
		/*if($min == $max )
		{
			$min = 0;
			$max = $value;
			$norm = ($value - $min) / ($max - $min);
		}
		else
		{
			$min = min($tempw);
			$max = max($tempw);
			$norm = ($value - $min) / ($max - $min);	
		}*/
		$norm = $value/$max ;
		
		//mysql_query("INSERT INTO bobot(df,idf,w,normalw) VALUES('$tempdf[$key]','$tempidf[$key]','$value','$norm')");
		//echo "INSERT INTO bobot(df,idf,w,normalw) VALUES('$tempdf[$key]','$tempidf[$key]','$value','$norm')"."<br>";
		mysql_query("UPDATE bobot SET df ='$tempdf[$key]' ,idf = '$tempidf[$key]', w = '$value', normalw = '$norm' WHERE id_token = '$key'");
		//echo "UPDATE bobot SET df ='$tempdf[$key]' ,idf = '$tempidf[$key]', w = '$value', normalw = '$norm' WHERE id_token = '$key'"."<br>";

	}

	//KLASIFIKASI
	//jabarkan dokumen uji apa saja
	$que_jenis_dok_uji = mysql_query("SELECT id_dok,id_kelas FROM bobot WHERE tag = 'uji' GROUP BY id_dok");
	while ($get_jenis_dok_uji = mysql_fetch_array($que_jenis_dok_uji))
	{
		$current_dok_uji = $get_jenis_dok_uji['id_dok'];
		$current_class_uji = $get_jenis_dok_uji['id_kelas'];

		$simresult = fopen("D:/sim.txt", "w");
		//jabarkan dokumen latih apa saja
		$que_jenis_dok_latih = mysql_query("SELECT id_dok,id_kelas FROM token WHERE tag = 'latih' OR tag = 'latihbaru' GROUP BY id_dok");
		while ($get_jenis_dok_latih = mysql_fetch_array($que_jenis_dok_latih))
		{
			$current_dok_latih = $get_jenis_dok_latih['id_dok'];
			$current_class_latih = $get_jenis_dok_latih['id_kelas'];
			$kedekatan_total = 0;
			$kedekatan_totallatih = 0;
			$kedekatan_totaluji = 0;
			$simlatih = 0;
			$simuji = 0;

			//array untuk bobot token uji - bobot token latih
			$arr_token_pair_uji_latih = array();

			//jabarkan token dok uji apa saja
			$que_token_pair_uji = mysql_query("SELECT token FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji'");
			while ($get_token_pair_uji = mysql_fetch_array($que_token_pair_uji))
			{
				array_push($arr_token_pair_uji_latih, $get_token_pair_uji['token']);
			}
			//jabarkan token dok latih apa saja
			$que_token_pair_latih = mysql_query("SELECT token FROM token WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih'");
			while ($get_token_pair_latih = mysql_fetch_array($que_token_pair_latih))
			{
				array_push($arr_token_pair_uji_latih, $get_token_pair_latih['token']);
			}
			//agar data token tidak redundan
			$arr_token_pair_uji_latih = array_unique($arr_token_pair_uji_latih);

			//perulangan untuk bobot token
			foreach ($arr_token_pair_uji_latih as $key => $value)
			{
				//token uji untuk id dokumen uji saat itu
				$que_check_element_in_uji = mysql_query("SELECT normalw FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji' AND token = '$value'");
				$count_check_element_in_uji = mysql_num_rows($que_check_element_in_uji);

				////token latih untuk id dokumen latih saat itu
				$que_check_element_in_latih = mysql_query("SELECT normalw FROM token WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
				$count_check_element_in_latih = mysql_num_rows($que_check_element_in_latih);

				
				$que_bobot_just_latih = mysql_query("SELECT normalw FROM token WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
				$get_bobot_just_latih = mysql_fetch_array($que_bobot_just_latih);
				$kedekatan_totallatih += pow($get_bobot_just_latih['normalw'],2);

				$que_bobot_just_uji = mysql_query("SELECT normalw FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji' AND token = '$value'");
				$get_bobot_just_uji = mysql_fetch_array($que_bobot_just_uji);
				$kedekatan_totaluji += pow($get_bobot_just_uji['normalw'],2);

				//cari token yang cuma ada di dok latih
				if ($count_check_element_in_uji != 0 && $count_check_element_in_latih != 0)
				{
					
					$que_bobot_uji = mysql_query("SELECT normalw FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji' AND token = '$value'");
					$get_bobot_uji = mysql_fetch_array($que_bobot_uji);
					$que_bobot_latih = mysql_query("SELECT normalw FROM token WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
					$get_bobot_latih = mysql_fetch_array($que_bobot_latih);
					//$simlatih += $get_bobot_latih['normalw'];
					//$simuji += $get_bobot_uji['normalw'];
					$total_bobot_pair_uji_latih = ($get_bobot_uji['normalw'] * $get_bobot_latih['normalw']);
					$kedekatan_total += $total_bobot_pair_uji_latih;
					
				}
			}
			$sim_latih = sqrt($kedekatan_totallatih);
			$sim_uji = sqrt($kedekatan_totaluji);
			$kedekatan_akar = $kedekatan_total/($sim_latih * $sim_uji);
			/*echo "<br>";
			echo "ID DOK UJI: ".$current_dok_uji."<br>";
			echo "KELAS UJI: ".$current_class_uji."<br>";
			echo "ID DOK LATIH: ".$current_dok_latih."<br>";
			echo "KELAS LATIH: ".$current_class_latih."<br>";
			echo "KEDEKATAN: ".$kedekatan_akar."<hr>";*/
			unset($arr_token_pair_uji_latih);

			$simresult = fopen("D:/sim.txt", "a");
			$simtxt = $current_dok_latih.",".$current_class_latih.",".$current_dok_uji.",".$current_class_uji.",".$kedekatan_akar.PHP_EOL;
			fwrite($simresult, $simtxt);
			fclose($simresult);

			//mysql_query("INSERT INTO klasifikasi(latih,kelaslatih,uji,kelasuji,hasil) VALUES('$current_dok_latih','$current_class_latih','$current_dok_uji','$current_class_uji','$kedekatan_akar')");
			//echo "INSERT INTO klasifikasi(latih,kelaslatih,uji,kelasuji,hasil) VALUES('$current_dok_latih','$current_class_latih','$current_dok_uji','$current_class_uji','$kedekatan_akar')"."<br>";

		}
		mysql_query("LOAD DATA INFILE 'D:/sim.txt' INTO TABLE klasifikasi FIELDS TERMINATED BY ','");



		//PEMILIHAN KELAS	
		mysql_query("CREATE TEMPORARY TABLE top5 SELECT * FROM klasifikasi WHERE uji='$iduji' AND hasil<>0 ORDER BY hasil DESC LIMIT 5");
	
		$quetop5 = mysql_query("SELECT * FROM klasifikasi WHERE uji='$iduji' AND hasil<>0 ORDER BY hasil DESC LIMIT 5");
		$gettop5 = mysql_fetch_array($quetop5);
		//while($gettop5 = mysql_fetch_array($quetop5))
		
			//echo $gettop5['latih']."<br>";
			//echo $gettop5['kelaslatih']."<br>";
			//echo $gettop5['uji']."<br>";
			//echo $gettop5['kelasuji']."<br>";
			//echo $gettop5['hasil']."<br><br>";
			$kuji = $gettop5['kelasuji'];
			//$klatih = $gettop5['kelaslatih'];
			//$hasiltop = $gettop5['hasil'];
			$querypo = mysql_query("SELECT kelaslatih FROM top5 WHERE kelaslatih='positive'");			
			$queryne = mysql_query("SELECT kelaslatih FROM top5 WHERE kelaslatih='negative'");
			$countpo = mysql_num_rows($querypo);
			$countne = mysql_num_rows($queryne);
			echo "Jumlah Kelas Positive : ".$countpo."<br>";
			echo "Jumlah Kelas Negative : ".$countne."<br>";
		
			
			if($countpo > $countne)
			{
				//echo "Class : ".$kuji."<br><br>";

				$queinsert2 = mysql_query("SELECT * FROM preprocessinguji ORDER BY id_preuji DESC LIMIT 1");
 				while($insert2 = mysql_fetch_array($queinsert2))
				{
					$idpreuji = $insert2['id_preuji'];
					$kelasnyauji = $insert2['kelas'];
					$judulpreprouji = $insert2['judul_preuji'];
					$isipreprouji = $insert2['isipreuji'];
					$tagpreuji = $insert2['tag'];
					
					mysql_query("UPDATE preprocessinguji SET kelas='positive' WHERE id_preuji='$idpreuji'");
				}
				$queinsert3 = mysql_query("SELECT * FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
				while($insert3 = mysql_fetch_array($queinsert3))
				{
					$iddokuji = $insert3['id_dokuji'];
					$kelasdokuji = $insert3['kelas'];
					$jdldokuji = $insert3['juduldokuji'];
					$isiduji = $insert3['isidokuji'];
					$tagdokuji = $insert3['tag'];
					
					mysql_query("UPDATE dokumenuji SET kelas='positive' WHERE id_dokuji='$iddokuji'");
					//mysql_query("INSERT INTO dokumenlatih(id_doklatih,kelas,juduldoklatih,isidoklatih,tag) VALUES('$iddokuji','$kelasdokuji','$jdldokuji','$isiduji','latihbaru')");
				}

			}
			elseif($countne > $countpo)
			{

				
				$queinsert2 = mysql_query("SELECT * FROM preprocessinguji ORDER BY id_preuji DESC LIMIT 1");
 				while($insert2 = mysql_fetch_array($queinsert2))
				{
					$idpreuji = $insert2['id_preuji'];
					$kelasnyauji = $insert2['kelas'];
					$judulpreprouji = $insert2['judul_preuji'];
					$isipreprouji = $insert2['isipreuji'];
					$tagpreuji = $insert2['tag'];
					
					mysql_query("UPDATE preprocessinguji SET kelas='negative' WHERE id_preuji='$idpreuji'");
					
				}
				$queinsert3 = mysql_query("SELECT * FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
				while($insert3 = mysql_fetch_array($queinsert3))
				{
					$iddokuji = $insert3['id_dokuji'];
					$kelasdokuji = $insert3['kelas'];
					$jdldokuji = $insert3['juduldokuji'];
					$isiduji = $insert3['isidokuji'];
					$tagdokuji = $insert3['tag'];

					mysql_query("UPDATE dokumenuji SET kelas='negative' WHERE id_dokuji='$iddokuji'");
				}

			}
			elseif($countne == $countpo)
			{
				$queseri = mysql_query("SELECT * FROM top5 WHERE hasil<>0 ORDER BY hasil DESC LIMIT 1 ");
					while($hasilseri = mysql_fetch_array($queseri))
					{
						$toplatih = $hasilseri['kelaslatih'];
						$topuji = $hasilseri['kelasuji'];

						if ($toplatih == $topuji && $topuji == 'positive')
						{
							$queinsert2 = mysql_query("SELECT * FROM preprocessinguji ORDER BY id_preuji DESC LIMIT 1");
			 				while($insert2 = mysql_fetch_array($queinsert2))
							{
								$idpreuji = $insert2['id_preuji'];
								$kelasnyauji = $insert2['kelas'];
								$judulpreprouji = $insert2['judul_preuji'];
								$isipreprouji = $insert2['isipreuji'];
								$tagpreuji = $insert2['tag'];
								
								mysql_query("UPDATE preprocessinguji SET kelas='positive' WHERE id_preuji='$idpreuji'");
							}
							$queinsert3 = mysql_query("SELECT * FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
							while($insert3 = mysql_fetch_array($queinsert3))
							{
								$iddokuji = $insert3['id_dokuji'];
								$kelasdokuji = $insert3['kelas'];
								$jdldokuji = $insert3['juduldokuji'];
								$isiduji = $insert3['isidokuji'];
								$tagdokuji = $insert3['tag'];
								
								mysql_query("UPDATE dokumenuji SET kelas='positive' WHERE id_dokuji='$iddokuji'");
								//mysql_query("INSERT INTO dokumenlatih(id_doklatih,kelas,juduldoklatih,isidoklatih,tag) VALUES('$iddokuji','$kelasdokuji','$jdldokuji','$isiduji','latihbaru')");
							}	
						}
						elseif($toplatih == $topuji && $topuji == 'negative')
						{
							$queinsert2 = mysql_query("SELECT * FROM preprocessinguji ORDER BY id_preuji DESC LIMIT 1");
			 				while($insert2 = mysql_fetch_array($queinsert2))
							{
								$idpreuji = $insert2['id_preuji'];
								$kelasnyauji = $insert2['kelas'];
								$judulpreprouji = $insert2['judul_preuji'];
								$isipreprouji = $insert2['isipreuji'];
								$tagpreuji = $insert2['tag'];
								
								mysql_query("UPDATE preprocessinguji SET kelas='negative' WHERE id_preuji='$idpreuji'");
								
							}
							$queinsert3 = mysql_query("SELECT * FROM dokumenuji ORDER BY id_dokuji DESC LIMIT 1");
							while($insert3 = mysql_fetch_array($queinsert3))
							{
								$iddokuji = $insert3['id_dokuji'];
								$kelasdokuji = $insert3['kelas'];
								$jdldokuji = $insert3['juduldokuji'];
								$isiduji = $insert3['isidokuji'];
								$tagdokuji = $insert3['tag'];

								mysql_query("UPDATE dokumenuji SET kelas='negative' WHERE id_dokuji='$iddokuji'");
							}

						}

				}
			}
			
}		
				


header("location:detailfilm.php?film=".$idreview);
}

?>