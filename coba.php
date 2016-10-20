<?php
include('koneksi.php');
include('capitalization.php');
ini_set('max_execution_time',0);
?>

<!DOCTYPE HTML>
	<html>
	<head>
		<link rel="shortcut icon" href="data/ukdw3.png">
		<title>TRAINING</title>
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
			              	<li class="active dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Training<span class="caret"></span></a>
			             		<ul class="dropdown-menu">
			              			<li><a href="addlatih.php">Add New Training Document</a></li>
			              			<li style="background-color:#91DBCD;"><a href="coba.php">Preprocessing and Weighting</a></li>
			            		</ul>
			            	</li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Testing<span class="caret"></span></a>
			              		<ul class="dropdown-menu">
			              			<li><a href="adduji.php">Add New Test Document</a></li>
			              			<li><a href="wordnetuji.php">Testing Using WordNet</a></li>
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
	    <h4 class="panel-title">Preprocessing : Case Folding (Lowercase and Alphabetic)</h4>
	  	</div>
	  	<div class="panel-body">

			<div>
			<form action="" method="POST" enctype = "multipart/form-data">
				<table>
					<tr><td colspan="5" align="right">
						<input type="submit" class="btn btn-default" name="prepro" value="Lowercase"/>
						
					</td></tr>
				</table>
			</form>
			</div>
		</div>
		</div>

		<br/><br/>
		<div class="panel panel-default" style="background-color:#C8FAF3;">
	  	<div class="panel-heading">
	    <h4 class="panel-title">Preprocessing : Tokenization, Stopword Removal, TF-IDF</h4>
	  	</div>
	  	<div class="panel-body">
			
			<div>
			<form action="" method="POST" enctype = "multipart/form-data">
				<table>
					<tr><td colspan="5" align="right">
						<input type="submit" class="btn btn-default" name="tfidf" value="Token" data-toggle="modal" data-target="#myModal"/>

							<!-- Modal -->
							<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								    <div class="modal-content">
									    <div class="modal-header">
									    	<h4 class="modal-title" id="myModalLabel" style = 'text-align: center;'>Please wait, system is preprocessing... </h4>
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

					</td></tr>
				</table>
			</form>
			</div>

			<br>
			<div>
			<form action="wordnetlatih.php" method="POST" enctype = "multipart/form-data">
				<table>
					<tr><td colspan="5" align="right">
						<input type="submit" class="btn btn-default" name="wordnet2" value="Token + WordNet" data-toggle="modal" data-target="#myModal2"/>

							<!-- Modal -->
							<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
								    <div class="modal-content">
									    <div class="modal-header">
									    	<h4 class="modal-title" id="myModalLabel" style = 'text-align: center;'>Please wait, system is preprocessing... </h4>
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
					</td></tr>
				</table>
			</form>
			</div>

		</div>
		</div>


<?php
/*if(!empty($_POST['id_doklatih']))$id_doklatih=$_POST['id_doklatih'];
if(!empty($_POST['juduldoklatih']))$juduldoklatih=$_POST['juduldoklatih'];
if(!empty($_POST['isidoklatih']))$isidoklatih=$_POST['isidoklatih'];
if(!empty($_POST['kelas']))$kelas=$_POST['kelas'];
if(!empty($_POST['tag']))$tanda=$_POST['tag'];*/
//if(!empty($_POST['ntambah']))$nsubmit=$_POST['ntambah'];
if(!empty($_POST['prepro']))$preprobutton=$_POST['prepro'];
if(!empty($_POST['tfidf']))$tokenbutton=$_POST['tfidf'];
?>

<br><br>

<?php
if(!empty($preprobutton))
{
	mysql_query("DELETE FROM preprocessing");
	$input = mysql_query("SELECT id_doklatih,kelas,juduldoklatih,isidoklatih,tag FROM dokumenlatih");

	//PREPROCESSING : LOWER CASE, NON-ALPHANUMERIC REMOVAL
	while($hasil = mysql_fetch_assoc($input))
	{

		$idlatih = $hasil['id_doklatih'];
		$kelas=$hasil['kelas'];
		$judul=$hasil['juduldoklatih'];
		$hasil2= capitalization($hasil['isidoklatih']);
		$label=$hasil['tag'];
		$prepro1 = mysql_query("INSERT INTO preprocessing(id_pre,kelas,judul_pre,isipre,tag) VALUES('$idlatih','$kelas','$judul','$hasil2','$label')");

	}

	$output = mysql_query("SELECT * FROM preprocessing ORDER BY id_pre DESC");
	?>
	<table class="table">
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Preprocessed Training Review</th>
		</tr>
		<?php
		while($hasiloutput = mysql_fetch_array($output))
		{?>
		<tr>
			<td><?php echo $hasiloutput['id_pre']; ?></td>
			<td><?php echo $hasiloutput['judul_pre']; ?></td>
			<td><?php echo $hasiloutput['isipre']; ?></td>
		</tr>
		<?php } ?>
	</table>
	
<?php
}
?>

<?php

if(!empty($tokenbutton))
{
	mysql_query("DELETE FROM token");
	$string = mysql_query("SELECT id_pre,isipre,kelas FROM preprocessing");
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
		mysql_query("INSERT INTO token(id_token,id_dok,id_kelas,token,tf) VALUES ('$idtok','$idp','$kls','$key','$value')");
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

	$qtoken = mysql_query("SELECT id_token,token FROM token");
		
	while ($t = mysql_fetch_assoc($qtoken))
	{
		array_push($temptoken, $t['token']);
	}
	unset($temptoken[0]);

	foreach($temptoken as $key => $value)
	{
		$qdf = mysql_query("SELECT id_dok FROM token WHERE token = '$value'");
		$df = mysql_num_rows($qdf);
		array_push($tempdf, $df);

	}
	unset($tempdf[0]);


	foreach($tempdf as $key => $value)
	{
		$qn = mysql_query("SELECT id_dok FROM token GROUP BY id_dok");
		$n = mysql_num_rows($qn);
		$idf = log10(($n / $value)+1);
		array_push($tempidf, $idf);

	}
	unset($tempidf[0]);

	foreach ($tempidf as $key => $value)
	{
		$qtf = mysql_query("SELECT tf FROM token WHERE id_token = '$key'");
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
		//$norm = ($value - $min) / ($max - $min);
		$norm = $value/$max ;
		mysql_query("UPDATE token SET df ='$tempdf[$key]' ,idf = '$tempidf[$key]', w = '$value', normalw = '$norm' WHERE id_token = '$key'");

	}


}


?>


</div>

				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>
