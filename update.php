<?php
ini_set('max_execution_time',0);


function update($queryuji)
{
	$que_insert1 = mysql_query("SELECT * FROM bobot WHERE tag = 'uji' AND id_dok = '$queryuji'");
	while($insert1 = mysql_fetch_array($que_insert1))
	{
		$idnyadok = $insert1['id_dok'];
		$kelasnya = $insert1['id_kelas'];
		$tokenya = $insert1['token'];
		$freknya = $insert1['frek'];
		$tfnya = $insert1['tf'];
		$dfnya = $insert1['df'];
		$idfnya = $insert1['idf'];
		$wnya = $insert1['w'];
		$normwnya = $insert1['normalw'];
		
		$queryidplus2 = mysql_query("SELECT id_token FROM token ORDER BY id_token DESC LIMIT 1");
		$getidplus2 = mysql_fetch_array($queryidplus2);
		$next_id = $getidplus2['id_token'] + 1;

		mysql_query("INSERT INTO token(id_token,id_dok,id_kelas,token,tf,df,idf,w,normalw,tag) VALUES('$next_id','$idnyadok','$kelasnya','$tokenya','$tfnya','','','','','latihbaru')");
		
	}

	$temptokenlatih = array("0" => " ");
	$tempdflatih = array("0" => " ");
	$tempidflatih = array("0" => " ");
	$tempwlatih = array("0" => " ");

	$arrayupdate = array("0" => " ");


	$qtokenlatih = mysql_query("SELECT id_token,id_dok,id_kelas,token,tag,tf FROM token");
		
	while ($tl = mysql_fetch_array($qtokenlatih))
	{
		array_push($temptokenlatih, $tl['token']);
		$arrayupdate[$tl['id_token']] = $tl['id_token'].",".$tl['id_dok'].",".$tl['id_kelas'].",".$tl['token'].",".$tl['tag'].",".$tl['tf'];
	}
	unset($temptokenlatih[0]);

	foreach($temptokenlatih as $key => $value)
	{
		$qdflatih = mysql_query("SELECT id_dok FROM token WHERE token = '$value'");
		$dflatih = mysql_num_rows($qdflatih);
		array_push($tempdflatih, $dflatih);

	}
	unset($tempdflatih[0]);


	foreach($tempdflatih as $key => $value)
	{
		$queryn = mysql_query("SELECT id_dok FROM token GROUP BY id_dok");
		$countn = mysql_num_rows($queryn);
		$idflatih = log10(($countn / $value)+1);
		array_push($tempidflatih, $idflatih);
		$arrayupdate[$key] = $arrayupdate[$key].",".$value;

	}
	unset($tempidflatih[0]);

	foreach ($tempidflatih as $key => $value)
	{
		$qtflatih = mysql_query("SELECT tf FROM token WHERE id_token = '$key'");
		$retrievetflatih = mysql_fetch_assoc($qtflatih);
		$tflatih = $retrievetflatih['tf'];
		$tfidflatih = $tflatih * $value;
		array_push($tempwlatih, $tfidflatih);
		$arrayupdate[$key] = $arrayupdate[$key].",".$value;

	}
	unset($tempwlatih[0]);

	foreach ($tempwlatih as $key => $value)
	{
		
		$minlatih = min($tempwlatih);
		$maxlatih = max($tempwlatih);
		$normlatih = $value/$maxlatih;
		$arrayupdate[$key] = $arrayupdate[$key].",".$value.",".$normlatih;

	}

	unset($arrayupdate[0]);
	$tokenresult = fopen("D:/token.txt", "w");
	foreach ($arrayupdate as $key => $value)
	{
		$tokenresult = fopen("D:/token.txt", "a");
		$tokentxt = $value.PHP_EOL;
		fwrite($tokenresult, $tokentxt);
		fclose($tokenresult);
	}
	mysql_query("DELETE FROM token");
	mysql_query("LOAD DATA INFILE 'D:/token.txt' INTO TABLE token FIELDS TERMINATED BY ','");
}
?>