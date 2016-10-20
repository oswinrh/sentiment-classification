<?php
include('koneksi.php');
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
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Training<span class="caret"></span></a>
			             		<ul class="dropdown-menu">
			              			<li><a href="addlatih.php">Add New Training Document</a></li>
			              			<li><a href="coba.php">Preprocessing and Weighting</a></li>
			            		</ul>
			            	</li>
			              	<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Testing<span class="caret"></span></a>
			              		<ul class="dropdown-menu">
			              			<li><a href="adduji.php">Add New Test Document</a></li>
			              			<li><a href="wordnetuji.php">Testing Using WordNet</a></li>
			              			<li><a href="pengujianlog.php">Testing Without WordNet</a></li>
			            		</ul>
			              	</li>
			               	<li class="active"><a href="wordnet.php">Thesaurus</a></li>
			            </ul>
	          		</div>
	         	</div>
	        </div>
	      	</nav>

<table class="table">
	<tr>
		<th>Token ID</th>
		<th>Token</th>
		<th>Lemma ID</th>
		<th>Lemma</th>
		<th>Definition</th>
		<th>Synset ID</th>
		<th>Another ID</th>
		<th>Another Token</th>
	</tr>

<?php

//$output = mysql_query("SELECT * FROM token, words, senses, synsets WHERE token.token = words.lemma AND words.wordid = senses.wordid AND senses.synsetid = synsets.synsetid");
$token = mysql_query("SELECT id_token,token,wordid,lemma FROM token,words WHERE token=lemma");
while($hasiltoken = mysql_fetch_array($token))
{
	$idtok = $hasiltoken['id_token'];
	$tok = $hasiltoken['token'];
	$wordid = $hasiltoken['wordid'];
	$lemma = $hasiltoken['lemma'];

	//echo "Token : ".$idtok." ".$tok."<br>Lemma : ".$wordid." ".$lemma." ";

	?>
	<tr>
		<td><?php echo $idtok; ?></td>
		<td><?php echo $tok; ?></td>
		<td><?php echo $wordid; ?></td>
		<td><?php echo $lemma; ?></td>
	<?php

	$que_seek_synsetid = mysql_query("SELECT * FROM senses WHERE wordid = '$wordid' ORDER BY tagcount DESC LIMIT 1");
	while($fetch_synsetid = mysql_fetch_array($que_seek_synsetid))
	{
		$synsetid = $fetch_synsetid['synsetid'];
		//echo "<br>Synset ID :".$synsetid." ";
		$que_def = mysql_query("SELECT * FROM synsets WHERE synsetid = '$synsetid'");
		while($fetchdef = mysql_fetch_array($que_def))
		{
			$definition = $fetchdef['definition'];
			?>
			<td><?php echo $definition; ?></td>
			<?php

		}
		?>
			<td><?php echo $synsetid; ?></td>
		<?php	
	}
	

	$que_seek_other_wordid_same_synsetid = mysql_query("SELECT s.wordid,w.lemma FROM senses s,words w WHERE s.synsetid = '$synsetid' AND w.wordid=s.wordid ORDER BY s.tagcount DESC LIMIT 1");
	while ($fetch_seek_other_wordid_same_synsetid = mysql_fetch_array($que_seek_other_wordid_same_synsetid)) {
		$other_wordid = $fetch_seek_other_wordid_same_synsetid['wordid'];
		$other_lemma = $fetch_seek_other_wordid_same_synsetid['lemma'];
		
		if($wordid != $other_wordid)
		{
			$tok = $other_lemma;
			//echo "<br><br><b>Replace into : </b>".$other_wordid." | ".$other_lemma;
			?>
				<td><?php echo $other_wordid; ?></td>
				<td><?php echo $other_lemma; ?></td>
			</tr>
			<?php
		}
		else
		{
			$tok = $tok;
		}
	}

}

?>

</table>

</div>

				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>

