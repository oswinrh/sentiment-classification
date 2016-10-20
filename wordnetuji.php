<?php
include('koneksi.php');
include('capitalization.php');
include('updatewordnet.php');
ini_set('max_execution_time',0);
?>

<!DOCTYPE HTML>
	<html>
	<head>
		<link rel="shortcut icon" href="data/ukdw3.png">
		<title>WORDNET TESTING</title>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="screen">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
		<style type="text/css">
			.container { 
			    min-height: 100%;
			}
			html, body {
			    height: 100%;
			}
		</style>
	</head>
	<body style="background : url(data/2.jpg);">
	<!--HEADER-->


		<div class="container" style="background-color:white;">
			<div id="header">
				<div class="row" style="background : url(data/home2.png);">
					<div class="col-md-1">
						<img src="data/ukdw3.png" width="90"/>
					</div>
					<div class="col-md-11">
						<h2>MOVIE REVIEW CLASSIFICATION</h2><br/><br/>
					</div>
				</div>
			</div>

			<nav class="navbar navbar-default" >
			<div class="row" style="background-color:#91DBCD;">
	        	<div class="container-fluid" style="background-color:#91DBCD;">
	          		<div>
			            <ul class="nav navbar-nav">
			            	<li><a href="home.php">Home</a></li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Training<span class="caret"></span></a>
			             		<ul class="dropdown-menu">
			              			<li><a href="addlatih.php">Add New Training Document</a></li>
			              			<li><a href="coba.php">Preprocessing and Weighting</a></li>
			            		</ul>
			            	</li>
			              	<li class="active dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Testing<span class="caret"></span></a>
			              		<ul class="dropdown-menu">
			              			<li><a href="adduji.php">Add New Test Document</a></li>
			              			<li style="background-color:#91DBCD;"><a href="wordnetuji.php">Testing Using WordNet</a></li>
			              			<li><a href="pengujianib1.php">Testing Without WordNet</a></li>
			            		</ul>
			              	</li>
			               	<li><a href="wordnet.php">Thesaurus</a></li>
			            </ul>
	          		</div>
	         	</div>
	        </div>
	      </nav>

		<div class="panel panel-default" style="background-color:#C8FAF3;">
	  	<div class="panel-heading">
	    <h4 class="panel-title">Test Using WordNet</h4>
	  	</div>
	  	<div class="panel-body">
			<form action="wordnetuji.php" method="POST" enctype = "multipart/form-data">
				<div class="form-group">
					<label>Amount of Similar Instances</label>
						<select name="instance">
							<option value="1">1</option>
							<option value="3">3</option>
							<option value="5">5</option>
							<option value="7">7</option>
							<option value="9">9</option>
							<option value="59">59</option>
							<option value="59">77</option>
							<option value="99">99</option>
						</select>
				</div>
				<input type="submit" class="btn btn-default" name="wordnetuji" value="Test Using WordNet" data-toggle="modal" data-target="#myModal"/></td>
							<!-- Modal -->
							<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								    <div class="modal-content">
									    <div class="modal-header">
									    	<h4 class="modal-title" id="myModalLabel" style = 'text-align: center;'>Please wait, system is classifying... </h4>
									    </div>
								      	<div class="modal-body">
									      	<!-- Progressbar -->
									        <div class="progress progress-striped active">
												<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
											  </div>
											</div>
											<!-- End of Progressbar -->
								      	</div>
								    </div>
								</div>
							</div>
							<!-- End of Modal -->				
			</form>
		</div>
		</div>

		
<?php
if(!empty($_POST['wordnetuji']))
{
	$tp=0;
	$fp=0;
	$tn=0;
	$fn=0;
	$accordionid = 0;
	$juminstance = $_POST['instance'];

	mysql_query("DELETE FROM klasifikasi");

	//untuk pengujian semua dokumen uji, jabarkan dok uji apa saja
	$queryautoid = mysql_query("SELECT id_preuji FROM preprocessinguji ");
	while($getautoid = mysql_fetch_array($queryautoid))
	{
		$queryuji = $getautoid['id_preuji'];

		$cari = mysql_query("SELECT * FROM preprocessinguji WHERE id_preuji = '$queryuji' ");
 		while($hasilcariuji = mysql_fetch_array($cari))
		{	
	
			mysql_query("CREATE TEMPORARY TABLE bobot SELECT * FROM token");
			mysql_query("DELETE FROM bobot");
			

			mysql_query("CREATE TEMPORARY TABLE uji SELECT * FROM preprocessing");
			mysql_query("DELETE FROM uji");

			$output = mysql_query("SELECT * FROM preprocessinguji WHERE id_preuji = '$queryuji'");

			while($hasil = mysql_fetch_array($output))
			{
				$iduji = $hasil['id_preuji'];
				$kls = $hasil['kelas'];
				$jdl =$hasil['judul_preuji'];
				$isi =$hasil['isipreuji'];
				$tags =$hasil['tag'];
				$preprouji = mysql_query("INSERT INTO uji (id_pre,kelas,judul_pre,isipre,tag) VALUES('$iduji','$kls','$jdl','$isi','$tags')");
				//echo "INSERT INTO uji (id_pre,kelas,judul_pre,isipre,tag) VALUES('$iduji','$kls','$jdl','$isi','$tags')"."<br>";
			}


			$string = mysql_query("SELECT * FROM uji ");
			$delimiter =" ";
			$idtok=0;
			$tempterm = array();
			$tempstop = array();
			$tempstopnama = array();
			$arraylemma = array();
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

						//IMPLEMENTASI WORDNET
					}
				}
		
				$tempterm2 = array();
				$hapus = -1;

				foreach($tempterm as $key => $value)
				{
					if($key != $hapus)
					{
						if ($value == "dont" || $value == "doesnt" || $value == "not" || $value == "cant" || $value == "less" || $value == "non" || $value == "never" || $value == "isnt" || $value == "no" || $value == "wasnt" || $value == "werent" || $value == "wont" || $value == "aint")
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




				foreach($tempstopremoval2 as $key => $value)
				{
						
						$token = mysql_query("SELECT wordid,lemma FROM words WHERE lemma='$value'");
						while($hasiltoken = mysql_fetch_array($token))
						{
							$wordid = $hasiltoken['wordid'];
							$lemma = $hasiltoken['lemma'];

							//echo "Token : ".$value." Lemma : ".$wordid." ".$lemma." ";
							

							$que_seek_synsetid = mysql_query("SELECT * FROM senses WHERE wordid = '$wordid' ORDER BY tagcount DESC LIMIT 1");
							$fetch_synsetid = mysql_fetch_array($que_seek_synsetid);
							
							$synsetid = $fetch_synsetid['synsetid'];
							//echo "<br>Synset ID :".$synsetid." ";	
							
							
							$que_seek_other_wordid_same_synsetid = mysql_query("SELECT s.wordid,w.lemma FROM senses s,words w WHERE s.synsetid = '$synsetid' AND w.wordid=s.wordid ORDER BY s.tagcount DESC LIMIT 1");
							while ($fetch_seek_other_wordid_same_synsetid = mysql_fetch_array($que_seek_other_wordid_same_synsetid))
							{
								$other_wordid = $fetch_seek_other_wordid_same_synsetid['wordid'];
								$other_lemma = $fetch_seek_other_wordid_same_synsetid['lemma'];
								
								if($wordid != $other_wordid)
								{
									$value = $other_lemma;
									//echo "<br><br><b>Replace into : </b>".$other_wordid." | ".$other_lemma;
									array_push($tempstopremoval2, $value);
									
								}

							}
							
						}
				}	




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
					//echo "INSERT INTO bobot(id_token,id_dok,id_kelas,token,tf,tag) VALUES ('$idtok','$idp','$kls','$key','$tf','$tagg')"."<br>";
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

		
				//jabarkan dokumen uji apa saja
				$que_jenis_dok_uji = mysql_query("SELECT id_dok,id_kelas FROM bobot WHERE tag = 'uji' GROUP BY id_dok");
				while ($get_jenis_dok_uji = mysql_fetch_array($que_jenis_dok_uji))
				{
					$current_dok_uji = $get_jenis_dok_uji['id_dok'];
					$current_class_uji = $get_jenis_dok_uji['id_kelas'];

					$simresult = fopen("D:/sim.txt", "w");
					//jabarkan dokumen latih apa saja
					$que_jenis_dok_latih = mysql_query("SELECT id_dok,id_kelas FROM tokenwordnet WHERE tag = 'latih' OR tag = 'latihbaru' GROUP BY id_dok");
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
						$que_token_pair_latih = mysql_query("SELECT token FROM tokenwordnet WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih'");
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
							$que_check_element_in_latih = mysql_query("SELECT normalw FROM tokenwordnet WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
							$count_check_element_in_latih = mysql_num_rows($que_check_element_in_latih);

							
							$que_bobot_just_latih = mysql_query("SELECT normalw FROM tokenwordnet WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
							$get_bobot_just_latih = mysql_fetch_array($que_bobot_just_latih);
							$kedekatan_totallatih += pow($get_bobot_just_latih['normalw'],2);

							$que_bobot_just_uji = mysql_query("SELECT normalw FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji' AND token = '$value'");
							$get_bobot_just_uji = mysql_fetch_array($que_bobot_just_uji);
							$kedekatan_totaluji += pow($get_bobot_just_uji['normalw'],2);

							//cari token yang cuma ada di dok latih atau kembar
							if ($count_check_element_in_uji != 0 && $count_check_element_in_latih != 0)
							{
								
								$que_bobot_uji = mysql_query("SELECT normalw FROM bobot WHERE tag = 'uji' AND id_dok = '$current_dok_uji' AND token = '$value'");
								$get_bobot_uji = mysql_fetch_array($que_bobot_uji);
								$que_bobot_latih = mysql_query("SELECT normalw FROM tokenwordnet WHERE (tag = 'latih' OR tag = 'latihbaru') AND id_dok = '$current_dok_latih' AND token = '$value'");
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



					echo "<div id = 'accordion'>";
					echo "<div class = 'panel panel-default'>";
					echo "<div class = 'panel-heading'>";

					$accordion = "#collapse_".$accordionid;
					$accordionid2 = "collapse_".$accordionid;

					echo "<div class = 'panel-title'>";

					echo "<b>Test Document ".$queryuji."<br><br></b>";
					echo "Test Class : ".$current_class_uji."<br>";

					mysql_query("CREATE TEMPORARY TABLE top5 SELECT * FROM klasifikasi WHERE uji='$queryuji' AND hasil<>0 ORDER BY hasil DESC LIMIT $juminstance");
				
					$quetop5 = mysql_query("SELECT * FROM klasifikasi WHERE uji='$queryuji' AND hasil<>0 ORDER BY hasil DESC LIMIT $juminstance");
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
					$countgab = $countpo + $countne;
					echo "Count Positive : ".$countpo."<br>";
					echo "Count Negative : ".$countne."<br>";
					echo "Count Total : ".$countgab."<br>";
	
			
					if($countpo > $countne && $kuji == 'positive')
					{

						echo "Result Class : ".$kuji."<br><br>";
						echo "<div class = 'row'>";
						echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
						echo "</div>";
						echo "</div></div>";
						echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
						echo "<div class='panel-body'>";

						$tp = $tp + 1;

						updatewordnet($queryuji);

						$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
						?>
						<table class="table">
						<tr>
							<th>Training Doc</th>
							<th>Training Class</th>
							<th>Test Doc</th>
							<th>Test Class</th>
							<th>Similarity Result</th>
						</tr>
						<?php

						while($insert4 = mysql_fetch_array($queshowsim))
						{
							$showdoklatih = $insert4['latih'];
							$showkelaslatih = $insert4['kelaslatih'];
							$showdokuji = $insert4['uji'];
							$showkelasuji = $insert4['kelasuji'];
							$showhasil = $insert4['hasil'];

							?>
							<tr>
								<td><?php echo $showdoklatih; ?></td>
								<td><?php echo $showkelaslatih; ?></td>
								<td><?php echo $showdokuji; ?></td>
								<td><?php echo $showkelasuji; ?></td>
								<td><?php echo $showhasil; ?></td>
							</tr>

							<?php

						}
						?>
						</table>
						</div></div></div></div>
						<br>
						<?php
						$accordionid++;
						

					}
					elseif($countne > $countpo && $kuji == 'negative')
					{

						echo "Result Class : ".$kuji."<br><br>";
						echo "<div class = 'row'>";
						echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
						echo "</div>";
						echo "</div></div>";
						echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
						echo "<div class='panel-body'>";
						


						$tn = $tn + 1;

						
						updatewordnet($queryuji);


						$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
						?>
						<table class="table">
						<tr>
							<th>Training Doc</th>
							<th>Training Class</th>
							<th>Test Doc</th>
							<th>Test Class</th>
							<th>Similarity Result</th>
						</tr>
						<?php

						while($insert4 = mysql_fetch_array($queshowsim))
						{
							$showdoklatih = $insert4['latih'];
							$showkelaslatih = $insert4['kelaslatih'];
							$showdokuji = $insert4['uji'];
							$showkelasuji = $insert4['kelasuji'];
							$showhasil = $insert4['hasil'];

							?>
							<tr>
								<td><?php echo $showdoklatih; ?></td>
								<td><?php echo $showkelaslatih; ?></td>
								<td><?php echo $showdokuji; ?></td>
								<td><?php echo $showkelasuji; ?></td>
								<td><?php echo $showhasil; ?></td>
							</tr>

							<?php

						}
						?>
						</table>
						</div></div></div></div>
						<br>
						<?php
						$accordionid++;


					}
					elseif($countne > $countpo && $kuji == 'positive')
					{

						echo "Result Class : negative"."<br><br>";
						echo "<div class = 'row'>";
						echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
						echo "</div>";
						echo "</div></div>";
						echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
						echo "<div class='panel-body'>";


						$fn = $fn + 1;

						$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
						?>
						<table class="table">
						<tr>
							<th>Training Doc</th>
							<th>Training Class</th>
							<th>Test Doc</th>
							<th>Test Class</th>
							<th>Similarity Result</th>
						</tr>
						<?php

						while($insert4 = mysql_fetch_array($queshowsim))
						{
							$showdoklatih = $insert4['latih'];
							$showkelaslatih = $insert4['kelaslatih'];
							$showdokuji = $insert4['uji'];
							$showkelasuji = $insert4['kelasuji'];
							$showhasil = $insert4['hasil'];

							?>
							<tr>
								<td><?php echo $showdoklatih; ?></td>
								<td><?php echo $showkelaslatih; ?></td>
								<td><?php echo $showdokuji; ?></td>
								<td><?php echo $showkelasuji; ?></td>
								<td><?php echo $showhasil; ?></td>
							</tr>

							<?php

						}
						?>
						</table>
						</div></div></div></div>
						<br>
						<?php
						$accordionid++;

					}
					elseif($countpo > $countne && $kuji == 'negative')
					{

						echo "Result Class : positive"."<br><br>";
						echo "<div class = 'row'>";
						echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
						echo "</div>";
						echo "</div></div>";
						echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
						echo "<div class='panel-body'>";


						$fp = $fp + 1;

						$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
						?>
						<table class="table">
						<tr>
							<th>Training Doc</th>
							<th>Training Class</th>
							<th>Test Doc</th>
							<th>Test Class</th>
							<th>Similarity Result</th>
						</tr>
						<?php

						while($insert4 = mysql_fetch_array($queshowsim))
						{
							$showdoklatih = $insert4['latih'];
							$showkelaslatih = $insert4['kelaslatih'];
							$showdokuji = $insert4['uji'];
							$showkelasuji = $insert4['kelasuji'];
							$showhasil = $insert4['hasil'];

							?>
							<tr>
								<td><?php echo $showdoklatih; ?></td>
								<td><?php echo $showkelaslatih; ?></td>
								<td><?php echo $showdokuji; ?></td>
								<td><?php echo $showkelasuji; ?></td>
								<td><?php echo $showhasil; ?></td>
							</tr>

							<?php

						}
						?>
						</table>
						</div></div></div></div>
						<br>
						<?php
						$accordionid++;
					}
					elseif($countpo == $countne)
					{
						$queseri = mysql_query("SELECT * FROM top5 WHERE uji='$queryuji' AND hasil<>0 ORDER BY hasil DESC LIMIT 1 ");
						while($hasilseri = mysql_fetch_array($queseri))
						{
							$toplatih = $hasilseri['kelaslatih'];
							$topuji = $hasilseri['kelasuji'];

							if ($toplatih == $topuji && $topuji == 'positive')
							{
								$tp = $tp + 1;

								echo "Result Class : ".$topuji."<br><br>";
								echo "<div class = 'row'>";
								echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
								echo "</div>";
								echo "</div></div>";
								echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
								echo "<div class='panel-body'>";

								
								updatewordnet($queryuji);


								$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
								?>
								<table class="table">
								<tr>
									<th>Training Doc</th>
									<th>Training Class</th>
									<th>Test Doc</th>
									<th>Test Class</th>
									<th>Similarity Result</th>
								</tr>
								<?php

								while($insert4 = mysql_fetch_array($queshowsim))
								{
									$showdoklatih = $insert4['latih'];
									$showkelaslatih = $insert4['kelaslatih'];
									$showdokuji = $insert4['uji'];
									$showkelasuji = $insert4['kelasuji'];
									$showhasil = $insert4['hasil'];

									?>
									<tr>
										<td><?php echo $showdoklatih; ?></td>
										<td><?php echo $showkelaslatih; ?></td>
										<td><?php echo $showdokuji; ?></td>
										<td><?php echo $showkelasuji; ?></td>
										<td><?php echo $showhasil; ?></td>
									</tr>

									<?php

								}
								?>
								</table>
								</div></div></div></div>
								<br>
								<?php
								$accordionid = $accordionid + 1;

							}
							elseif($toplatih == $topuji && $topuji == 'negative')
							{
								$tn = $tn + 1;

								echo "Result Class : ".$topuji."<br><br>";
								echo "<div class = 'row'>";
								echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
								echo "</div>";
								echo "</div></div>";
								echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
								echo "<div class='panel-body'>";

								
								updatewordnet($queryuji);


								$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
								?>
								<table class="table">
								<tr>
									<th>Training Doc</th>
									<th>Training Class</th>
									<th>Test Doc</th>
									<th>Test Class</th>
									<th>Similarity Result</th>
								</tr>
								<?php

								while($insert4 = mysql_fetch_array($queshowsim))
								{
									$showdoklatih = $insert4['latih'];
									$showkelaslatih = $insert4['kelaslatih'];
									$showdokuji = $insert4['uji'];
									$showkelasuji = $insert4['kelasuji'];
									$showhasil = $insert4['hasil'];

									?>
									<tr>
										<td><?php echo $showdoklatih; ?></td>
										<td><?php echo $showkelaslatih; ?></td>
										<td><?php echo $showdokuji; ?></td>
										<td><?php echo $showkelasuji; ?></td>
										<td><?php echo $showhasil; ?></td>
									</tr>

									<?php

								}
								?>
								</table>
								</div></div></div></div>
								<br>
								<?php
								$accordionid = $accordionid + 1;						
							}
							elseif($toplatih == 'negative' && $topuji == 'positive')
							{
								$fn = $fn + 1;

								echo "Result Class : negative"."<br><br>";
								echo "<div class = 'row'>";
								echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
								echo "</div>";
								echo "</div></div>";
								echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
								echo "<div class='panel-body'>";

								$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
								?>
								<table class="table">
								<tr>
									<th>Training Doc</th>
									<th>Training Class</th>
									<th>Test Doc</th>
									<th>Test Class</th>
									<th>Similarity Result</th>
								</tr>
								<?php

								while($insert4 = mysql_fetch_array($queshowsim))
								{
									$showdoklatih = $insert4['latih'];
									$showkelaslatih = $insert4['kelaslatih'];
									$showdokuji = $insert4['uji'];
									$showkelasuji = $insert4['kelasuji'];
									$showhasil = $insert4['hasil'];

									?>
									<tr>
										<td><?php echo $showdoklatih; ?></td>
										<td><?php echo $showkelaslatih; ?></td>
										<td><?php echo $showdokuji; ?></td>
										<td><?php echo $showkelasuji; ?></td>
										<td><?php echo $showhasil; ?></td>
									</tr>

									<?php

								}
								?>
								</table>
								</div></div></div></div>
								<br>
								<?php
								$accordionid = $accordionid + 1;
							}
							elseif($toplatih == 'positive' && $topuji == 'negative')
							{
								$fp = $fp + 1;

								echo "Result Class : positive"."<br><br>";
								echo "<div class = 'row'>";
								echo "<div class = 'col-md-offset-10 col-md-2'><a data-toggle ='collapse' data-parent = '#accordion' href='".$accordion."'>Similarity Details</a></div>";
								echo "</div>";
								echo "</div></div>";
								echo "<div id='".$accordionid2."' class='panel-collapse collapse'>";
								echo "<div class='panel-body'>";

								$queshowsim = mysql_query("SELECT * FROM klasifikasi WHERE uji = '$queryuji' ORDER BY hasil DESC LIMIT $juminstance");
								?>
								<table class="table">
								<tr>
									<th>Training Doc</th>
									<th>Training Class</th>
									<th>Test Doc</th>
									<th>Test Class</th>
									<th>Similarity Result</th>
								</tr>
								<?php

								while($insert4 = mysql_fetch_array($queshowsim))
								{
									$showdoklatih = $insert4['latih'];
									$showkelaslatih = $insert4['kelaslatih'];
									$showdokuji = $insert4['uji'];
									$showkelasuji = $insert4['kelasuji'];
									$showhasil = $insert4['hasil'];

									?>
									<tr>
										<td><?php echo $showdoklatih; ?></td>
										<td><?php echo $showkelaslatih; ?></td>
										<td><?php echo $showdokuji; ?></td>
										<td><?php echo $showkelasuji; ?></td>
										<td><?php echo $showhasil; ?></td>
									</tr>

									<?php

								}
								?>
								</table>
								</div></div></div></div>
								<br>
								<?php
								$accordionid = $accordionid + 1;
							}	
						}
					}

					
					mysql_query("DROP TABLE top5");
					
					
				//TUTUP WHILE DOKUJI
				}
			//TUTUP WHILE SELECT *			
			}
		//TUTUP WHILE SELECT IDPREPROUJI
		}
		?>
		<div class="panel panel-default">
		<div class="panel-body">
		<?php
		echo "<b>Evaluation</b><br>";
		echo "<br>TP:".$tp." TN:".$tn." FP:".$fp." FN:".$fn."<br><br>";

		$precisionpositive = $tp/($tp+$fp);
		$recallpositive = $tp/($tp+$fn);
		$fpositive = 2/((1/$precisionpositive)+(1/$recallpositive));

		$precisionnegative = $tn/($tn+$fn);
		$recallnegative = $tn/($tn+$fp);
		$akurasi = (($tp+$tn)/($tp+$tn+$fp+$fn))*100;
		$fnegative = 2/((1/$precisionnegative)+(1/$recallnegative));

		echo "Precision (Class Positive) : ".$precisionpositive."<br>";
		echo "Recall (Class Positive) : ".$recallpositive."<br>";
		echo "F-Measure (Class Positive) : ".$fpositive."<br><br>";

		echo "Precision (Class Negative) : ".$precisionnegative."<br>";
		echo "Recall (Class Negative) : ".$recallnegative."<br>";
		echo "F-Measure (Class Negative) : ".$fnegative."<br><br>";

		echo "System Accuration : ".$akurasi."% <br>";
		?>
		
		</div>
		</div>
<?php
//TUTUP TOMBOL PENGUJIAN
}


?>
	

</div>

				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>