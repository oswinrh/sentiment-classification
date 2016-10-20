<?php
include('koneksi.php');
include('capitalization.php');
ini_set('max_execution_time',0);
?>


<?php

if(!empty($_POST['wordnet2']))
{
	mysql_query("DELETE FROM tokenwordnet");
	$string = mysql_query("SELECT id_pre,isipre,kelas FROM preprocessing");
	$delimiter =" ";
	$idtok=0;
	$tempterm = array();
	$tempstop = array();
	$tempstopnama = array();
	//$temptoken = array();
	$arraylemma = array();

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
		mysql_query("INSERT INTO tokenwordnet(id_token,id_dok,id_kelas,token,tf) VALUES ('$idtok','$idp','$kls','$key','$value')");
	}

	//HAPUS KEY DAN VALUE ARRAY
	foreach ($tempterm as $i => $value) 
	{
		unset($tempterm[$i]);
	}

	foreach ($arraylemma as $i => $value) 
	{
		unset($arraylemma[$i]);
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

	$qtoken = mysql_query("SELECT id_token,token FROM tokenwordnet");
		
	while ($t = mysql_fetch_assoc($qtoken))
	{
		array_push($temptoken, $t['token']);
	}
	unset($temptoken[0]);

	foreach($temptoken as $key => $value)
	{
		$qdf = mysql_query("SELECT id_dok FROM tokenwordnet WHERE token = '$value'");
		$df = mysql_num_rows($qdf);
		array_push($tempdf, $df);

	}
	unset($tempdf[0]);


	foreach($tempdf as $key => $value)
	{
		$qn = mysql_query("SELECT id_dok FROM tokenwordnet GROUP BY id_dok");
		$n = mysql_num_rows($qn);
		$idf = log10(($n / $value)+1);
		array_push($tempidf, $idf);

	}
	unset($tempidf[0]);

	foreach ($tempidf as $key => $value)
	{
		$qtf = mysql_query("SELECT tf FROM tokenwordnet WHERE id_token = '$key'");
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
		mysql_query("UPDATE tokenwordnet SET df ='$tempdf[$key]' ,idf = '$tempidf[$key]', w = '$value', normalw = '$norm' WHERE id_token = '$key'");

	}


}
header("location:coba.php");

?>


</div>

				
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>
