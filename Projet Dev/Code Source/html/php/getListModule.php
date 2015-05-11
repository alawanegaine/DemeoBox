<?php
/*--------------------------------------------------------------------------------------------------*/
/*	Author : Repillez Kévin & Chalopin Quentin														*/
/*	Role : Récupération de tous les modules															*/
/*	Parameter : type de module à retourner															*/
/* 	Information : Si paramètre vide, on retourne tous les modules sans exception 					*/
/*	Sortie : Ajout d'une "option" (ligne dans tableList dans JQuery) par module						*/
/*--------------------------------------------------------------------------------------------------*/

	$settings = parse_ini_file("../config/config.ini", TRUE);

	$username = $settings['database']['user'];
	$password = $settings['database']['mdp'];
	$hostname = $settings['server']['URL']; 
	$dbname = $settings['database']['db'];

	//connection to the database
	$dbhandle = mysql_connect($hostname, $username, $password) 
	 or die("Unable to connect to MySQL");
	//echo "Connected to MySQL<br>";

	//select a database to work with
	$selected = mysql_select_db("demeobox",$dbhandle) 
	  or die("Could not select examples");
	//echo "Database selected : $dbname <br>" ;

	$typeModule = $_POST['typeModule'];

	if ($typeModule == "") {
		$sql = "SELECT * FROM Module " ;
	}
	else
	{
		$sql = "SELECT * FROM Module WHERE sonType='".$typeModule."'";
	}
	//echo "SQL : ".$sql."<br>";

	//execute the SQL query and return records
	$result = mysql_query($sql);

	//fetch tha data from the database 
	while ($row = mysql_fetch_array($result)) {
		
		echo "<option value=\"".$row{'id_module'}."\">".$row{'sonType'}." -- ".$row{'saDescription'}."</option>" ;
	}

	//close the connection
	mysql_close($dbhandle);
?>
