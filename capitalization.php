<?php

	function capitalization($input) {
    //Lower case everything
    $input = strtolower($input);
    //$endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');
    $input = str_replace("-", " ", $input);
    //$input = preg_replace("/[$endash]/", " ", $input);
    //Make alphanumeric (removes all other characters)
    $input = preg_replace("/[^a-z ]/", "", $input);
    //Clean up multiple dashes or whitespaces
    //$input = preg_replace("/[\s-]+/", " ", $input);
    //Convert whitespaces and underscore to dash
    //$input = preg_replace("/[\s_]/", " ", $input);
    // Remove all non-word and non-space chars
    //$input = preg_replace("/[^\sa-z]/", "", $input);
    // Replace enters
    //$input = preg_replace("/[\r\n]/", " ", $input);
    //Trim
    //$input = trim($input);
    return $input;

    //PREPROCESSING : STOPWORD REMOVAL, NAME STOPWORD REMOVAL


//$prepro = mysql_query("INSERT INTO preprocessing (isipre) VALUES($hasil2)");
//echo $hai['isidoklatih'];

// Remove tags
//$input = strip_tags($input);
// Converts accented to non-accented
//$input =  iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
// String to lower
//$input = strtolower($input);
// Remove all non-word and non-space chars
//$input4 = preg_replace('/[^\sa-z]/', '', $input3);
// Replace enters
//$input5 = preg_replace('/[\r\n]/', ' ', $input4);
// Remove stopwords
//$input = preg_replace('/\b(' . implode('|', $stopwords) . ')\b/', '', $input);
// Remove individual chars
//$input6 = preg_replace('/\b([a-z])\b/', '', $input5);
// Trim it
//$input7 = trim($input6);
// Remove multiple spaces
//$input8 = preg_replace("/[[:blank:]]+/", " ", $input7);

//echo $input;
}
?>