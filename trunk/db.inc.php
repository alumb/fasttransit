<?php


//opens a connection to the database server and selectes the correct database
function connect() {
	$host = "localhost";
	$dbname = "transit"; //name of the database you wish to query
	$user = "transit";
	$password = "transit";
	// Connecting, selecting database
	$link = mysql_pconnect($host, $user, $password) or die('Could not connect: ' . mysql_error());
	mysql_select_db($dbname) or die('Could not select database: '.$this->dbname);
}

connect();

//queries the database and returns an array of the results.
function query($queryString) {
	$resultset = mysql_query($queryString) or die('Query Failed: ' . mysql_error());
	$resultarray = array();
	while ($resultarray[] = mysql_fetch_assoc($resultset)) {}
	unset($resultarray[count($resultarray)-1]); // this is to remove the last blank record.
	return $resultarray;
}

//queries the database and returns an array indexed by the given field
function queryRBI($queryString, $index) {
	$resultset = mysql_query($queryString) or die('Query Failed: ' . mysql_error());	
	$resultarray = array();
	while ($row = mysql_fetch_assoc($resultset)) {
			$resultarray[$row[$index]] = $row;
	}
	return $resultarray;
}

//this function returns only the first record in the query
function querySingleRecord($queryString) {
	$resultset = mysql_query($queryString) or die('Query Failed: ' . mysql_error());
	return mysql_fetch_assoc($resultset);
}

//this function runs a query that doesn't return a record set.
function queryNR($queryString) {
//	echo $queryString;
	return mysql_query($queryString) or die('Query Failed: ' . mysql_error());
}


?>
